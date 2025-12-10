<?php

namespace BestWishes\Tests\Twig\Extension;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Security\Core\BestWishesSecurityContext;
use BestWishes\Twig\Extension\BestWishesSecurityExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\TwigFunction;

class BestWishesSecurityExtensionTest extends TestCase
{
    private BestWishesSecurityContext&MockObject $securityContext;
    private AuthorizationCheckerInterface&MockObject $authorizationChecker;
    private BestWishesSecurityExtension $extension;

    protected function setUp(): void
    {
        $this->securityContext = $this->createMock(BestWishesSecurityContext::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->extension = new BestWishesSecurityExtension(
            $this->securityContext,
            $this->authorizationChecker
        );
    }

    public function testIsUserGrantedWithPermission(): void
    {
        $user = $this->createMock(User::class);
        $giftList = $this->createMock(GiftList::class);

        $this->securityContext
            ->expects($this->once())
            ->method('isGranted')
            ->with('EDIT', $giftList, $user)
            ->willReturn(true);

        $result = $this->extension->isUserGranted('EDIT', $giftList, $user);

        $this->assertTrue($result);
    }

    public function testIsUserGrantedWithoutPermission(): void
    {
        $user = $this->createMock(User::class);
        $giftList = $this->createMock(GiftList::class);

        $this->securityContext
            ->expects($this->once())
            ->method('isGranted')
            ->with('DELETE', $giftList, $user)
            ->willReturn(false);

        $result = $this->extension->isUserGranted('DELETE', $giftList, $user);

        $this->assertFalse($result);
    }

    public function testIsMultiGrantedWithSingleRole(): void
    {
        $object = new \stdClass();

        $this->authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN', $object)
            ->willReturn(true);

        $result = $this->extension->isMultiGranted('ROLE_ADMIN', $object);

        $this->assertTrue($result);
    }

    public function testIsMultiGrantedWithMultipleRoles(): void
    {
        $object = new \stdClass();

        $this->authorizationChecker
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->willReturnCallback(function ($role) {
                return $role === 'ROLE_USER';
            });

        $result = $this->extension->isMultiGranted(['ROLE_ADMIN', 'ROLE_USER'], $object);

        $this->assertTrue($result);
    }

    public function testIsMultiGrantedWithNoMatchingRoles(): void
    {
        $object = new \stdClass();

        $this->authorizationChecker
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->willReturn(false);

        $result = $this->extension->isMultiGranted(['ROLE_ADMIN', 'ROLE_USER'], $object);

        $this->assertFalse($result);
    }

    public function testIsMultiGrantedWithNullChecker(): void
    {
        $extension = new BestWishesSecurityExtension($this->securityContext, null);

        $result = $extension->isMultiGranted('ROLE_ADMIN');

        $this->assertFalse($result);
    }

    public function testIsMultiGrantedHandlesAuthenticationException(): void
    {
        $this->authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->willThrowException(new AuthenticationCredentialsNotFoundException());

        $result = $this->extension->isMultiGranted('ROLE_ADMIN');

        $this->assertFalse($result);
    }

    public function testIsMultiGrantedWithoutObject(): void
    {
        $this->authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN', null)
            ->willReturn(true);

        $result = $this->extension->isMultiGranted('ROLE_ADMIN');

        $this->assertTrue($result);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);

        $functionNames = array_map(fn(TwigFunction $f) => $f->getName(), $functions);
        $this->assertContains('is_multi_granted', $functionNames);
        $this->assertContains('is_user_granted', $functionNames);
    }

    public function testIsUserGrantedWithAlertPermission(): void
    {
        $user = $this->createMock(User::class);
        $giftList = $this->createMock(GiftList::class);

        $this->securityContext
            ->expects($this->once())
            ->method('isGranted')
            ->with('ALERT_ADD', $giftList, $user)
            ->willReturn(true);

        $result = $this->extension->isUserGranted('ALERT_ADD', $giftList, $user);

        $this->assertTrue($result);
    }

    public function testIsMultiGrantedStopsOnFirstMatch(): void
    {
        $object = new \stdClass();

        // Should stop after first match
        $this->authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN', $object)
            ->willReturn(true);

        $result = $this->extension->isMultiGranted(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_GUEST'], $object);

        $this->assertTrue($result);
    }
}
