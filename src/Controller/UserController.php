<?php

namespace BestWishes\Controller;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Security\PermissionManager;
use BestWishes\Security\Core\BestWishesSecurityContext;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: 'user')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly BestWishesSecurityContext $securityContext, private readonly PermissionManager $permissionManager)
    {
    }

    #[Route(path: '/', name: 'user_home')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route(path: '/manage-alerts', name: 'user_manage_alerts')]
    public function manageAlerts(): Response
    {
        $availableAlerts = [
            'ALERT_ADD',
            'ALERT_PURCHASE',
            'ALERT_EDIT',
            'ALERT_DELETE'
        ];
        $lists = $this->entityManager->getRepository(GiftList::class)->findAll();
        $user = $this->getUser();
        return $this->render(
            'user/manage_alerts.html.twig',
            compact('lists', 'availableAlerts', 'user')
        );
    }

    #[Route(path: '/{id}/updateAlert', name: 'user_update_list_alert', requirements: ['id' => '\d+'], options: ['expose' => true], methods: ['POST'])]
    public function updateAlert(Request $request, GiftList $giftList): JsonResponse
    {
        $defaultData = [
            'success'  => false,
            'message' => 'An error occurred'
        ];
        $alert = $request->request->get('alert');
        // Do some input checks
        if (empty($alert)) {
            new JsonResponse($defaultData);
        }
        $availableAlerts = [
            'ALERT_ADD',
            'ALERT_PURCHASE',
            'ALERT_EDIT',
            'ALERT_DELETE'
        ];
        if (!\in_array($alert, $availableAlerts, true)) {
            return new JsonResponse($defaultData);
        }
        /** @var User $user */
        $user = $this->getUser();

        $hadAlert = $this->securityContext->isGranted($alert, $giftList, $user);
        if ($hadAlert) {
            $this->permissionManager->revoke($giftList, $user, $alert);
        } else {
            $this->permissionManager->grant($giftList, $user, $alert);
        }

        $successData = [
            'success'  => true,
            'message' => \sprintf('Alert updated for "%s"', $giftList->getName())
        ];

        return new JsonResponse($successData);
    }
}
