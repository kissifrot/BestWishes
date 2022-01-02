<?php

namespace BestWishes\Twig\Extension;

use BestWishes\Security\Core\BestWishesSecurityContext;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BestWishesSecurityExtension extends AbstractExtension
{
    private BestWishesSecurityContext $securityContext;
    private ?AuthorizationCheckerInterface $securityChecker;

    public function __construct(BestWishesSecurityContext $securityContext, ?AuthorizationCheckerInterface $securityChecker = null)
    {
        $this->securityChecker = $securityChecker;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_multi_granted', [$this, 'isMultiGranted']),
            new TwigFunction('is_user_granted', [$this, 'isUserGranted']),
        ];
    }

    /**
     * Checks if current user has the specified roles granted
     * @param      $roles
     * @param null $object
     * @param null $field
     */
    public function isMultiGranted($roles, $object = null, $field = null): bool
    {
        if (null === $this->securityChecker) {
            return false;
        }

        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        try {
            if (!\is_array($roles)) {
                $roles = [$roles];
            }
            foreach ($roles as $role) {
                if ($this->securityChecker->isGranted($role, $object)) {
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
     * @param null   $object
     * @param        $user
     */
    public function isUserGranted(string $role, $object = null, $user): bool
    {
        return $this->securityContext->isGranted($role, $object, $user);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'bw_security';
    }
}
