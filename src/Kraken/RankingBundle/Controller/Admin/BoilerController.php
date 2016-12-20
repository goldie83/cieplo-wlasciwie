<?php

namespace Kraken\RankingBundle\Controller\Admin;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BoilerController extends Controller
{
    public function reviewsAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $object->getId()));
        }

        $this->addFlash('sonata_flash_success', 'Reviews done!');

        return $this->render('KrakenRankingBundle:Admin:boiler_reviews.html.twig', ['boiler' => $object]);
    }
}
