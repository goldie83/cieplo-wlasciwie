<?php

namespace Kraken\WarmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StaticController extends Controller
{
    public function landingAction()
    {
        return $this->render('KrakenWarmBundle:Static:landing.html.twig');
    }

    public function howItWorksAction()
    {
        return $this->render('KrakenWarmBundle:Static:howItWorks.html.twig');
    }

    public function whyNotWorksAction()
    {
        return $this->render('KrakenWarmBundle:Static:whyNotWorks.html.twig');
    }

    public function rulesAction()
    {
        return $this->render('KrakenWarmBundle:Static:rules.html.twig');
    }

    public function whatAction()
    {
        return $this->render('KrakenWarmBundle:Static:what.html.twig');
    }
}
