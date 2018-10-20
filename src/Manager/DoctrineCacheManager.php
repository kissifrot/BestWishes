<?php

namespace BestWishes\Manager;


use BestWishes\Entity\GiftList;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCacheManager
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function clearGiftListCache(GiftList $list): void
    {
        if (!$cacheDriver = $this->em->getConfiguration()->getResultCacheImpl()) {
            return;
        }
        $cacheKeys = [
            sprintf('giftlist_full_slug_%s', $list->getSlug()),
            sprintf('giftlist_full_%u', $list->getId()),
            sprintf('giftlist_full_surpr_excl_%u', $list->getId()),
        ];
        foreach ($cacheKeys as $cacheKey) {
            $cacheDriver->delete($cacheKey);
        }
    }
}
