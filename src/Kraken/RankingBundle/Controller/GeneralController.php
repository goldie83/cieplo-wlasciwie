<?php

namespace Kraken\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

use Kraken\RankingBundle\Entity\Boiler;
use Kraken\RankingBundle\Entity\Category;
use Kraken\RankingBundle\Entity\Search;
use Kraken\RankingBundle\Form\SearchForm;


class GeneralController extends BaseController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction()
    {
        $form = $this->createForm(new SearchForm());

        return $this->render('KrakenRankingBundle:Ranking:index.html.twig', ['searchForm' => $form->createView()]);
    }

    /**
     * @Route("/{category}/", name="boiler_category")
     * @ParamConverter("category", class="KrakenRankingBundle:Category", options={"repository_method" = "findOneBySlug"})
     */
    public function categoryAction(Category $category)
    {
        $search = new Search;
        $search->setCategory($category);

        $form = $this->createForm(new SearchForm(), $search);

        return $this->render('KrakenRankingBundle:Ranking:category.html.twig', ['category' => $category, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{category}/{boiler}", name="boiler_overview")
     * @ParamConverter("category", class="KrakenRankingBundle:Category", options={"repository_method" = "findOneBySlug"})
     * @ParamConverter("boiler", class="KrakenRankingBundle:Boiler", options={"repository_method" = "findOneBySlug"})
     */
    public function boilerAction(Category $category, Boiler $boiler)
    {
        return $this->render('KrakenRankingBundle:Ranking:boiler.html.twig', ['boiler' => $boiler]);
    }

    /**
     * @Route("/kotly/{slug}/", name="legacy_boiler_category")
     */
    public function legacyCategoryAction($slug)
    {
        return $this->redirectToRoute('boiler_category', ['category' => $slug], 301);
    }
}
