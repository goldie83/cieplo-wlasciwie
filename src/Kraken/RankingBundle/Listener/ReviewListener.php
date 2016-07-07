<?php

namespace Kraken\RankingBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Kraken\RankingBundle\Entity\Review;
use Kraken\RankingBundle\Entity\ReviewSummary;

class ReviewListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->updateReviewSummary($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateReviewSummary($args);
    }

    public function updateReviewSummary(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Review) {
            return;
        }

        //TODO powiadamiać autora po publikacji opinii

        $em = $args->getEntityManager();

        $summary = $entity->getBoiler()->getReviewSummary();

        if (!$summary) {
            $summary = new ReviewSummary();
        }

        $reviews = $em->getRepository('KrakenRankingBundle:Review')->findBy(['boiler' => $entity->getBoiler(), 'accepted' => true]);
        $reviewsNumber = count($reviews);

        if ($reviewsNumber == 0) {
            return;
        }

        $ratings = [
            'general' => 0,
            'quality' => 0,
            'warranty' => 0,
            'operation' => 0,
        ];
        $warrantyReviewsNumber = 0;

        foreach ($reviews as $r) {
            $ratings['general'] += $r->getRating();
            $ratings['quality'] += $r->getQualityRating();
            $ratings['warranty'] += $r->getWarrantyRating();
            $ratings['operation'] += $r->getOperationRating();

            if ($r->getWarrantyRating() > 0) {
                $warrantyReviewsNumber++;
            }
        }

        $summary->setReviewsNumber($reviewsNumber);
        $summary->setWarrantyReviewsNumber($warrantyReviewsNumber);
        $summary->setRating($ratings['general']/$reviewsNumber);
        $summary->setQualityRating($ratings['quality']/$reviewsNumber);
        $summary->setWarrantyRating($ratings['warranty']/$reviewsNumber);
        $summary->setOperationRating($ratings['operation']/$reviewsNumber);

        $entity->getBoiler()->setReviewSummary($summary);

        $em->persist($entity);
        $em->flush();
    }
}
