<?php

namespace BestWishes\Tests\Manager;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\ListEvent;
use BestWishes\Manager\ListEventManager;
use BestWishes\Repository\ListEventRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * @group time-sensitive
 */
class ListEventManagerTest extends TestCase
{
    private MockObject|ListEventRepository $listEventRepository;

    public function setUp(): void
    {
        date_default_timezone_set('UTC');

        $this->listEventRepository = $this->createMock(ListEventRepository::class);
    }

    public function testNoActiveEvents(): void
    {
        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([]);
        $listEventManager = new ListEventManager($this->listEventRepository);
        $gitList = new GiftList();
        $today = \DateTimeImmutable::createFromFormat('U', (string) time());
        $gitList->setBirthDate($today);

        $result = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals(false, $result);
    }

    public function testTodayBirthdayEvent(): void
    {
        ClockMock::withClockMock(strtotime('2019-11-05 20:00:00'));
        ClockMock::register(ListEventManager::class);

        $now = \DateTimeImmutable::createFromFormat('U', (string) time());

        $listEventBirthdate = new ListEvent();
        $listEventBirthdate->setName('Birthday');
        $listEventBirthdate->setType('birthday');
        $refTime = mktime(0, 0, 1, $now->format('n'), $now->format('j'), $now->format('Y'));
        $today = $now->setTime(0, 0, 1);

        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([$listEventBirthdate]);
        $listEventManager = new ListEventManager($this->listEventRepository);
        $gitList = new GiftList();
        $gitList->setBirthDate($today);

        $result = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Birthday',
            'time' => $refTime,
            'daysLeft' => 0
        ], $result);
    }

    public function testChristmasEvent(): void
    {
        ClockMock::withClockMock(strtotime('2019-11-05 20:00:00'));
        ClockMock::register(ListEventManager::class);

        $listEventChristmas = new ListEvent();
        $listEventChristmas->setName('Christmas');
        $listEventChristmas->setDay(25);
        $listEventChristmas->setMonth(12);
        $today = \DateTimeImmutable::createFromFormat('U', (string) time())->setTime(0, 0, 1);

        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([$listEventChristmas]);
        $listEventManager = new ListEventManager($this->listEventRepository);
        $gitList = new GiftList();
        $gitList->setBirthDate($today);

        $result = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Christmas',
            'time' => 1577232001,
            'daysLeft' => 50
        ], $result);
    }

    public function testTwoEventsToday(): void
    {
        ClockMock::withClockMock(strtotime('2019-11-05 20:00:00'));
        ClockMock::register(ListEventManager::class);

        $now = \DateTimeImmutable::createFromFormat('U', (string) time());

        $listEventBirthdate = new ListEvent();
        $listEventBirthdate->setName('Birthday');
        $listEventBirthdate->setType('birthday');

        $listEventToday = new ListEvent();
        $listEventToday->setName('An event of today');
        $listEventToday->setDay((int) $now->format('j'));
        $listEventToday->setMonth((int) $now->format('n'));
        $listEventToday->setYear((int) $now->format('Y'));

        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([$listEventBirthdate, $listEventToday]);
        $listEventManager = new ListEventManager($this->listEventRepository);
        $gitList = new GiftList();
        $gitList->setBirthDate($now);

        $result = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Birthday',
            'time' => 1572912001,
            'daysLeft' => 0
        ], $result);
    }

    public function testTwoNextEvents(): void
    {
        $now = \DateTimeImmutable::createFromFormat('U', (string) time());
        $inThreeMonths = $now->add(new \DateInterval('P3M'));
        $tomorrow = $now->add(new \DateInterval('P1D'));
        $afterTomorrow = $now->add(new \DateInterval('P2D'));

        $listEventBirthdate = new ListEvent(true);
        $listEventBirthdate->setName('Birthday');
        $listEventBirthdate->setType('birthday');

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

        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([$listEventBirthdate, $listEventAfterTomorrow, $listEventTomorrow]);
        $listEventManager = new ListEventManager($this->listEventRepository);
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
        $now = \DateTimeImmutable::createFromFormat('U', (string) time());
        $nextThreeDays = $now->add(new \DateInterval('P3D'));

        $listEvent = new ListEvent();
        $listEvent->setName('Next three days event');
        $listEvent->setDay($nextThreeDays->format('j'));
        $listEvent->setMonth($nextThreeDays->format('n'));
        $listEvent->setYear($nextThreeDays->format('Y'));

        $refTime = mktime(0, 0, 1, $nextThreeDays->format('n'), $nextThreeDays->format('j'), $nextThreeDays->format('Y'));

        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([$listEvent]);
        $listEventManager = new ListEventManager($this->listEventRepository);
        $gitList = new GiftList();
        $gitList->setBirthDate($now);

        $result = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Next three days event',
            'time' => $refTime,
            'daysLeft' => 3
        ], $result);
    }

    public function testYesterdayEvent(): void
    {
        ClockMock::withClockMock(strtotime('2019-11-05 20:00:00'));

        $now = \DateTimeImmutable::createFromFormat('U', (string) time());
        $yesterday = $now->sub(new \DateInterval('P1D'));

        $listEvent = new ListEvent();
        $listEvent->setName('Yesterday event');
        $listEvent->setDay($yesterday->format('j'));
        $listEvent->setMonth($yesterday->format('n'));
        $listEvent->setYear($yesterday->format('Y'));

        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([$listEvent]);
        $listEventManager = new ListEventManager($this->listEventRepository);
        $gitList = new GiftList();
        $gitList->setBirthDate($now);

        $result = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals(false, $result);
    }

    public function testNextYearEvent(): void
    {
        $now = \DateTimeImmutable::createFromFormat('U', (string) time());
        $yesterday = $now->sub(new \DateInterval('P1D'));

        $listEvent = new ListEvent(true);
        $listEvent->setName('Next year event');
        $listEvent->setDay($yesterday->format('j'));
        $listEvent->setMonth($yesterday->format('n'));

        $refTime = mktime(0, 0, 1, $yesterday->format('n'), $yesterday->format('j'), (int) $yesterday->format('Y') + 1);

        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([$listEvent]);
        $listEventManager = new ListEventManager($this->listEventRepository);
        $gitList = new GiftList();
        $gitList->setBirthDate($now);

        $refDatetime = \DateTimeImmutable::createFromFormat('U', $refTime);

        $result = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Next year event',
            'time' => $refTime,
            'daysLeft' => '1' === $refDatetime->format('L') ? 365 : 364
        ], $result);
    }

    public function testNextYearBirthday(): void
    {
        $now = \DateTimeImmutable::createFromFormat('U', (string) time());
        $yesterday = $now->sub(new \DateInterval('P1D'));

        $listEventBirthdate = new ListEvent(true);
        $listEventBirthdate->setName('Birthday');
        $listEventBirthdate->setType('birthday');

        $refTime = mktime(0, 0, 1, $yesterday->format('n'), $yesterday->format('j'), (int) $yesterday->format('Y') + 1);

        $this->listEventRepository->expects($this->once())->method('findAllActive')->willReturn([$listEventBirthdate]);
        $listEventManager = new ListEventManager($this->listEventRepository);
        $gitList = new GiftList();
        $gitList->setBirthDate($yesterday);

        $refDatetime = \DateTimeImmutable::createFromFormat('U', $refTime);

        $result = $listEventManager->getNearestEventData($gitList);
        $this->assertEquals([
            'name' => 'Birthday',
            'time' => $refTime,
            'daysLeft' => '1' === $refDatetime->format('L') ? 365 : 364
        ], $result);
    }
}
