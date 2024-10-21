<?php

namespace BestWishes\Tests\Entity;

use BestWishes\Entity\Category;
use BestWishes\Entity\Gift;
use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

/**
 * @group time-sensitive
 */
class GiftTest extends TestCase
{
    use ClockSensitiveTrait;

    private \DateTimeImmutable $now;
    private Category $category;
    private GiftList $giftList;
    private User $buyer;

    public function setUp(): void
    {
        static::mockTime(new \DateTimeImmutable('2024-10-21 10:00:00'));
        $this->now = new DatePoint();

        // Force id on category
        $reflectionClass = new \ReflectionClass(Category::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $this->category = new Category();
        $reflectionProperty->setValue($this->category, 1);

        $this->giftList = new GiftList();
        $this->category->setList($this->giftList);
        $this->buyer = new User();
    }

    public function testSurpriseCreation(): void
    {
        $gift = new Gift(true, $this->category);
        $this->assertTrue($gift->isSurprise());
        $this->assertSame($this->category, $gift->getCategory());
        $this->assertSame($this->category->getId(), $gift->getCategoryId());
        $this->assertSame($this->giftList, $gift->getList());
        $this->assertFalse($gift->isBought());
        $this->assertFalse($gift->isReceived());
        $this->assertNull($gift->getBuyer());
        $this->assertNull($gift->getReceivedDate());
        $this->assertNull($gift->getPurchaseDate());
        $this->assertNull($gift->getPurchaseComment());
        $this->assertNull($gift->getImage());
        $this->assertNull($gift->getMoreDetail());
        $this->assertNull($gift->getMoreDetailUrl());
        $this->assertSame(0, $gift->getEditsCount());
        $this->assertEquals($this->now, $gift->getAddedDate());
    }

    public function testNormalCreation(): void
    {
        $gift = new Gift(false, $this->category);
        $this->assertFalse($gift->isSurprise());
        $this->assertSame($this->category, $gift->getCategory());
        $this->assertSame($this->category->getId(), $gift->getCategoryId());
        $this->assertSame($this->giftList, $gift->getList());
        $this->assertFalse($gift->isBought());
        $this->assertFalse($gift->isReceived());
        $this->assertNull($gift->getBuyer());
        $this->assertNull($gift->getReceivedDate());
        $this->assertNull($gift->getPurchaseDate());
        $this->assertNull($gift->getPurchaseComment());
        $this->assertSame(0, $gift->getEditsCount());
        $this->assertEquals($this->now, $gift->getAddedDate());
    }

    public function testMarkReceived(): void
    {
        $gift = new Gift(false, $this->category);
        $gift->markReceived();
        $this->assertFalse($gift->isSurprise());
        $this->assertFalse($gift->isBought());
        $this->assertTrue($gift->isReceived());
        $this->assertNull($gift->getBuyer());
        $this->assertNull($gift->getPurchaseDate());
        $this->assertNull($gift->getPurchaseComment());
        $this->assertEquals($this->now, $gift->getReceivedDate());
    }

    public function testMarkPurchasedEmptyComment(): void
    {
        $gift = new Gift(false, $this->category);
        $gift->markPurchasedBy($this->buyer, '');
        $this->assertFalse($gift->isSurprise());
        $this->assertTrue($gift->isBought());
        $this->assertFalse($gift->isReceived());
        $this->assertSame($this->buyer, $gift->getBuyer());
        $this->assertNull($gift->getReceivedDate());
        $this->assertEmpty($gift->getPurchaseComment());
        $this->assertEquals($this->now, $gift->getPurchaseDate());
    }

    public function testMarkPurchasedComment(): void
    {
        $gift = new Gift(false, $this->category);
        $comment = 'This is a comment';
        $gift->markPurchasedBy($this->buyer, $comment);
        $this->assertFalse($gift->isSurprise());
        $this->assertTrue($gift->isBought());
        $this->assertFalse($gift->isReceived());
        $this->assertSame($this->buyer, $gift->getBuyer());
        $this->assertNull($gift->getReceivedDate());
        $this->assertEquals($comment, $gift->getPurchaseComment());
        $this->assertEquals($this->now, $gift->getPurchaseDate());
    }

    public function testMarkSurprisePurchasedEmptyComment(): void
    {
        $gift = new Gift(true, $this->category);
        $gift->markPurchasedBy($this->buyer, '');
        $this->assertTrue($gift->isSurprise());
        $this->assertTrue($gift->isBought());
        $this->assertFalse($gift->isReceived());
        $this->assertSame($this->buyer, $gift->getBuyer());
        $this->assertNull($gift->getReceivedDate());
        $this->assertEmpty($gift->getPurchaseComment());
        $this->assertEquals($this->now, $gift->getPurchaseDate());
    }

    public function testMarkSurprisePurchasedComment(): void
    {
        $gift = new Gift(true, $this->category);
        $comment = 'This is a comment';
        $gift->markPurchasedBy($this->buyer, $comment);
        $this->assertTrue($gift->isSurprise());
        $this->assertTrue($gift->isBought());
        $this->assertFalse($gift->isReceived());
        $this->assertSame($this->buyer, $gift->getBuyer());
        $this->assertNull($gift->getReceivedDate());
        $this->assertEquals($comment, $gift->getPurchaseComment());
        $this->assertEquals($this->now, $gift->getPurchaseDate());
    }
}
