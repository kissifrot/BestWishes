<?php

namespace BestWishes\Twig\Extension;

use BestWishes\Entity\User;
use BestWishes\Security\Core\BestWishesSecurityContext;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BestWishesSecurityExtension extends AbstractExtension
{
    public function __construct(private readonly BestWishesSecurityContext $securityContext, private readonly ?AuthorizationCheckerInterface $securityChecker = null)
    {
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
     */
    public function isMultiGranted(mixed $roles, mixed $object = null, string $field = null): bool
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
        } catch (AuthenticationCredentialsNotFoundException) {
            return false;
        }
    }

    /**
     * Checks if specified user has the specified role granted
     */
    public function isUserGranted(string $role, User $user, mixed $object = null): bool
    {
        return $this->securityContext->isGranted($role, $object, $user);
    }

    public function getName(): string
    {
        return 'bw_security';
    }
}
