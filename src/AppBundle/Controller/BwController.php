<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GiftList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BwController extends Controller
{
    /**
     * Check if current user has specifc for a specific GiftList
     * @param mixed $attributes Attritude to check, e.g 'OWNER'
     * @param GiftList $list
     */
    protected function checkAccess($attributes, GiftList $list): void
    {
        $authorizationChecker = $this->get('security.authorization_checker');
        if (!\is_array($attributes)) {
            $attributes = [$attributes];
        }
        foreach ($attributes as $attribute) {
            if ($authorizationChecker->isGranted($attribute, $list)) {
                return;
            }
        }
        throw $this->createAccessDeniedException();
    }
}
