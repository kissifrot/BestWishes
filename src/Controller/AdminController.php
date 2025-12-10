<?php

namespace BestWishes\Controller;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\GiftListPermission;
use BestWishes\Entity\User;
use BestWishes\Event\GiftListCreatedEvent;
use BestWishes\Form\Type\GiftListType;
use BestWishes\Form\Type\UserType;
use BestWishes\Manager\UserManager;
use BestWishes\Security\PermissionManager;
use BestWishes\Security\Core\BestWishesSecurityContext;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private const array AVAILABLE_PERMISSIONS = ['EDIT', 'SURPRISE_ADD', 'ALERT_ADD', 'ALERT_PURCHASE', 'ALERT_EDIT', 'ALERT_DELETE'];
    public function __construct(
        private readonly EntityManagerInterface    $entityManager,
        private readonly BestWishesSecurityContext $securityContext,
        private readonly PermissionManager         $permissionManager,
        private readonly EventDispatcherInterface  $eventDispatcher,
        private readonly UserManager               $userManager
    ) {
    }

    #[Route(path: '/lists', name: 'admin_lists')]
    public function lists(): Response
    {
        $lists = $this->entityManager->getRepository(GiftList::class)->findAll();

        $deleteForm = $this->createSimpleActionForm(new GiftList(), 'delete')->createView();

        return $this->render('admin/lists.html.twig', compact('lists', 'deleteForm'));
    }

    #[Route(path: '/lists/rights', name: 'admin_lists_rights')]
    public function listsRights(): Response
    {
        $lists = $this->entityManager->getRepository(GiftList::class)->findAll();
        $users = $this->userManager->findUsers();
        $availablePermissions = [
            GiftListPermission::PERMISSION_OWNER,
            GiftListPermission::PERMISSION_EDIT,
            GiftListPermission::PERMISSION_SURPRISE_ADD,
            GiftListPermission::PERMISSION_ALERT_ADD,
            GiftListPermission::PERMISSION_ALERT_PURCHASE,
            GiftListPermission::PERMISSION_ALERT_EDIT,
            GiftListPermission::PERMISSION_ALERT_DELETE,
        ];

        return $this->render(
            'admin/lists_rights.html.twig',
            compact('lists', 'users', 'availablePermissions')
        );
    }

    #[Route(path: '/list/{id}/updatePerm', name: 'admin_update_list_perm', requirements: ['id' => '\d+'], options: ['expose' => true], methods: ['POST'])]
    public function updatePermission(Request $request, GiftList $giftList): JsonResponse
    {
        $defaultData = [
            'success'  => false,
            'message' => 'An error occurred',
        ];
        $userId = $request->request->getInt('userId');
        $perm = $request->request->get('perm');
        // Do some input checks
        if (empty($perm) || empty($userId)) {
            new JsonResponse($defaultData);
        }
        if (!\in_array($perm, self::AVAILABLE_PERMISSIONS, true)) {
            return new JsonResponse($defaultData);
        }
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            return new JsonResponse(array_merge($defaultData, ['message' => 'User could not be found']));
        }

        if ($this->securityContext->isGranted($perm, $giftList, $user)) {
            // Remove the specified permission
            $this->permissionManager->revoke($giftList, $user, $perm);
        } else {
            // Add the specified permission
            $this->permissionManager->grant($giftList, $user, $perm);
        }

        $successData = [
            'success'  => true,
            'message' => \sprintf('Permission updated for "%s"', $giftList->getName()),
        ];

        return new JsonResponse($successData);
    }

    #[Route(path: '/list/{id}', name: 'admin_list_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function listDelete(Request $request, GiftList $giftList): Response
    {
        $form = $this->createSimpleActionForm($giftList, 'delete');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($giftList);
            $this->entityManager->flush();

            $this->addFlash('notice', \sprintf('List "%s" deleted', $giftList->getName()));
        } else {
            $this->addFlash('error', \sprintf('Could not delete list "%s"', $giftList->getName()));
        }

        return $this->redirectToRoute('admin_lists');
    }

    #[Route(path: '/list/create', name: 'admin_list_create', methods: ['GET', 'POST'])]
    public function listCreate(Request $request): Response
    {
        $giftList = new GiftList();

        $form = $this->createForm(GiftListType::class, $giftList);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($giftList);
            try {
                $this->entityManager->flush();

                // Grant owner permission
                $this->permissionManager->grant($giftList, $giftList->getOwner(), GiftListPermission::PERMISSION_OWNER);

                $this->addFlash('notice', \sprintf('List "%s" created', $giftList->getName()));

                // Dispatch the creation event
                $this->eventDispatcher->dispatch(new GiftListCreatedEvent(), GiftListCreatedEvent::NAME);

                return $this->redirectToRoute('admin_lists');
            } catch (\Exception $e) {
                $this->addFlash('error', \sprintf('Error creating "%s": %s', $giftList->getName(), $e->getMessage()));
            }
        }

        return $this->render(
            'admin/list_create.html.twig',
            ['form' => $form->createView()]
        );
    }

    #[Route(path: '/list/{id}/edit', name: 'admin_list_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function listEdit(Request $request, GiftList $giftList): Response
    {
        $originGiftList = clone $giftList;
        $form = $this->createForm(GiftListType::class, $giftList);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($giftList);
            try {
                $this->entityManager->flush();

                if ($originGiftList->getOwner() !== $giftList->getOwner()) {
                    // Exchange permissions
                    $this->permissionManager->exchangePerms(
                        $giftList,
                        $giftList->getOwner(),
                        $originGiftList->getOwner(),
                        GiftListPermission::PERMISSION_OWNER
                    );
                }
                $this->addFlash('notice', \sprintf('List "%s" updated', $giftList->getName()));

                return $this->redirectToRoute('admin_lists');
            } catch (\Exception $e) {
                $this->addFlash('error', \sprintf('Error editing "%s": %s', $giftList->getName(), $e->getMessage()));
            }
        }

        return $this->render(
            'admin/list_edit.html.twig',
            ['form' => $form->createView(), 'list' => $giftList]
        );
    }

    /**
     * Creates a form for simple actions
     *
     * @param string $action Chosen action
     */
    private function createSimpleActionForm(mixed $entity, string $action = 'delete'): FormInterface
    {
        $routePart = match ($entity::class) {
            GiftList::class => 'list',
            User::class => 'user',
            default => throw new \RuntimeException(\sprintf('The "%s" type is not supported', $entity::class)),
        };
        switch ($action) {
            case 'delete':
                $method = 'DELETE';
                $url = $this->generateUrl(
                    'admin_' . $routePart . '_delete',
                    ['id' => !empty($entity->getId()) ? $entity->getId() : 99_999_999_999_999]
                );
                break;
            default:
                throw new \RuntimeException(\sprintf('The "%s" action is not supported', $action));
        }

        return $this->createFormBuilder()
            ->setAction($url)
            ->setMethod($method)
            ->getForm();
    }

    #[Route(path: '/users', name: 'admin_users')]
    public function users(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $deleteForm = $this->createSimpleActionForm(new User(), 'delete')->createView();

        return $this->render('admin/users.html.twig', compact('users', 'deleteForm'));
    }

    #[Route(path: '/user/{id}', name: 'admin_user_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function userDelete(Request $request, User $user): Response
    {
        $form = $this->createSimpleActionForm($user, 'delete');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->addFlash('notice', \sprintf('User "%s" deleted', $user->getName()));
        } else {
            $this->addFlash('error', \sprintf('Could not delete user "%s"', $user->getName()));
        }

        return $this->redirectToRoute('admin_users');
    }

    #[Route(path: '/user/create', name: 'admin_user_create', methods: ['GET', 'POST'])]
    public function userCreate(Request $request): Response
    {
        $user = $this->userManager->createUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updatePassword($user, $form->get('plainPassword')->getData());
            $this->entityManager->persist($user);
            try {
                $this->entityManager->flush();

                /** @var GiftList|null $chosenList */
                $chosenList = $user->getList();
                if (null !== $chosenList && $chosenList->getOwner()->getId() !== $user->getId()) {
                    // Exchange permissions
                    $this->permissionManager->exchangePerms(
                        $chosenList,
                        $user,
                        $chosenList->getOwner(),
                        GiftListPermission::PERMISSION_OWNER
                    );
                }

                $this->addFlash('notice', \sprintf('User "%s" created', $user->getUserIdentifier()));

                return $this->redirectToRoute('admin_users');
            } catch (\Exception $e) {
                $this->addFlash('error', \sprintf('Error creating "%s": %s', $user->getUserIdentifier(), $e->getMessage()));
            }
        }

        return $this->render('admin/user_create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/user/{id}/edit', name: 'admin_user_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function userEdit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, ['isEditing' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updatePassword($user, $form->get('plainPassword')->getData());
            $this->entityManager->persist($user);
            try {
                $this->entityManager->flush();

                /** @var GiftList|null $chosenList */
                $chosenList = $user->getList();
                if (null !== $chosenList && $chosenList->getOwner()->getId() !== $user->getId()) {
                    // Exchange permissions
                    $this->permissionManager->exchangePerms(
                        $chosenList,
                        $user,
                        $chosenList->getOwner(),
                        GiftListPermission::PERMISSION_OWNER
                    );
                }

                $this->addFlash('notice', \sprintf('User "%s" updated', $user->getUserIdentifier()));

                return $this->redirectToRoute('admin_users');
            } catch (\Exception $e) {
                $this->addFlash('error', \sprintf('Error editing "%s": %s', $user->getUserIdentifier(), $e->getMessage()));
            }
        }

        return $this->render('admin/user_edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
