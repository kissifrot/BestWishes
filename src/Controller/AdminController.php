<?php

namespace BestWishes\Controller;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Event\GiftListCreatedEvent;
use BestWishes\Form\Type\GiftListType;
use BestWishes\Form\Type\UserType;
use BestWishes\Manager\UserManager;
use BestWishes\Security\Acl\Permissions\BestWishesMaskBuilder;
use BestWishes\Security\AclManager;
use BestWishes\Security\Core\BestWishesSecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    private $securityContext;
    private $aclManager;
    private $eventDispatcher;
    private $userManager;

    public function __construct(BestWishesSecurityContext $securityContext, AclManager $aclManager, EventDispatcherInterface $eventDispatcher, UserManager $userManager)
    {
        $this->securityContext = $securityContext;
        $this->aclManager = $aclManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/lists", name="admin_lists")
     */
    public function lists(): Response
    {
        $lists = $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findAll();

        $deleteForm = $this->createSimpleActionForm(new GiftList(), 'delete')->createView();

        return $this->render('admin/lists.html.twig', compact('lists', 'deleteForm'));
    }

    /**
     * @Route("/lists/rights", name="admin_lists_rights")
     */
    public function listsRights(): Response
    {
        $lists = $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findAll();
        $users = $this->userManager->findUsers();
        $availablePermissions = [
            'OWNER',
            'EDIT',
            'SURPRISE_ADD',
            'ALERT_ADD',
            'ALERT_PURCHASE',
            'ALERT_EDIT',
            'ALERT_DELETE'
        ];

        return $this->render('admin/lists_rights.html.twig',
            compact('lists', 'users', 'availablePermissions'));
    }

    /**
     * @Route("/list/{id}/updatePerm", name="admin_update_list_perm", requirements={"id": "\d+"}, options = { "expose" = true }, methods={"POST"})
     * @param Request  $request
     * @param GiftList $giftList
     * @return JsonResponse
     */
    public function updatePermission(Request $request, GiftList $giftList): JsonResponse
    {
        $defaultData = [
            'success'  => false,
            'message' => 'An error occurred'
        ];
        $userId = $request->request->getInt('userId');
        $perm = $request->request->get('perm');
        // Do some input checks
        if (empty($perm) || empty($userId)) {
            new JsonResponse($defaultData);
        }
        $availablePermissions = ['EDIT', 'SURPRISE_ADD', 'ALERT_ADD', 'ALERT_PURCHASE', 'ALERT_EDIT', 'ALERT_DELETE'];
        if (!\in_array($perm, $availablePermissions, true)) {
            return new JsonResponse($defaultData);
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        if (!$user) {
            return new JsonResponse(array_merge($defaultData, ['message' => 'User could not be found']));
        }

        // Build the mask to update
        $permMask = \constant('BestWishes\Security\Acl\Permissions\BestWishesMaskBuilder::MASK_' . $perm);
        $hadPerm = $this->securityContext->isGranted($perm, $giftList, $user);

        if ($hadPerm) {
            // Remove the specified permission
            $this->aclManager->revoke($giftList, $user, $permMask);
        } else {
            // Add the specified permission
            $this->aclManager->grant($giftList, $user, $permMask);
        }

        $successData = [
            'success'  => true,
            'message' => sprintf('Permission updated for "%s"', $giftList->getName())
        ];

        return new JsonResponse($successData);
    }

    /**
     * @param Request  $request
     * @param GiftList $giftList
     * @Route("/list/{id}", name="admin_list_delete", requirements={"id": "\d+"}, methods={"DELETE"})
     *
     * @return Response
     */
    public function listDelete(Request $request, GiftList $giftList): Response
    {
        $form = $this->createSimpleActionForm($giftList, 'delete');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($giftList);
            $em->flush();

            $this->addFlash('notice', sprintf('List "%s" deleted', $giftList->getName()));
        } else {
            $this->addFlash('error', sprintf('Could not delete list "%s"', $giftList->getName()));
        }

        return $this->redirectToRoute('admin_lists');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/list/create", name="admin_list_create", methods={"GET", "POST"})
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function listCreate(Request $request): Response
    {
        $giftList = new GiftList();

        $form = $this->createForm(GiftListType::class, $giftList);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($giftList);
            try {
                $em->flush();

                // Add the correct ACL for the owner
                $permMask = BestWishesMaskBuilder::MASK_OWNER;
                $this->aclManager->grant($giftList, $giftList->getOwner(), $permMask);

                $this->addFlash('notice', sprintf('List "%s" created', $giftList->getName()));

                // Dispatch the creation event
                $this->eventDispatcher->dispatch(GiftListCreatedEvent::NAME, new GiftListCreatedEvent());

                return $this->redirectToRoute('admin_lists');
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('Error creating "%s": %s', $giftList->getName(), $e->getMessage()));
            }
        }

        return $this->render('admin/list_create.html.twig',
            ['form' => $form->createView()]);
    }

    /**
     * @param Request  $request
     * @param GiftList $giftList
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @Route("/list/{id}/edit", name="admin_list_edit", requirements={"id": "\d+"}, methods={"GET", "POST"})
     */
    public function listEdit(Request $request, GiftList $giftList): Response
    {
        $originGiftList = clone $giftList;
        $form = $this->createForm(GiftListType::class, $giftList);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($giftList);
            try {
                $em->flush();

                if ($originGiftList->getOwner() !== $giftList->getOwner()) {
                    // Exchange permissions
                    $this->aclManager->exchangePerms(
                        $giftList,
                        $giftList->getOwner(),
                        $originGiftList->getOwner(),
                        BestWishesMaskBuilder::MASK_OWNER
                    );
                }
                $this->addFlash('notice', sprintf('List "%s" updated', $giftList->getName()));

                return $this->redirectToRoute('admin_lists');
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('Error editing "%s": %s', $giftList->getName(), $e->getMessage()));
            }
        }

        return $this->render('admin/list_edit.html.twig',
            ['form' => $form->createView(), 'list' => $giftList]);
    }

    /**
     * Creates a form for simple actions
     *
     * @param mixed  $entity
     * @param string $action Chosen action
     *
     * @return FormInterface|RedirectResponse Delete form or redirect
     */
    private function createSimpleActionForm($entity, $action = 'delete')
    {
        switch (\get_class($entity)) {
            case GiftList::class:
                $routePart = 'list';
                break;
            case User::class:
                $routePart = 'user';
                break;
            default:
                throw new \RuntimeException(sprintf('The "%s" type is not supported', \get_class($entity)));
                break;
        }
        switch ($action) {
            case 'delete':
                $method = 'DELETE';
                $url = $this->generateUrl('admin_' . $routePart . '_delete',
                    ['id' => !empty($entity->getId()) ? $entity->getId() : 99999999999999]);
                break;
            default:
                throw new \RuntimeException(sprintf('The "%s" action is not supported', $action));
        }

        return $this->createFormBuilder()
            ->setAction($url)
            ->setMethod($method)
            ->getForm();
    }

    /**
     * @Route("/users", name="admin_users")
     * @return Response
     */
    public function users(): Response
    {
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();

        $deleteForm = $this->createSimpleActionForm(new User(), 'delete')->createView();

        return $this->render('admin/users.html.twig', compact('users', 'deleteForm'));
    }

    /**
     * @param Request  $request
     * @param User $user
     * @Route("/user/{id}", name="admin_user_delete", requirements={"id": "\d+"}, methods={"DELETE"})
     *
     * @return Response
     */
    public function userDelete(Request $request, User $user): Response
    {
        $form = $this->createSimpleActionForm($user, 'delete');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('notice', sprintf('User "%s" deleted', $user->getName()));
        } else {
            $this->addFlash('error', sprintf('Could not delete user "%s"', $user->getName()));
        }

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @Route("/user/create", name="admin_user_create", methods={"GET", "POST"})
     */
    public function userCreate(Request $request): Response
    {
        $user = $this->userManager->createUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->userManager->updatePassword($user, $form->get('plainPassword')->getData());
            $em->persist($user);
            try {
                $em->flush();

                /** @var GiftList|null $chosenList */
                $chosenList = $user->getList();
                if (null !== $chosenList && $chosenList->getOwner()->getId() !== $user->getId()) {
                    // Exchange permissions
                    $this->aclManager->exchangePerms(
                        $chosenList,
                        $user,
                        $chosenList->getOwner(),
                        BestWishesMaskBuilder::MASK_OWNER
                    );
                }

                $this->addFlash('notice', sprintf('User "%s" created', $user->getUsername()));

                return $this->redirectToRoute('admin_users');
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('Error creating "%s": %s', $user->getUsername(), $e->getMessage()));
            }
        }

        return $this->render('admin/user_create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param User    $user
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @Route("/user/{id}/edit", name="admin_user_edit", requirements={"id": "\d+"}, methods={"GET", "POST"})
     */
    public function userEdit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, ['isEditing' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->userManager->updatePassword($user, $form->get('plainPassword')->getData());
            $em->persist($user);
            try {
                $em->flush();

                /** @var GiftList|null $chosenList */
                $chosenList = $user->getList();
                if (null !== $chosenList && $chosenList->getOwner()->getId() !== $user->getId()) {
                    // Exchange permissions
                    $this->aclManager->exchangePerms(
                        $chosenList,
                        $user,
                        $chosenList->getOwner(),
                        BestWishesMaskBuilder::MASK_OWNER
                    );
                }

                $this->addFlash('notice', sprintf('User "%s" updated', $user->getUsername()));

                return $this->redirectToRoute('admin_users');
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('Error editing "%s": %s', $user->getUsername(), $e->getMessage()));
            }
        }

        return $this->render('admin/user_edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
