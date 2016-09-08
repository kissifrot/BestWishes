<?php

namespace AppBundle\Manager;


use Doctrine\ORM\EntityManager;
use AppBundle\Entity\ListEvent;
use AppBundle\Entity\GiftList;

/**
 * Class ListEventManager
 * @package AppBundle\Manager
 */
class ListEventManager
{
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
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
        $currentTime = mktime(0, 1, 1); // Today at 00:01:01
        $timeLeft = $nextEventData['time'] - $currentTime;
        $daysLeft = round($timeLeft / 3600 / 24);
        $nextEventData['daysLeft'] = intval($daysLeft);

        return $nextEventData;
    }

    /**
     * Get the nearest events of a birthdate
     *
     * @param \DateTime $birthDate
     *
     * @return array|bool
     */
    private function getNearestEvents(\DateTime $birthDate = null)
    {
        if (null === $birthDate) {
            return false;
        }

        $activeEvents = $this->getAllActiveEvents();
        $currentTime = mktime(0, 1, 1); // Today at 00:01:01
        // First update the "birthday" event with this list's birthdate
        /** @var ListEvent $activeEvent */
        foreach ($activeEvents as $activeEvent) {
            if ($activeEvent->getType() === 'birthday') {
                $activeEvent->setDay($birthDate->format('j'))->setMonth($birthDate->format('n'));
                break;
            }
        }

        // Next create the dates corresponding to current year's events and next year's ones
        $calculatedEvents = [];
        foreach ($activeEvents as $activeEvent) {
            $currentYear = $activeEvent->getYear();
            if (empty($currentYear)) {
                $currentYear = date('Y');
            }
            $currentMonth = $activeEvent->getMonth();
            if (empty($currentMonth)) {
                $currentMonth = date('n');
            }
            $currentYearEvent = mktime(0, 1, 1, $currentMonth, $activeEvent->getDay(), $currentYear);
            if ($currentYearEvent >= $currentTime) {
                $calculatedEvents[] = [
                    'name' => $activeEvent->getName(),
                    'time' => $currentYearEvent
                ];
            }
            if ($activeEvent->isPermanent()) {
                $nextYearEvent = mktime(0, 1, 1, $currentMonth, $activeEvent->getDay(), ($currentYear + 1));
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
            if ($a['time'] === $b['time']) {
                return 0;
            }

            return ($a['time'] > $b['time']) ? -1 : 1;
        });

        return $calculatedEvents;
    }

    /**
     * Get a list of all active events
     * @return array
     */
    private function getAllActiveEvents()
    {
        $activeListEvents = $this->em->getRepository('AppBundle:ListEvent')->findAllActive();

        return $activeListEvents;
    }
}
