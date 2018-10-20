<?php

namespace BestWishes\Security\Acl\Voter;

use Symfony\Component\Security\Acl\Voter\AclVoter;

/**
 * Redefined voter to be able to use our own permissions mask
 */
class BestWishesAclVoter extends AclVoter
{
}
