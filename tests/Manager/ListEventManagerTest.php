<?php

namespace BestWishes\Tests\Manager;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\ListEvent;
use BestWishes\Manager\ListEventManager;
use BestWishes\Repository\ListEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * @group time-sensitive
 */
class ListEventManagerTest extends TestCase
{
    /** @var MockObject */
    private $em;
    /** @var MockObject */
    private $repo;

    public function setUp(): void
    {
        date_default_timezone_set('UTC');

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->repo = $this->createMock(ListEventRepository::class);
        $this->em->method('getRepository')->with(ListEvent::class)->willReturn($this->repo);
    }

    public function testNoActiveEvents(): void
    {
        $this->repo->expects($this->once())->method('findAllActive')->willReturn([]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $today = \DateTimeImmutable::createFromFormat('U', time());
        $gitList->setBirthDate($today);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals(false, $res);
    }

    public function testTodayBirthdayEvent(): void
    {
        ClockMock::withClockMock(strtotime('2019-11-05 20:00:00'));
        ClockMock::register(ListEventManager::class);

        $now = \DateTimeImmutable::createFromFormat('U', time());

        $listEventBdate = new ListEvent();
        $listEventBdate->setName('Birthday');
        $listEventBdate->setType('birthday');
        $refTime = mktime(0, 0, 1, $now->format('n'), $now->format('j') , $now->format('Y'));
        $today = $now->setTime(0,0,1);

        $this->repo->expects($this->once())->method('findAllActive')->willReturn([$listEventBdate]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $gitList->setBirthDate($today);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Birthday',
            'time' => $refTime,
            'daysLeft' => 0
        ], $res);
    }

    public function testChristmasEvent(): void
    {
        ClockMock::withClockMock(strtotime('2019-11-05 20:00:00'));
        ClockMock::register(ListEventManager::class);

        $listEventChristmas = new ListEvent();
        $listEventChristmas->setName('Christmas');
        $listEventChristmas->setDay(25);
        $listEventChristmas->setMonth(12);
        $today = \DateTimeImmutable::createFromFormat('U', time())->setTime(0,0,1);

        $this->repo->expects($this->once())->method('findAllActive')->willReturn([$listEventChristmas]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $gitList->setBirthDate($today);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Christmas',
            'time' => 1577232001,
            'daysLeft' => 50
        ], $res);
    }

    public function testTwoEventsToday(): void
    {
        ClockMock::withClockMock(strtotime('2019-11-05 20:00:00'));
        ClockMock::register(ListEventManager::class);

        $now = \DateTimeImmutable::createFromFormat('U', time());

        $listEventBdate = new ListEvent();
        $listEventBdate->setName('Birthday');
        $listEventBdate->setType('birthday');

        $listEventTday = new ListEvent();
        $listEventTday->setName('An event of today');
        $listEventTday->setDay((int) $now->format('j'));
        $listEventTday->setMonth((int) $now->format('n'));
        $listEventTday->setYear((int) $now->format('Y'));

        $this->repo->expects($this->once())->method('findAllActive')->willReturn([$listEventBdate, $listEventTday]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $gitList->setBirthDate($now);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Birthday',
            'time' => 1572912001,
            'daysLeft' => 0
        ], $res);
    }

    public function testTwoNextEvents(): void
    {
        $now = \DateTimeImmutable::createFromFormat('U', time());
        $inThreeMonths = $now->add(new \DateInterval('P3M'));
        $tomorrow = $now->add(new \DateInterval('P1D'));
        $afterTomorrow = $now->add(new \DateInterval('P2D'));

        $listEventBdate = new ListEvent(true);
        $listEventBdate->setName('Birthday');
        $listEventBdate->setType('birthday');

        $listEventTomorrow = new ListEvent(true);
        $listEventTomorrow->setName('A permanent event');
        $listEventTomorrow->setDay($tomorrow->format('j'));
        $listEventTomorrow->setMonth($tomorrow->format('n'));

        $listEventAfterTomorrow = new ListEvent();
        $listEventAfterTomorrow->setName('An event after tomorrow');
        $listEventAfterTomorrow->setDay((int) $afterTomorrow->format('j'));
        $listEventAfterTomorrow->setMonth((int) $afterTomorrow->format('n'));
        $listEventAfterTomorrow->setYear((int) $afterTomorrow->format('Y'));

        $refTime = mktime(0, 0, 1, $listEventTomorrow->getMonth(), $listEventTomorrow->getDay(), $tomorrow->format('Y'));

        $this->repo->expects($this->once())->method('findAllActive')->willReturn([$listEventBdate, $listEventAfterTomorrow, $listEventTomorrow]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $gitList->setBirthDate($inThreeMonths);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'A permanent event',
            'time' => $refTime,
            'daysLeft' => 1
        ], $res);
    }

    public function testEventInThreeDays(): void
    {
        $now = \DateTimeImmutable::createFromFormat('U', time());
        $nextThreeDays = $now->add(new \DateInterval('P3D'));

        $listEvent = new ListEvent();
        $listEvent->setName('Next three days event');
        $listEvent->setDay($nextThreeDays->format('j'));
        $listEvent->setMonth($nextThreeDays->format('n'));
        $listEvent->setYear($nextThreeDays->format('Y'));

        $refTime = mktime(0, 0, 1, $nextThreeDays->format('n'), $nextThreeDays->format('j'), $nextThreeDays->format('Y'));

        $this->repo->expects($this->once())->method('findAllActive')->willReturn([$listEvent]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $gitList->setBirthDate($now);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Next three days event',
            'time' => $refTime,
            'daysLeft' => 3
        ], $res);
    }

    public function testYesterdayEvent(): void
    {
        ClockMock::withClockMock(strtotime('2019-11-05 20:00:00'));

        $now = \DateTimeImmutable::createFromFormat('U', time());
        $yesterday = $now->sub(new \DateInterval('P1D'));

        $listEvent = new ListEvent();
        $listEvent->setName('Yesterday event');
        $listEvent->setDay($yesterday->format('j'));
        $listEvent->setMonth($yesterday->format('n'));
        $listEvent->setYear($yesterday->format('Y'));

        $this->repo->expects($this->once())->method('findAllActive')->willReturn([$listEvent]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $gitList->setBirthDate($now);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals(false, $res);
    }

    public function testNextYearEvent(): void
    {
        $now = \DateTimeImmutable::createFromFormat('U', time());
        $yesterday = $now->sub(new \DateInterval('P1D'));

        $listEvent = new ListEvent(true);
        $listEvent->setName('Next year event');
        $listEvent->setDay($yesterday->format('j'));
        $listEvent->setMonth($yesterday->format('n'));

        $refTime = mktime(0, 0, 1, $yesterday->format('n'), $yesterday->format('j'), (int) $yesterday->format('Y') + 1);

        $this->repo->expects($this->once())->method('findAllActive')->willReturn([$listEvent]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $gitList->setBirthDate($now);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Next year event',
            'time' => $refTime,
            'daysLeft' => $now->format('L') ? 364 : 365
        ], $res);
    }

    public function testNextYearBirthday(): void
    {
        $now = \DateTimeImmutable::createFromFormat('U', time());
        $yesterday = $now->sub(new \DateInterval('P1D'));

        $listEventBdate = new ListEvent(true);
        $listEventBdate->setName('Birthday');
        $listEventBdate->setType('birthday');

        $refTime = mktime(0, 0, 1, $yesterday->format('n'), $yesterday->format('j'), (int) $yesterday->format('Y') + 1);

        $this->repo->expects($this->once())->method('findAllActive')->willReturn([$listEventBdate]);
        $listEventManager = new ListEventManager($this->em);
        $gitList = new GiftList();
        $gitList->setBirthDate($yesterday);

        $res = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Birthday',
            'time' => $refTime,
            'daysLeft' =>  $now->format('L') ? 364 : 365
        ], $res);
    }
}
