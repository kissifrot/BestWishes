<?php

namespace BestWishes\Tests\Twig\Extension;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Security\Core\BestWishesSecurityContext;
use BestWishes\Twig\Extension\BestWishesSecurityExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\TwigFunction;

class BestWishesSecurityExtensionTest extends TestCase
{
    public function testIsUserGrantedWithPermission(): void
    {
        $user = $this->createStub(User::class);
        $giftList = $this->createStub(GiftList::class);

        $securityContext = $this->createMock(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createStub(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $securityContext
            ->expects($this->once())
            ->method('isGranted')
            ->with('EDIT', $giftList, $user)
            ->willReturn(true);

        $result = $extension->isUserGranted('EDIT', $giftList, $user);

        $this->assertTrue($result);
    }

    public function testIsUserGrantedWithoutPermission(): void
    {
        $user = $this->createStub(User::class);
        $giftList = $this->createStub(GiftList::class);

        $securityContext = $this->createMock(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createStub(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $securityContext
            ->expects($this->once())
            ->method('isGranted')
            ->with('DELETE', $giftList, $user)
            ->willReturn(false);

        $result = $extension->isUserGranted('DELETE', $giftList, $user);

        $this->assertFalse($result);
    }

    public function testIsMultiGrantedWithSingleRole(): void
    {
        $object = new \stdClass();

        $securityContext = $this->createStub(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN', $object)
            ->willReturn(true);

        $result = $extension->isMultiGranted('ROLE_ADMIN', $object);

        $this->assertTrue($result);
    }

    public function testIsMultiGrantedWithMultipleRoles(): void
    {
        $object = new \stdClass();

        $securityContext = $this->createStub(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $authorizationChecker
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->willReturnCallback(function ($role) {
                return $role === 'ROLE_USER';
            });

        $result = $extension->isMultiGranted(['ROLE_ADMIN', 'ROLE_USER'], $object);

        $this->assertTrue($result);
    }

    public function testIsMultiGrantedWithNoMatchingRoles(): void
    {
        $object = new \stdClass();

        $securityContext = $this->createStub(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $authorizationChecker
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->willReturn(false);

        $result = $extension->isMultiGranted(['ROLE_ADMIN', 'ROLE_USER'], $object);

        $this->assertFalse($result);
    }

    public function testIsMultiGrantedWithNullChecker(): void
    {
        $securityContext = $this->createStub(BestWishesSecurityContext::class);
        $extension = new BestWishesSecurityExtension($securityContext, null);

        $result = $extension->isMultiGranted('ROLE_ADMIN');

        $this->assertFalse($result);
    }

    public function testIsMultiGrantedHandlesAuthenticationException(): void
    {
        $securityContext = $this->createStub(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->willThrowException(new AuthenticationCredentialsNotFoundException());

        $result = $extension->isMultiGranted('ROLE_ADMIN');

        $this->assertFalse($result);
    }

    public function testIsMultiGrantedWithoutObject(): void
    {
        $securityContext = $this->createStub(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN', null)
            ->willReturn(true);

        $result = $extension->isMultiGranted('ROLE_ADMIN');

        $this->assertTrue($result);
    }

    public function testGetFunctions(): void
    {
        $securityContext = $this->createStub(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createStub(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $functions = $extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);

        $functionNames = array_map(fn(TwigFunction $f) => $f->getName(), $functions);
        $this->assertContains('is_multi_granted', $functionNames);
        $this->assertContains('is_user_granted', $functionNames);
    }

    public function testIsUserGrantedWithAlertPermission(): void
    {
        $user = $this->createStub(User::class);
        $giftList = $this->createStub(GiftList::class);

        $securityContext = $this->createMock(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createStub(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        $securityContext
            ->expects($this->once())
            ->method('isGranted')
            ->with('ALERT_ADD', $giftList, $user)
            ->willReturn(true);

        $result = $extension->isUserGranted('ALERT_ADD', $giftList, $user);

        $this->assertTrue($result);
    }

    public function testIsMultiGrantedStopsOnFirstMatch(): void
    {
        $object = new \stdClass();

        $securityContext = $this->createStub(BestWishesSecurityContext::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $extension = new BestWishesSecurityExtension($securityContext, $authorizationChecker);

        // Should stop after first match
        $authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN', $object)
            ->willReturn(true);

        $result = $extension->isMultiGranted(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_GUEST'], $object);

        $this->assertTrue($result);
    }
}
