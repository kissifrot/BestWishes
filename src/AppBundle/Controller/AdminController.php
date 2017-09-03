<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GiftList;
use AppBundle\Entity\User;
use AppBundle\Form\Type\GiftListType;
use AppBundle\Form\Type\UserType;
use AppBundle\Security\Acl\Permissions\BestWishesMaskBuilder;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AdminController
 * @Route("admin")
 * @Security("has_role('ROLE_ADMIN')")
 *
 */
class AdminController extends Controller
{
    /**
     * @Route("/lists", name="admin_lists")
     * @Method({"GET"})
     */
    public function listsAction()
    {
        $lists = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:GiftList')->findAll();

        $deleteForm = $this->createSimpleActionForm(new GiftList(), 'delete')->createView();

        return $this->render('AppBundle:admin:lists.html.twig', compact('lists', 'deleteForm'));
    }

    /**
     * @Route("/lists/rights", name="admin_lists_rights")
     * @Method({"GET"})
     */
    public function listsRightsAction()
    {
        $lists = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:GiftList')->findAll();
        $users = $this->get('fos_user.user_manager')->findUsers();
        $availablePermissions = [
            'OWNER',
            'EDIT',
            'SURPRISE_ADD',
            'ALERT_ADD',
            'ALERT_PURCHASE',
            'ALERT_EDIT',
            'ALERT_DELETE'
        ];

        return $this->render('AppBundle:admin:lists_rights.html.twig',
            compact('lists', 'users', 'availablePermissions'));
    }

    /**
     * @Route("/list/{id}/updatePerm", name="admin_update_list_perm", requirements={"id": "\d+"}, options = { "expose" = true })
     * @Method({"POST"})
     * @param Request  $request
     * @param GiftList $giftList
     * @return JsonResponse
     */
    public function updatePermisionAction(Request $request, GiftList $giftList)
    {
        $defaultData = [
            'sucess'  => false,
            'message' => 'An error occurred'
        ];
        $userId = $request->request->getInt('userId');
        $perm = $request->request->get('perm');
        // Do some input checks
        if (empty($perm) || empty($userId)) {
            new JsonResponse($defaultData);
        }
        $availablePermissions = ['EDIT', 'SURPRISE_ADD', 'ALERT_ADD', 'ALERT_PURCHASE', 'ALERT_EDIT', 'ALERT_DELETE'];
        if (!in_array($perm, $availablePermissions)) {
            return new JsonResponse($defaultData);
        }
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userId);
        if (!$user) {
            return new JsonResponse(array_merge($defaultData, ['message' => 'User could not be found']));
        }

        // Build the mask to update
        $permMask = constant('AppBundle\Security\Acl\Permissions\BestWishesMaskBuilder::MASK_' . $perm);
        $hadPerm = $this->get('bw.security_context')->isGranted($perm, $giftList, $user);

        if ($hadPerm) {
            // Remove the specified permission
            $this->get('bw.security_acl_manager')->revoke($giftList, $user, $permMask);
        } else {
            // Add the specified permission
            $this->get('bw.security_acl_manager')->grant($giftList, $user, $permMask);
        }

        $successData = [
            'sucess'  => true,
            'message' => sprintf('Permission updated for "%s"', $giftList->getName())
        ];

        return new JsonResponse($successData);
    }

    /**
     * @param Request  $request
     * @param GiftList $giftList
     * @Route("/list/{id}", name="admin_list_delete", requirements={"id": "\d+"})
     * @Method({"DELETE"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listDeleteAction(Request $request, GiftList $giftList)
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/list/create", name="admin_list_create")
     * @Method({"GET", "POST"})
     *
     */
    public function listCreateAction(Request $request)
    {
        $giftList = new GiftList();

        $form = $this->createForm(GiftListType::class, $giftList);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($giftList);
            try {
                $em->flush();

                // Add the correct ACL for the onwer
                $permMask = BestWishesMaskBuilder::MASK_OWNER;
                $this->get('bw.security_acl_manager')->grant($giftList, $giftList->getOwner(), $permMask);

                $this->addFlash('notice', sprintf('List "%s" created', $giftList->getName()));

                return $this->redirectToRoute('admin_lists');
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('Error creating "%s": %s', $giftList->getName(), $e->getMessage()));
            }
        }

        return $this->render('AppBundle:admin:list_create.html.twig',
            ['form' => $form->createView()]);
    }

    /**
     * @param Request  $request
     * @param GiftList $giftList
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/list/{id}/edit", name="admin_list_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function listEditAction(Request $request, GiftList $giftList)
    {
        $originGiftList = clone $giftList;
        $form = $this->createForm(GiftListType::class, $giftList);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($giftList);
            try {
                $em->flush();

                if ($originGiftList->getOwner() !== $giftList->getOwner()) {
                    // Add the correct ACL for the new onwer
                    $permMask = BestWishesMaskBuilder::MASK_OWNER;
                    $this->get('bw.security_acl_manager')->grant($giftList, $giftList->getOwner(), $permMask);
                    // Remove the owner ACL for the old owner
                    $this->get('bw.security_acl_manager')->revoke($giftList, $originGiftList->getOwner(), $permMask);
                }
                $this->addFlash('notice', sprintf('List "%s" updated', $giftList->getName()));

                return $this->redirectToRoute('admin_lists');
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('Error editing "%s": %s', $giftList->getName(), $e->getMessage()));
            }
        }

        return $this->render('AppBundle:admin:list_edit.html.twig',
            ['form' => $form->createView(), 'list' => $giftList]);
    }

    /**
     * Creates a form for simple actions
     *
     * @param mixed  $entity
     * @param string $action Chosen action
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\HttpFoundation\RedirectResponse Delete form or redirect
     */
    private function createSimpleActionForm($entity, $action = 'delete')
    {
        switch (get_class($entity)) {
            case GiftList::class:
                $routePart = 'list';
                break;
            case User::class:
                $routePart = 'user';
                break;
            default:
                throw new \RuntimeException(sprintf('The "%s" type is not supported', get_class($entity)));
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
     * @Method({"GET"})
     */
    public function usersAction()
    {
        $users = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:User')->findAll();

        $deleteForm = $this->createSimpleActionForm(new User(), 'delete')->createView();

        return $this->render('AppBundle:admin:users.html.twig', compact('users', 'deleteForm'));
    }

    /**
     * @param Request  $request
     * @param User $user
     * @Route("/user/{id}", name="admin_user_delete", requirements={"id": "\d+"})
     * @Method({"DELETE"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userDeleteAction(Request $request, User $user)
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/user/create", name="admin_user_create")
     * @Method({"GET", "POST"})
     *
     */
    public function userCreateAction(Request $request)
    {
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $userManager->updatePassword($user);
            $em->persist($user);
            try {
                $em->flush();

                /** @var GiftList $chosenList */
                $chosenList = $user->getList();
                if ($chosenList->getOwner()->getId() !== $user->getId()) {
                    // Add the correct ACL for the new onwer
                    $permMask = BestWishesMaskBuilder::MASK_OWNER;
                    $this->get('bw.security_acl_manager')->grant($chosenList, $user, $permMask);
                    // Remove the owner ACL for the old owner
                    $this->get('bw.security_acl_manager')->revoke($chosenList, $chosenList->getOwner(), $permMask);
                }

                $this->addFlash('notice', sprintf('User "%s" created', $user->getUsername()));

                return $this->redirectToRoute('admin_users');
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('Error creating "%s": %s', $user->getUsername(), $e->getMessage()));
            }
        }

        return $this->render('AppBundle:admin:user_create.html.twig',
            ['form' => $form->createView()]);
    }
}
