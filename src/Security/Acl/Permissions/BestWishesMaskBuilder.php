<?php

namespace BestWishes\Security\Acl\Permissions;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Adds more masks newed for BestWishes on top of the base ones
 */
class BestWishesMaskBuilder extends MaskBuilder
{
    final public const MASK_SURPRISE_ADD = 256;        // 1 << 8
    final public const MASK_ALERT_ADD = 512;        // 1 << 9
    final public const MASK_ALERT_PURCHASE = 1024;        // 1 << 10
    final public const MASK_ALERT_EDIT = 2048;        // 1 << 11
    final public const MASK_ALERT_DELETE = 4096;        // 1 << 12

    // Stupid codes...
    final public const CODE_SURPRISE_ADD = 'S';
    final public const CODE_ALERT_ADD = 'A';
    final public const CODE_ALERT_PURCHASE = 'P';
    final public const CODE_ALERT_EDIT = 'I';
    final public const CODE_ALERT_DELETE = 'T';
}
