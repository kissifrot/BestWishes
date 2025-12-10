<?php

namespace BestWishes\Tests\Security\Voter;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Repository\GiftListPermissionRepository;
use BestWishes\Security\Voter\GiftListVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class GiftListVoterTest extends TestCase
{
    private GiftListPermissionRepository $permissionRepository;
    private Security $security;
    private GiftListVoter $voter;

    protected function setUp(): void
    {
        $this->permissionRepository = $this->createStub(GiftListPermissionRepository::class);
        $this->security = $this->createStub(Security::class);
        $this->voter = new GiftListVoter($this->permissionRepository, $this->security);
    }

    public function testOwnerWithPermissionHasAccess(): void
    {
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList(1, $owner);
        $token = $this->createToken($owner);

        $this->security->method('isGranted')->willReturn(false);

        // Owner HAS permission in DB
        $this->permissionRepository->method('hasPermission')
            ->with($giftList, $owner, 'EDIT')
            ->willReturn(true);

        // Owner with permission in DB has access
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $giftList, ['EDIT'])
        );
    }

    public function testOwnerWithoutPermissionIsDenied(): void
    {
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList(1, $owner);
        $token = $this->createToken($owner);

        $this->security->method('isGranted')->willReturn(false);

        // Owner does NOT have permission in DB
        $this->permissionRepository->method('hasPermission')
            ->with($giftList, $owner, 'DELETE')
            ->willReturn(false);

        // Owner without permission in DB is denied (just like old ACL system)
        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $giftList, ['DELETE'])
        );
    }

    public function testOwnerCanHaveAlertPermissionsIfConfigured(): void
    {
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList(1, $owner);
        $token = $this->createToken($owner);

        $this->security->method('isGranted')->willReturn(false);

        // Owner HAS alerts configured in DB
        $this->permissionRepository->method('hasPermission')
            ->with($giftList, $owner, 'ALERT_ADD')
            ->willReturn(true);

        // Owner CAN have alert permissions if explicitly configured
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $giftList, ['ALERT_ADD'])
        );
    }

    public function testAdminHasAllPermissions(): void
    {
        $admin = $this->createUser(2, 'admin');
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList(1, $owner);
        $token = $this->createToken($admin);

        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        // Admin has all rights
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $giftList, ['EDIT'])
        );
    }

    public function testUserWithPermissionHasAccess(): void
    {
        $user = $this->createUser(2, 'user');
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList(1, $owner);
        $token = $this->createToken($user);

        $this->security->method('isGranted')->willReturn(false);
        $this->permissionRepository->method('hasPermission')
            ->with($giftList, $user, 'ALERT_ADD')
            ->willReturn(true);

        // User with permission has access
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $giftList, ['ALERT_ADD'])
        );
    }

    public function testUserWithoutPermissionIsDenied(): void
    {
        $user = $this->createUser(2, 'user');
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList(1, $owner);
        $token = $this->createToken($user);

        $this->security->method('isGranted')->willReturn(false);
        $this->permissionRepository->method('hasPermission')
            ->with($giftList, $user, 'EDIT')
            ->willReturn(false);

        // User without permission is denied
        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $giftList, ['EDIT'])
        );
    }

    public function testAnonymousUserIsDenied(): void
    {
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList(1, $owner);
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        $this->security->method('isGranted')->willReturn(false);

        // Anonymous user is denied
        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $giftList, ['VIEW'])
        );
    }

    public function testVoterAbstainsOnNonGiftList(): void
    {
        $user = $this->createUser(1, 'user');
        $token = $this->createToken($user);
        $nonGiftList = new \stdClass();

        // Voter abstains for non-GiftList objects
        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->voter->vote($token, $nonGiftList, ['EDIT'])
        );
    }

    private function createUser(int $id, string $username): User
    {
        $user = $this->createStub(User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getUserIdentifier')->willReturn($username);
        return $user;
    }

    private function createGiftList(int $id, User $owner): GiftList
    {
        $giftList = $this->createStub(GiftList::class);
        $giftList->method('getId')->willReturn($id);
        $giftList->method('getOwner')->willReturn($owner);
        return $giftList;
    }

    private function createToken(User $user): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        return $token;
    }
}

