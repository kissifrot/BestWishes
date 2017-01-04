<?php

namespace AppBundle\Security\Acl\Permissions;

use Symfony\Component\Security\Acl\Permission\BasicPermissionMap;

class BestWishesPermissionMap extends BasicPermissionMap
{
    const PERMISSION_SURPRISE_ADD = 'SURPRISE_ADD';
    const PERMISSION_ALERT_ADD = 'ALERT_ADD';
    const PERMISSION_ALERT_PURCHASE = 'ALERT_PURCHASE';
    const PERMISSION_ALERT_EDIT = 'ALERT_EDIT';
    const PERMISSION_ALERT_DELETE = 'ALERT_DELETE';

    public function __construct()
    {
        parent::__construct();

        $this->map = array_merge(
            $this->map,
            [
                self::PERMISSION_SURPRISE_ADD   => [
                    BestWishesMaskBuilder::MASK_SURPRISE_ADD,
                ],
                self::PERMISSION_ALERT_ADD      => [
                    BestWishesMaskBuilder::MASK_ALERT_ADD,
                ],
                self::PERMISSION_ALERT_PURCHASE => [
                    BestWishesMaskBuilder::MASK_ALERT_PURCHASE,
                ],
                self::PERMISSION_ALERT_EDIT     => [
                    BestWishesMaskBuilder::MASK_ALERT_EDIT,
                ],
                self::PERMISSION_ALERT_DELETE   => [
                    BestWishesMaskBuilder::MASK_ALERT_DELETE,
                ],
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
