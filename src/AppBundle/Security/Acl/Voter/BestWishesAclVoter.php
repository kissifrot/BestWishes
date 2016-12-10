<?php

namespace AppBundle\Security\Acl\Voter;


use Symfony\Component\Security\Acl\Voter\AclVoter;

/**
 * Redifined voter to be able to use our own permisions mask
 */
class BestWishesAclVoter extends AclVoter
{
}
