<?php

namespace Kraken\RankingBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Kraken\RankingBundle\Entity\Boiler;
use Kraken\RankingBundle\Entity\Experience;
use Kraken\RankingBundle\Entity\Review;
use Kraken\RankingBundle\Entity\ReviewExperience;
use Kraken\RankingBundle\Form\ReviewForm;

class ReviewController extends BaseController
{
    /**
     * @Route("/dodaj-opinie-o-kotle/{boilerId}", name="ranking_review", defaults={"boilerId" = ""})
     */
    public function reviewAction($boilerId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$this->get('session')->has('review_id')) {
            $review = new Review();

            $form = $this->createFormBuilder($review)
                ->add('boiler', 'entity', [
                    'class' => 'KrakenRankingBundle:Boiler',
                    'label' => 'Wybierz kocioł',
                    'placeholder' => '--- wybierz ---',
                    'query_builder' => function (EntityRepository $er) use ($boilerId) {
                        $qb = $er->createQueryBuilder('b')
                            ->where('b.rejected = false')
                            ->andWhere('b.published = true')
                            ->orderBy('b.name', 'ASC');

                        if ($boilerId) {
                            $qb->andWhere('b.id = :boilerId')
                                ->setParameter('boilerId', $boilerId)
                            ;
                        }

                        return $qb;
                    },
                ])
                ->add('email', 'text', [
                    'label' => 'Twój adres e-mail',
                ])
                ->getForm();
            $form->handleRequest($request);

            if ($form->isValid()) {
                $reviewsPerEmail = $this->getDoctrine()->getRepository('KrakenRankingBundle:Review')->findByEmail($form->get('email')->getData());

                if (count($reviewsPerEmail) > 1) {
                    $this->addFlash('error', 'Z tego adresu e-mail dodane zostały już opinie o dwóch różnych kotłach.');

                    return $this->redirectToRoute('ranking_review');
                }

                $em->persist($review);
                $em->flush();

                $this->get('session')->set('review_id', $review->getId());

                return $this->redirectToRoute('ranking_review');
            }

            return $this->render('KrakenRankingBundle:Ranking:review.html.twig', [
                'form' => $form->createView(),
                'selectedBoiler' => false,
            ]);
        } else {
            $review = $em->getRepository('KrakenRankingBundle:Review')->find($this->get('session')->get('review_id'));

            $form = $this->createForm(new ReviewForm(), $review, ['boiler_id' => $review->getBoiler()->getId()]);

            $form->handleRequest($request);

            if ($form->isValid()) {
                $review->setIp($_SERVER['REMOTE_ADDR']);
                $review->setUserAgent($_SERVER['HTTP_USER_AGENT']);

                $em->persist($review);
                $em->flush();

                $this->get('session')->remove('review_id');

                $this->addFlash(
                    'success',
                    'OK. Twoja opinia została przesłana do poczekalni. Dostaniesz wiadomość jak tylko wejdzie do zestawienia lub skontaktujemy się z tobą gdyby były jakieś wątpliwości.'
                );

                return $this->redirectToRoute('ranking_review');
            }

            return $this->render('KrakenRankingBundle:Ranking:review.html.twig', [
                'form' => $form->createView(),
                'selectedBoiler' => $review->getBoiler(),
            ]);
        }
    }

    /**
     * @Route("/anuluj-opinie-o-kotle/{review}", name="ranking_review_revoke")
     */
    public function revokeReviewAction(Review $review, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $review->setRevoked(true);
        $em->persist($review);
        $em->flush();

        $this->addFlash(
            'success',
            'OK. Twoja opinia została usunięta z publicznego podsumowania.'
        );

        return $this->redirectToRoute('ranking_review');
    }
}
