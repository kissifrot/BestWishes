<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Security\Core\BestWishesSecurityContext;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class BestWishesSecurityExtension extends \Twig_Extension
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $securityChecker;

    /**
     * @var BestWishesSecurityContext
     */
    private $securityContext;

    public function __construct(AuthorizationCheckerInterface $securityChecker = null, BestWishesSecurityContext $securityContext)
    {
        $this->securityChecker = $securityChecker;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_multi_granted', array($this, 'isMultiGranted')),
            new \Twig_SimpleFunction('is_user_granted', array($this, 'isUserGranted')),
        );
    }

    /**
     * Checks if current user has the specified roles granted
     * @param      $roles
     * @param null $object
     * @param null $field
     * @return bool
     */
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
     * Checks if specified user has the specified role granted
     * @param      $role
     * @param null $object
     * @param      $user
     * @return bool
     */
    public function isUserGranted($role, $object = null, $user)
    {
        return $this->securityContext->isGranted($role, $object, $user);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bw_security';
    }
}
