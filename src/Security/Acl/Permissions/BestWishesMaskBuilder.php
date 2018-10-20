<?php

namespace BestWishes\Security\Acl\Permissions;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Adds more masks newed for BestWishes on top of the base ones
 */
class BestWishesMaskBuilder extends MaskBuilder
{
    public const MASK_SURPRISE_ADD = 256;        // 1 << 8
    public const MASK_ALERT_ADD = 512;        // 1 << 9
    public const MASK_ALERT_PURCHASE = 1024;        // 1 << 10
    public const MASK_ALERT_EDIT = 2048;        // 1 << 11
    public const MASK_ALERT_DELETE = 4096;        // 1 << 12

    // Stupid codes...
    public const CODE_SURPRISE_ADD = 'S';
    public const CODE_ALERT_ADD = 'A';
    public const CODE_ALERT_PURCHASE = 'P';
    public const CODE_ALERT_EDIT = 'I';
    public const CODE_ALERT_DELETE = 'T';
}
