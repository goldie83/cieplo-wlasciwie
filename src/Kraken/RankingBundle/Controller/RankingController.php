<?php

namespace Kraken\RankingBundle\Controller;

use Kraken\RankingBundle\Form\SearchForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RankingController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $searchForm = $this->createForm(new SearchForm());

        return $this->render('KrakenRankingBundle:Ranking:index.html.twig', array(
            'searchForm' => $searchForm->createView(),
        ));
    }
}
