<?php

namespace BestWishes\Tests\Controller;

use BestWishes\Entity\Category;
use BestWishes\Entity\Gift;
use BestWishes\Entity\GiftList;
use BestWishes\Entity\GiftListPermission;
use BestWishes\Entity\User;
use BestWishes\Kernel;
use BestWishes\Message\CreationAlertMessage;
use BestWishes\MessageHandler\CreationAlertMessageHandler;
use BestWishes\Repository\GiftListPermissionRepository;
use BestWishes\Repository\GiftRepository;
use BestWishes\Security\PermissionManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;

final class GiftListSecurityTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private string $pdfHtml = '';
    private ?string $databaseFile = null;
    private ?string $previousDatabaseUrl = null;
    private GiftList $list;
    private Category $category;
    private Gift $ordinary;
    private Gift $surprise;
    /** @var array<string, User> */
    private array $users = [];

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected function setUp(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('The functional security tests require PDO SQLite.');
        }

        $databaseFile = tempnam(sys_get_temp_dir(), 'bestwishes-test-');
        self::assertIsString($databaseFile);
        $this->databaseFile = $databaseFile;
        $this->previousDatabaseUrl = $_ENV['DATABASE_URL'] ?? null;
        $_ENV['DATABASE_URL'] = 'sqlite:///' . $databaseFile;

        $this->client = static::createClient();
        $this->client->disableReboot();
        $container = static::getContainer();
        $container->set('assets.packages', new Packages(new Package(new EmptyVersionStrategy())));
        $container->set('webpack_encore.entrypoint_lookup[_default]', $this->createStub(EntrypointLookupInterface::class));
        $container->set('http_client', new MockHttpClient(function (string $method, string $url, array $options): MockResponse {
            $body = $options['body'];
            self::assertInstanceOf(\Closure::class, $body);
            while (($chunk = $body(8192)) !== '') {
                $this->pdfHtml .= $chunk;
            }

            return new MockResponse('%PDF-test');
        }));

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->entityManager->getConfiguration()->setUseSchemaEditorApi(true);
        (new SchemaTool($this->entityManager))->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());

        foreach (['owner', 'guest-account', 'editor', 'buyer', 'admin'] as $username) {
            $user = new User();
            $user->setUserIdentifier($username);
            $user->setName($username);
            $user->setEmail($username . '@example.test');
            $user->setPassword('unused');
            $user->setRoles($username === 'admin' ? ['ROLE_ADMIN'] : []);
            $this->users[$username] = $user;
            $this->entityManager->persist($user);
        }

        $this->list = new GiftList();
        $this->list->setName('Public wishlist');
        $this->list->setSlug('public-wishlist');
        $this->list->setBirthDate(new \DateTimeImmutable('2000-04-05'));
        $this->list->setOwner($this->users['owner']);
        $this->entityManager->persist($this->list);

        $this->category = new Category();
        $this->category->setName('Books');
        $this->category->setList($this->list);
        $this->entityManager->persist($this->category);

        $this->ordinary = new Gift(false, $this->category);
        $this->ordinary->setName('Public Gift');
        $this->surprise = new Gift(true, $this->category);
        $this->surprise->setName('Secret Gift');
        $this->surprise->setMoreDetail('Secret gift description');
        $this->entityManager->persist($this->ordinary);
        $this->entityManager->persist($this->surprise);
        $this->entityManager->flush();

        $permissions = $container->get(PermissionManager::class);
        $permissions->grant($this->list, $this->users['editor'], GiftListPermission::PERMISSION_EDIT);
        $permissions->grant($this->list, $this->users['buyer'], GiftListPermission::PERMISSION_SURPRISE_ADD);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if ($this->databaseFile === null) {
            return;
        }

        unlink($this->databaseFile);
        if ($this->previousDatabaseUrl === null) {
            unset($_ENV['DATABASE_URL']);
        } else {
            $_ENV['DATABASE_URL'] = $this->previousDatabaseUrl;
        }
    }

    /** @return iterable<string, array{?string, bool}> */
    public static function surpriseViewers(): iterable
    {
        yield 'anonymous' => [null, false];
        yield 'owner' => ['owner', false];
        yield 'unrelated account' => ['guest-account', false];
        yield 'editor' => ['editor', true];
        yield 'buyer' => ['buyer', true];
        yield 'admin' => ['admin', true];
    }

    #[DataProvider('surpriseViewers')]
    public function testSurpriseVisibilityAcrossPublicRoutes(?string $username, bool $canSeeSurprise): void
    {
        if ($username !== null) {
            $this->client->loginUser($this->users[$username]);
        }

        $this->get('list_index');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Public wishlist', (string) $this->client->getResponse()->getContent());

        $this->get('list_show', $this->list->getId());
        self::assertResponseIsSuccessful();

        $this->get('gift_show', $this->ordinary->getId());
        self::assertResponseIsSuccessful();

        $this->get('category_show', $this->category->getId());
        self::assertResponseIsSuccessful();
        $category = (string) $this->client->getResponse()->getContent();
        self::assertStringContainsString('Public Gift', $category);
        self::assertSame($canSeeSurprise, str_contains($category, 'Secret Gift'));

        $this->get('gift_show', $this->surprise->getId());
        self::assertResponseStatusCodeSame($canSeeSurprise ? 200 : 404);

        $this->get('list_export_pdf', $this->list->getId());
        self::assertResponseIsSuccessful();
        self::assertTrue($this->client->getResponse()->headers->hasCacheControlDirective('no-store'));
        self::assertStringContainsString('Public Gift', $this->pdfHtml);
        self::assertSame($canSeeSurprise, str_contains($this->pdfHtml, 'Secret Gift'));
        self::assertSame($canSeeSurprise, str_contains($this->pdfHtml, 'Secret gift description'));
    }

    public function testAdminCannotReadSurprisesOnTheirOwnList(): void
    {
        $this->list->setOwner($this->users['admin']);
        $this->entityManager->flush();
        $this->client->loginUser($this->users['admin']);

        $this->get('gift_show', $this->surprise->getId());
        self::assertResponseStatusCodeSame(404);
    }

    public function testOwnerCanEditOrdinaryGiftButCannotEditSurprise(): void
    {
        $this->client->loginUser($this->users['owner']);
        $this->get('gift_edit', $this->ordinary->getId());
        self::assertResponseIsSuccessful();
        $this->get('gift_edit', $this->surprise->getId());
        self::assertResponseStatusCodeSame(403);
    }

    public function testOrdinaryAccountCannotChangeListPermissions(): void
    {
        $this->client->loginUser($this->users['guest-account']);
        $this->post('admin_update_list_perm', $this->list->getId(), [
            'userId' => $this->users['guest-account']->getId(),
            'perm' => GiftListPermission::PERMISSION_EDIT,
        ]);
        self::assertResponseStatusCodeSame(403);
        self::assertFalse($this->hasPermission('guest-account', GiftListPermission::PERMISSION_EDIT));
    }

    public function testAdminNeedsCsrfTokenAndCanGrantOnlyListedPermissions(): void
    {
        $this->client->loginUser($this->users['admin']);
        $payload = ['userId' => $this->users['guest-account']->getId(), 'perm' => GiftListPermission::PERMISSION_EDIT];
        $this->post('admin_update_list_perm', $this->list->getId(), $payload);
        self::assertResponseStatusCodeSame(403);
        self::assertFalse($this->hasPermission('guest-account', GiftListPermission::PERMISSION_EDIT));

        $this->get('admin_lists_rights');
        self::assertResponseIsSuccessful();
        $payload['_token'] = $this->client->getCrawler()->filter('[data-csrf-token]')->attr('data-csrf-token');
        $this->post('admin_update_list_perm', $this->list->getId(), $payload);
        self::assertResponseIsSuccessful();
        self::assertTrue($this->hasPermission('guest-account', GiftListPermission::PERMISSION_EDIT));

        foreach (['OWNER', 'ROLE_ADMIN'] as $invalidPermission) {
            $payload['perm'] = $invalidPermission;
            $this->post('admin_update_list_perm', $this->list->getId(), $payload);
            $result = json_decode((string) $this->client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
            self::assertFalse($result['success']);
            self::assertFalse($this->hasPermission('guest-account', $invalidPermission));
        }
    }

    public function testAlertChangesRequireCsrfAndOnlyAffectCurrentUser(): void
    {
        $this->client->loginUser($this->users['guest-account']);
        $payload = ['alert' => GiftListPermission::PERMISSION_ALERT_ADD, 'userId' => $this->users['owner']->getId()];
        $this->post('user_update_list_alert', $this->list->getId(), $payload);
        self::assertResponseStatusCodeSame(403);

        $this->get('user_manage_alerts');
        self::assertResponseIsSuccessful();
        $payload['_token'] = $this->client->getCrawler()->filter('[data-csrf-token]')->attr('data-csrf-token');
        $this->post('user_update_list_alert', $this->list->getId(), $payload);
        self::assertResponseIsSuccessful();
        self::assertTrue($this->hasPermission('guest-account', GiftListPermission::PERMISSION_ALERT_ADD));
        self::assertFalse($this->hasPermission('owner', GiftListPermission::PERMISSION_ALERT_ADD));
    }

    public function testSurpriseAlertWorkerRechecksRecipientRightsWithoutASession(): void
    {
        $container = static::getContainer();
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->with(self::callback(
            static fn (TemplatedEmail $email): bool => $email->getTo()[0]->getAddress() === 'buyer@example.test'
        ));
        $container->set('mailer.mailer', $mailer);
        $handler = $container->get(CreationAlertMessageHandler::class);

        foreach (['owner', 'guest-account', 'buyer'] as $recipient) {
            $handler(new CreationAlertMessage(
                (int) $this->users[$recipient]->getId(),
                (int) $this->surprise->getId(),
                (int) $this->users['editor']->getId()
            ));
        }

        $container->get(PermissionManager::class)
            ->revoke($this->list, $this->users['buyer'], GiftListPermission::PERMISSION_SURPRISE_ADD);
        $handler(new CreationAlertMessage(
            (int) $this->users['buyer']->getId(),
            (int) $this->surprise->getId(),
            (int) $this->users['editor']->getId()
        ));
    }

    public function testResetEmailUsesCanonicalUrlBeforeDelivery(): void
    {
        $previousUri = $_ENV['DEFAULT_URI'] ?? null;
        $_ENV['DEFAULT_URI'] = 'https://wishlist.example.test/installation';
        try {
            $mailer = $this->createMock(MailerInterface::class);
            $mailer->expects(self::once())->method('send')->with(self::callback(static function (TemplatedEmail $email): bool {
                $context = $email->getContext();

                return $context['siteUrl'] === 'https://wishlist.example.test/installation/'
                    && str_starts_with($context['resetTokenLink'], 'https://wishlist.example.test/installation/reset-password/reset/');
            }));
            static::getContainer()->set('mailer.mailer', $mailer);

            $crawler = $this->client->request('GET', 'https://alias.example.test/reset-password');
            self::assertResponseIsSuccessful();
            $form = $crawler->filter('form')->form(['reset_password_request_form[email]' => 'owner@example.test']);
            $this->client->setServerParameter('HTTP_ORIGIN', 'https://alias.example.test');
            $this->client->submit($form);
            self::assertResponseRedirects('/reset-password/check-email');
        } finally {
            if ($previousUri === null) {
                unset($_ENV['DEFAULT_URI']);
            } else {
                $_ENV['DEFAULT_URI'] = $previousUri;
            }
        }
    }

    public function testPurchaseDoesNotOverwriteAnExistingBuyer(): void
    {
        $repository = static::getContainer()->get(GiftRepository::class);
        self::assertInstanceOf(Gift::class, $repository->recordPurchase($this->ordinary, $this->users['buyer'], 'First purchase'));
        self::assertNull($repository->recordPurchase($this->ordinary, $this->users['admin'], 'Second purchase'));

        $this->entityManager->clear();
        $gift = $repository->find($this->ordinary->getId());
        self::assertNotNull($gift);
        self::assertSame('buyer', $gift->getBuyer()?->getUserIdentifier());
        self::assertSame('First purchase', $gift->getPurchaseComment());
    }

    private function hasPermission(string $username, string $permission): bool
    {
        return static::getContainer()->get(GiftListPermissionRepository::class)
            ->hasPermission($this->list, $this->users[$username], $permission);
    }

    private function get(string $route, ?int $id = null): void
    {
        $this->client->request('GET', $this->route($route, $id));
    }

    /** @param array<string, mixed> $data */
    private function post(string $route, ?int $id, array $data): void
    {
        $this->client->request('POST', $this->route($route, $id), $data);
    }

    private function route(string $route, ?int $id): string
    {
        return static::getContainer()->get(UrlGeneratorInterface::class)->generate($route, $id === null ? [] : ['id' => $id]);
    }
}
