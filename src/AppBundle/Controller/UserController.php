<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GiftList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @Route("user")
 * @Security("has_role('ROLE_USER')")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_home")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return $this->render('AppBundle:user:index.html.twig');
    }

    /**
     * @Route("/manage-alerts", name="user_manage_alerts")
     * @Method({"GET"})
     */
    public function manageAlertsAction(): Response
    {
        $availableAlerts = [
            'ALERT_ADD',
            'ALERT_PURCHASE',
            'ALERT_EDIT',
            'ALERT_DELETE'
        ];
        $lists = $this->getDoctrine()->getManager()->getRepository('AppBundle:GiftList')->findAll();
        $user = $this->getUser();
        return $this->render('AppBundle:user:manage_alerts.html.twig',
            compact('lists', 'availableAlerts', 'user'));
    }

    /**
     * @Route("/{id}/updateAlert", name="user_update_list_alert", requirements={"id": "\d+"}, options = { "expose" = true })
     * @Method({"POST"})
     * @param Request  $request
     * @param GiftList $giftList
     * @return JsonResponse
     */
    public function updateAlertAction(Request $request, GiftList $giftList): JsonResponse
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
        $alertMask = \constant('AppBundle\Security\Acl\Permissions\BestWishesMaskBuilder::MASK_' . $alert);
        $hadAlert = $this->get('bw.security_context')->isGranted($alert, $giftList, $user);
        $action = $hadAlert ? 'revoke' : 'grant';
        $this->get('bw.security_acl_manager')->$action($giftList, $user, $alertMask);

        $successData = [
            'success'  => true,
            'message' => sprintf('Alert updated for "%s"', $giftList->getName())
        ];

        return new JsonResponse($successData);
    }
}
