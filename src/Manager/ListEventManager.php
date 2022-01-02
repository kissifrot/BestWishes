<?php

namespace BestWishes\Manager;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\ListEvent;
use Doctrine\ORM\EntityManagerInterface;

class ListEventManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return bool|mixed
     */
    public function getNearestEventData(GiftList $list)
    {
        $calculatedEvents = $this->getNearestEvents($list->getBirthDate());
        if (empty($calculatedEvents)) {
            return false;
        }

        $nextEventData = reset($calculatedEvents);
        // Today at 00:00:01
        $currentTime = \DateTimeImmutable::createFromFormat('U', time())->setTime(0, 0, 1)->getTimestamp();
        $timeLeft = $nextEventData['time'] - $currentTime;
        $daysLeft = round($timeLeft / 3600 / 24);
        $nextEventData['daysLeft'] = (int)$daysLeft;

        return $nextEventData;
    }

    /**
     * Get the nearest events of a list
     */
    private function getNearestEvents(\DateTimeImmutable $birthDate = null): array
    {
        if (null === $birthDate) {
            return [];
        }

        $activeEvents = $this->getAllActiveEvents();
        $todayAtMidnight = \DateTimeImmutable::createFromFormat('U', time())->setTime(0, 0, 1);
        $currentTime = $todayAtMidnight->getTimestamp();
        // First update the "birthday" event with this list's birthdate
        foreach ($activeEvents as $activeEvent) {
            if ($activeEvent->isBirthday()) {
                $activeEvent->setDay($birthDate->format('j'));
                $activeEvent->setMonth($birthDate->format('n'));
                break;
            }
        }

        // Next create the dates corresponding to current year's events and next year's ones
        $calculatedEvents = [];
        foreach ($activeEvents as $activeEvent) {
            $currentYear = $activeEvent->getYear() ?? (int) $todayAtMidnight->format('Y');
            $currentMonth = $activeEvent->getMonth() ?? (int) $todayAtMidnight->format('n');
            $currentYearEvent = $todayAtMidnight->setDate($currentYear, $currentMonth, $activeEvent->getDay())->getTimestamp();
            if ($currentYearEvent >= $currentTime) {
                $calculatedEvents[] = [
                    'name' => $activeEvent->getName(),
                    'time' => $currentYearEvent
                ];
            }
            if ($activeEvent->isPermanent()) {
                $nextYearEvent = $todayAtMidnight->setDate($currentYear + 1, $currentMonth, $activeEvent->getDay())->getTimestamp();
                if ($nextYearEvent >= $currentTime) {
                    $calculatedEvents[] = [
                        'name' => $activeEvent->getName(),
                        'time' => $nextYearEvent
                    ];
                }
            }
        }

        // And finally sort them
        usort($calculatedEvents, static function ($a, $b) {
            return $a['time'] <=> $b['time'];
        });

        return $calculatedEvents;
    }

    /**
     * Get a list of all active events
     * @return ListEvent[]
     */
    private function getAllActiveEvents(): array
    {
        return $this->em->getRepository(ListEvent::class)->findAllActive();
    }
}
