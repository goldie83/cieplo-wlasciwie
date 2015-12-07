<?php

namespace Kraken\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Kraken\RankingBundle\Entity\Boiler;
use Kraken\RankingBundle\Entity\Category;
use Kraken\RankingBundle\Entity\Search;
use Kraken\RankingBundle\Form\SearchForm;


class GeneralController extends BaseController
{
    /**
     * @Route("/", name="ranking_homepage")
     */
    public function homepageAction()
    {
        $form = $this->createForm(new SearchForm());

        return $this->render('KrakenRankingBundle:Ranking:index.html.twig', ['searchForm' => $form->createView()]);
    }

    /**
     * @Route("/szukaj/{uid}", name="ranking_search", defaults={"uid" = 0})
     */
    public function searchAction($uid, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($uid == 0) {
            $form = $this->createForm(new SearchForm(), null);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $searchRecord = $form->getData();
                $em->persist($searchRecord);
                $em->flush();

                return $this->redirectToRoute('ranking_search', ['uid' => base_convert($searchRecord->getId(), 10, 36)]);
            }
        }

        $searchRecord = $this->getDoctrine()
            ->getRepository('KrakenRankingBundle:Search')
            ->findOneBy(['id' => intval($uid, 36)]);
        $form = $this->createForm(new SearchForm(), $searchRecord);

        $query = $em
            ->createQueryBuilder()
            ->select('b')
            ->from('KrakenRankingBundle:Boiler', 'b');

        if ($searchRecord->getModelName() != '') {
            $query
                ->andWhere('b.name LIKE :model_name')
                ->setParameter('model_name', '%'.$searchRecord->getModelName().'%');
        }

        if ($searchRecord->getCategory() != '') {
            $query
                ->andWhere('b.category = :category')
                ->setParameter('category', $searchRecord->getCategory()->getId());
        }

        if ($searchRecord->getFuelType() != '') {
            $fuels = [];
            foreach ($searchRecord->getFuelType() as $f) {
                $fuels[] = $f->getId();
            }

            $query
                ->innerJoin('b.acceptedFuelTypes', 'f')
                ->andWhere('f.id IN (:fuels)')
                ->setParameter('fuels', $fuels);
        }

        if ($searchRecord->getPower() != '') {
            $query
                ->innerJoin('b.boilerPowers', 'bp');

            if (stristr($searchRecord->getPower(), '+')) {
                $query
                    ->andWhere('bp.power > :power')
                    ->setParameter('power', (int) $searchRecord->getPower());
            }
        }

        if ($searchRecord->getNormClass() != '') {
            $query
                ->andWhere('b.normClass = :class')
                ->setParameter('class', $searchRecord->getNormClass());
        }

        if ($searchRecord->getRating() != '') {
            $query
                ->andWhere('b.rating = :rating')
                ->setParameter('rating', $searchRecord->getRating());
        }

        if ($searchRecord->isForClosedSystem()) {
            $query
                ->andWhere('b.forClosedSystem = :for_closed_system')
                ->setParameter('for_closed_system', $searchRecord->isForClosedSystem());
        }

        $boilers = $query
            ->getQuery()
            ->getResult();

        return $this->render('KrakenRankingBundle:Ranking:search.html.twig', ['form' => $form->createView(), 'boilers' => $boilers, 'search' => $searchRecord]);
    }

    /**
     * @Route("/{category}/", name="ranking_boiler_category")
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
     * @Route("/{category}/{boiler}", name="ranking_boiler_overview")
     * @ParamConverter("category", class="KrakenRankingBundle:Category", options={"repository_method" = "findOneBySlug"})
     * @ParamConverter("boiler", class="KrakenRankingBundle:Boiler", options={"repository_method" = "findOneBySlug"})
     */
    public function boilerAction(Category $category, Boiler $boiler)
    {
        return $this->render('KrakenRankingBundle:Ranking:boiler.html.twig', ['boiler' => $boiler]);
    }

    /**
     * @Route("/kotly/{slug}/", name="ranking_legacy_boiler_category")
     */
    public function legacyCategoryAction($slug)
    {
        return $this->redirectToRoute('boiler_category', ['category' => $slug], 301);
    }
}
