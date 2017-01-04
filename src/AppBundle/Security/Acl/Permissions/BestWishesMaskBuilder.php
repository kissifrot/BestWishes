<?php

namespace AppBundle\Security\Acl\Permissions;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Adds more masks newed for BestWishes on top of the base ones
 */
class BestWishesMaskBuilder extends MaskBuilder
{
    const MASK_SURPRISE_ADD = 256;        // 1 << 8
    const MASK_ALERT_ADD = 512;        // 1 << 9
    const MASK_ALERT_PURCHASE = 1024;        // 1 << 10
    const MASK_ALERT_EDIT = 2048;        // 1 << 11
    const MASK_ALERT_DELETE = 4096;        // 1 << 12

    // Stupid codes...
    const CODE_SURPRISE_ADD = 'S';
    const CODE_ALERT_ADD = 'A';
    const CODE_ALERT_PURCHASE = 'P';
    const CODE_ALERT_EDIT = 'I';
    const CODE_ALERT_DELETE = 'T';
}
