<?php

namespace BestWishes\Twig\Extension;

use BestWishes\Entity\User;
use BestWishes\Security\Core\BestWishesSecurityContext;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BestWishesSecurityExtension extends AbstractExtension
{
    public function __construct(private readonly BestWishesSecurityContext $securityContext, private readonly ?AuthorizationCheckerInterface $securityChecker = null)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_multi_granted', [$this, 'isMultiGranted']),
            new TwigFunction('is_user_granted', [$this, 'isUserGranted']),
        ];
    }

    /**
     * Check if current user has the specified roles granted
     */
    public function isMultiGranted(mixed $roles, mixed $object = null): bool
    {
        if (null === $this->securityChecker) {
            return false;
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
        } catch (AuthenticationCredentialsNotFoundException) {
            return false;
        }
    }

    /**
     * Check if a specific user has a permission on an object
     *
     * @param string $permission Permission to check (e.g., 'EDIT', 'ALERT_ADD')
     * @param mixed $object Object to check permission on (e.g., GiftList)
     * @param User $user User to check permission for
     */
    public function isUserGranted(string $permission, mixed $object, User $user): bool
    {
        return $this->securityContext->isGranted($permission, $object, $user);
    }

    public function getName(): string
    {
        return 'bw_security';
    }
}
