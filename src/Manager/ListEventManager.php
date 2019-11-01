<?php

namespace BestWishes\Manager;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\ListEvent;
use Doctrine\ORM\EntityManagerInterface;

class ListEventManager
{
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param GiftList $list
     *
     * @return bool|mixed
     */
    public function getNearestEventData(GiftList $list)
    {
        $calculatedEvents = $this->getNearestEvents($list->getBirthDate());
        if (empty($calculatedEvents)) {
            return false;
        }

        $nextEventData = end($calculatedEvents);
        // Today at 00:01:01
        $currentTime = mktime(0, 1, 1);
        $timeLeft = $nextEventData['time'] - $currentTime;
        $daysLeft = round($timeLeft / 3600 / 24);
        $nextEventData['daysLeft'] = (int)$daysLeft;

        return $nextEventData;
    }

    /**
     * Get the nearest events of a list
     *
     * @param \DateTime $birthDate
     *
     * @return array
     */
    private function getNearestEvents(\DateTime $birthDate = null): array
    {
        if (null === $birthDate) {
            return [];
        }

        $activeEvents = $this->getAllActiveEvents();
        // Today at 00:01:01
        $currentTime = mktime(0, 1, 1);
        // First update the "birthday" event with this list's birthdate
        /** @var ListEvent $activeEvent */
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
            $currentYear = $activeEvent->getYear() ?? date('Y');
            $currentMonth = $activeEvent->getMonth() ?? date('n');
            $currentYearEvent = mktime(0, 1, 1, $currentMonth, $activeEvent->getDay(), $currentYear);
            if ($currentYearEvent >= $currentTime) {
                $calculatedEvents[] = [
                    'name' => $activeEvent->getName(),
                    'time' => $currentYearEvent
                ];
            }
            if ($activeEvent->isPermanent()) {
                $nextYearEvent = mktime(0, 1, 1, $currentMonth, $activeEvent->getDay(), $currentYear + 1);
                if ($nextYearEvent >= $currentTime) {
                    $calculatedEvents[] = [
                        'name' => $activeEvent->getName(),
                        'time' => $nextYearEvent
                    ];
                }
            }
        }

        // And finally sort them
        usort($calculatedEvents, function ($a, $b) {
            return $a['time'] <=> $b['time'];
        });

        return $calculatedEvents;
    }

    /**
     * Get a list of all active events
     * @return array
     */
    private function getAllActiveEvents(): array
    {
        return $this->em->getRepository(ListEvent::class)->findAllActive();
    }
}
