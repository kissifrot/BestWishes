<?php

namespace BestWishes\Controller;

use BestWishes\Entity\GiftList;
use BestWishes\Security\AclManager;
use BestWishes\Security\Core\BestWishesSecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @Route("user")
 * @Security("has_role('ROLE_USER')")
 */
class UserController extends AbstractController
{
    private $securityContext;
    private $aclManager;

    public function __construct(BestWishesSecurityContext $securityContext, AclManager $aclManager)
    {
        $this->securityContext = $securityContext;
        $this->aclManager = $aclManager;
    }

    /**
     * @Route("/", name="user_home")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/manage-alerts", name="user_manage_alerts")
     */
    public function manageAlerts(): Response
    {
        $availableAlerts = [
            'ALERT_ADD',
            'ALERT_PURCHASE',
            'ALERT_EDIT',
            'ALERT_DELETE'
        ];
        $lists = $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findAll();
        $user = $this->getUser();
        return $this->render('user/manage_alerts.html.twig',
            compact('lists', 'availableAlerts', 'user'));
    }

    /**
     * @Route("/{id}/updateAlert", name="user_update_list_alert", requirements={"id": "\d+"}, options = { "expose" = true }, methods={"POST"})
     * @param Request  $request
     * @param GiftList $giftList
     * @return JsonResponse
     */
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
        $user = $this->getUser();

        // Build the mask to update
        $alertMask = \constant(sprintf('BestWishes\Security\Acl\Permissions\BestWishesMaskBuilder::MASK_%s' , $alert));
        $hadAlert = $this->securityContext->isGranted($alert, $giftList, $user);
        $action = $hadAlert ? 'revoke' : 'grant';
        $this->aclManager->$action($giftList, $user, $alertMask);

        $successData = [
            'success'  => true,
            'message' => sprintf('Alert updated for "%s"', $giftList->getName())
        ];

        return new JsonResponse($successData);
    }
}
