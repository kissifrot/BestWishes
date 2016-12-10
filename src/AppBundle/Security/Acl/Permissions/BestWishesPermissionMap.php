<?php

namespace AppBundle\Security\Acl\Permissions;

use Symfony\Component\Security\Acl\Permission\BasicPermissionMap;

class BestWishesPermissionMap extends BasicPermissionMap
{
    const PERMISSION_SURPRISE_ADD = 'SURPRISE_ADD';
    const PERMISSION_ALERT_ADD = 'ALERT_ADD';
    const PERMISSION_ALERT_PURCHASE = 'ALERT_PURCHASE';
    const PERMISSION_ALERT_EDIT = 'ALERT_EDIT';

    public function __construct()
    {
        parent::__construct();

        $this->map = array_merge(
            $this->map,
            [
                self::PERMISSION_SURPRISE_ADD => array(
                    BestWishesMaskBuilder::MASK_SURPRISE_ADD,
                ),
                self::PERMISSION_ALERT_ADD => array(
                    BestWishesMaskBuilder::MASK_ALERT_ADD,
                ),
                self::PERMISSION_ALERT_PURCHASE => array(
                    BestWishesMaskBuilder::MASK_ALERT_PURCHASE,
                ),
                self::PERMISSION_ALERT_EDIT => array(
                    BestWishesMaskBuilder::MASK_ALERT_EDIT,
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMaskBuilder()
    {
        return new BestWishesMaskBuilder();
    }
}
