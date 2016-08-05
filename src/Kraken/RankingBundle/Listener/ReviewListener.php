<?php

namespace Kraken\RankingBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Kraken\RankingBundle\Entity\Review;
use Kraken\RankingBundle\Entity\ReviewSummary;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReviewListener implements EventSubscriber
{
    private $mailer;

    public function __construct(ContainerInterface $container)
    {
        $this->mailer = $container->get('mailer');
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->updateReviewSummary($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->updateReviewSummary($args);
    }

    public function updateReviewSummary(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$entity instanceof Review) {
            return;
        }

        $boiler = $entity->getBoiler();
        $actualReview = $em->getRepository('KrakenRankingBundle:Review')->find($entity->getId());

        if ($actualReview && !$actualReview->isAccepted() && $entity->isAccepted()) {
            $mailBody = <<<BODY
Witaj<br>
<br>
Twoja opinia o kotle %s została uwzględniona w publicznym podsumowaniu.<br>
Adres strony z kotłem to: http://ranking.czysteogrzewanie.pl/%s/%s<br>
Jeśli kiedyś zechcesz usunąć swoją opinię: http://ranking.czysteogrzewanie.pl/%s/%s<br>
<br>
--<br>
CzysteOgrzewanie.pl
BODY;
            $message = \Swift_Message::newInstance()
                ->setSubject(sprintf('Twoja opinia o kotle %s została opublikowana', $boiler->getName()))
                ->setFrom([$this->getParameter('mailer_user') => 'CieploWlasciwie.pl'])
                ->setTo($entity->getEmail())
                ->setContentType('text/html')
                ->setBody(sprintf($mailBody,
                    $boiler->getName(),
                    $boiler->getCategory()->getSlug(),
                    $boiler->getSlug(),
                    'anuluj-opinie-o-kotle',
                    $entity->getId()
                ))
            ;
            $this->mailer->send($message);
        }

        $summary = $boiler->getReviewSummary();

        if (!$summary) {
            $summary = new ReviewSummary();
        }

        $reviews = $em->getRepository('KrakenRankingBundle:Review')->findBy(['boiler' => $boiler, 'accepted' => true]);
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

        $boiler->setReviewSummary($summary);

        $em->persist($entity);
        $em->flush();
    }
}
