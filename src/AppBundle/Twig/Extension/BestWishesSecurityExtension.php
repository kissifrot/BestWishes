<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class BestWishesSecurityExtension extends \Twig_Extension
{
    private $securityChecker;

    public function __construct(AuthorizationCheckerInterface $securityChecker = null)
    {
        $this->securityChecker = $securityChecker;
    }

    public function isMultiGranted($roles, $object = null, $field = null)
    {
        if (null === $this->securityChecker) {
            return false;
        }

        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        try {
            if (!is_array($roles)) {
                $roles = [$roles];
            }
            foreach($roles as $role) {
                if($this->securityChecker->isGranted($role, $object)) {
                    return true;
                }
            }
            return false;
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_multi_granted', array($this, 'isMultiGranted')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bw_security';
    }
}
