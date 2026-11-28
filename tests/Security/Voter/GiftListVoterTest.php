<?php

namespace BestWishes\Tests\Security\Voter;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Repository\GiftListPermissionRepository;
use BestWishes\Security\Voter\GiftListVoter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class GiftListVoterTest extends TestCase
{
    private GiftListPermissionRepository&Stub $permissionRepository;
    private GiftListVoter $voter;

    protected function setUp(): void
    {
        $this->permissionRepository = $this->createStub(GiftListPermissionRepository::class);
        $hierarchy = new RoleHierarchy(['ROLE_SUPER_ADMIN' => ['ROLE_ADMIN']]);
        $this->voter = new GiftListVoter($this->permissionRepository, $hierarchy);
    }

    public function testOwnerWithPermissionHasAccess(): void
    {
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList($owner);
        $token = $this->createToken($owner);

        // Owner HAS permission in DB
        $this->permissionRepository->method('hasPermission')
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
        $giftList = $this->createGiftList($owner);
        $token = $this->createToken($owner);

        // Owner does NOT have permission in DB
        $this->permissionRepository->method('hasPermission')
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
        $giftList = $this->createGiftList($owner);
        $token = $this->createToken($owner);

        // Owner HAS alerts configured in DB
        $this->permissionRepository->method('hasPermission')
            ->willReturn(true);

        // Owner CAN have alert permissions if explicitly configured
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $giftList, ['ALERT_ADD'])
        );
    }

    public function testSuperAdminInheritsAdminRights(): void
    {
        $admin = $this->createUser(2, 'admin', ['ROLE_SUPER_ADMIN']);
        $owner = $this->createUser(1, 'owner');
        $giftList = $this->createGiftList($owner);
        $token = $this->createToken($admin);

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
        $giftList = $this->createGiftList($owner);
        $token = $this->createToken($user);

        $this->permissionRepository->method('hasPermission')
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
        $giftList = $this->createGiftList($owner);
        $token = $this->createToken($user);

        $this->permissionRepository->method('hasPermission')
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
        $giftList = $this->createGiftList($owner);
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

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

    public function testOwnerComesFromTheListEvenWhenAnOldPermissionStillExists(): void
    {
        $owner = $this->createUser(1, 'owner');
        $formerOwner = $this->createUser(2, 'former-owner');
        $list = $this->createGiftList($owner);

        $this->permissionRepository->method('hasPermission')->willReturnCallback(
            static fn (GiftList $list, User $user, string $permission): bool => $permission === 'OWNER'
        );

        self::assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->createToken($owner), $list, ['OWNER']));
        self::assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($this->createToken($formerOwner), $list, ['OWNER']));
    }

    /** @param list<string> $roles */
    private function createUser(int $id, string $username, array $roles = ['ROLE_USER']): User
    {
        $user = $this->createStub(User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getUserIdentifier')->willReturn($username);
        $user->method('getRoles')->willReturn($roles);
        return $user;
    }

    private function createGiftList(User $owner): GiftList
    {
        $giftList = new GiftList();
        $giftList->setOwner($owner);
        return $giftList;
    }

    private function createToken(User $user): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getRoleNames')->willReturn($user->getRoles());
        return $token;
    }
}

