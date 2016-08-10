<?php

namespace Kraken\RankingBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Kraken\RankingBundle\Entity\Boiler;
use Kraken\RankingBundle\Entity\Category;
use Kraken\RankingBundle\Entity\Manufacturer;
use Kraken\RankingBundle\Entity\Experience;
use Kraken\RankingBundle\Entity\Review;
use Kraken\RankingBundle\Entity\ReviewExperience;
use Kraken\RankingBundle\Entity\Search;
use Kraken\RankingBundle\Form\FileProposalForm;
use Kraken\RankingBundle\Form\ReviewForm;
use Kraken\RankingBundle\Form\SearchForm;
use Kraken\RankingBundle\Form\SearchRejectedForm;

class GeneralController extends BaseController
{
    /**
     * @Route("/", name="ranking_homepage")
     */
    public function homepageAction()
    {
        $form = $this->createForm(new SearchForm(), new Search());

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $latestBoilers = $qb
            ->select('b')
            ->from('KrakenRankingBundle:Boiler', 'b')
            ->where('b.published = 1')
            ->andWhere('b.rating <> :rating')
            ->addOrderBy('b.created', 'DESC')
            ->setParameter('rating', 'Z')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->render('KrakenRankingBundle:Ranking:index.html.twig', ['searchForm' => $form->createView(), 'latestBoilers' => $latestBoilers]);
    }

    /**
     * @Route("/o-rankingu", name="ranking_about")
     */
    public function aboutAction()
    {
        return $this->render('KrakenRankingBundle:Ranking:about.html.twig');
    }

    /**
     * @Route("/kryteria", name="ranking_criteria")
     */
    public function criteriaAction()
    {
        return $this->render('KrakenRankingBundle:Ranking:about.html.twig');
    }

    /**
     * @Route("/propozycje-do-rankingu", name="ranking_proposal")
     */
    public function proposalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new FileProposalForm(), null);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $proposal = $form->getData();
                $em->persist($proposal);
                $em->flush();

                $this->addFlash(
                    'success',
                    'OK. Twoja propozycja dotarła gdzie trzeba i niebawem pojawi się w rankingu.'
                );

                return $this->redirectToRoute('ranking_proposal');
            } else {
                $this->addFlash(
                    'error',
                    'Oj... chyba nie wszystko jest w porządku.'
                );
            }
        }

        return $this->render('KrakenRankingBundle:Ranking:proposal.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/salon-odrzuconych/{uid}", name="ranking_rejected", defaults={"uid" = 0})
     */
    public function rejectedAction($uid, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('post')) {
            $search = new Search();
            $search->setRejected(true);

            $form = $this->createForm(new SearchRejectedForm(), $search);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $searchRecord = $form->getData();

                $em->persist($searchRecord);
                $em->flush();

                return $this->redirectToRoute('ranking_rejected', ['uid' => base_convert($searchRecord->getId(), 10, 36)]);
            }
        }

        if ($uid) {
            $searchRecord = $this->getDoctrine()
                ->getRepository('KrakenRankingBundle:Search')
                ->findOneBy(['id' => intval($uid, 36)]);
        } else {
            $searchRecord = new Search();
            $searchRecord->setRejected(true);
        }

        $form = $this->createForm(new SearchRejectedForm(), $searchRecord);

        $qb = $em->createQueryBuilder();
        $query = $qb
            ->select('b')
            ->from('KrakenRankingBundle:Boiler', 'b')
            ->where('b.rejected = 1')
            ->andWhere('b.published = 1');

        if ($uid) {
            if ($searchRecord->getModelName() != '') {
                $query
                    ->innerJoin('b.manufacturer', 'm')
                    ->andWhere($qb->expr()->orX(
                        $qb->expr()->like('b.name', ':model_name'),
                        $qb->expr()->like('m.name', ':model_name')
                    ))
                    ->setParameter('model_name', '%'.$searchRecord->getModelName().'%');
            }

            if ($searchRecord->getCategory() != '') {
                $categories = array_merge($searchRecord->getCategory()->getChildrenIds(), [$searchRecord->getCategory()->getId()]);

                $query
                    ->andWhere('b.category IN (:categories)')
                    ->setParameter('categories', $categories);
            }

            if ($searchRecord->getManufacturer() != '') {
                $query
                    ->andWhere('b.manufacturer = :manufacturer')
                    ->setParameter('manufacturer', $searchRecord->getManufacturer()->getId());
            }
        }

        $boilers = $query
            ->addOrderBy('b.name')
            ->getQuery()
            ->getResult();

        return $this->render('KrakenRankingBundle:Ranking:rejected.html.twig', ['form' => $form->createView(), 'boilers' => $boilers, 'search' => $searchRecord]);
    }

    /**
     * @Route("/szukaj/{uid}/{sort}", name="ranking_search", defaults={"uid" = 0, "sort" = ""})
     */
    public function searchAction($uid, $sort, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$uid) {
            $form = $this->createForm(new SearchForm(), null, ['vertical' => true]);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $searchRecord = $form->getData();

                if ($searchRecord->isOnlyCategorySelected()) {
                    return $this->redirectToRoute('ranking_boiler_category', ['category' => $searchRecord->getCategory()->getSlug()]);
                }

                if ($searchRecord->isOnlyManufacturerSelected()) {
                    return $this->redirectToRoute('ranking_boiler_manufacturer', ['manufacturer' => $searchRecord->getManufacturer()->getSlug()]);
                }

                $em->persist($searchRecord);
                $em->flush();

                return $this->redirectToRoute('ranking_search', ['uid' => base_convert($searchRecord->getId(), 10, 36)]);
            }
        }

        $searchRecord = $this->getDoctrine()
            ->getRepository('KrakenRankingBundle:Search')
            ->findOneBy(['id' => intval($uid, 36)]);

        $this->get('session')->set('actual_search_slug', $uid);

        $form = $this->createForm(new SearchForm(), $searchRecord, ['vertical' => true]);

        $qb = $em->createQueryBuilder();
        $query = $qb
            ->select('b')
            ->from('KrakenRankingBundle:Boiler', 'b')
            ->andWhere('b.published = 1');

        if ($searchRecord->getModelName() != '') {
            $query
                ->innerJoin('b.manufacturer', 'm')
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('b.name', ':model_name'),
                    $qb->expr()->like('m.name', ':model_name')
                ))
                ->setParameter('model_name', '%'.$searchRecord->getModelName().'%');
        }

        if ($searchRecord->getCategory() != '') {
            $categories = array_merge($searchRecord->getCategory()->getChildrenIds(), [$searchRecord->getCategory()->getId()]);

            $query
                ->andWhere('b.category IN (:categories)')
                ->setParameter('categories', $categories);
        }

        if ($searchRecord->getManufacturer() != '') {
            $query
                ->andWhere('b.manufacturer = :manufacturer')
                ->setParameter('manufacturer', $searchRecord->getManufacturer()->getId());
        }

        if ($searchRecord->getFuelType()->count() > 0) {
            $fuels = [];
            foreach ($searchRecord->getFuelType() as $f) {
                $fuels[] = $f->getId();
            }

            $query
                ->innerJoin('b.boilerFuelTypes', 'f')
                ->andWhere('f.fuelType IN (:fuels)')
                ->setParameter('fuels', $fuels);
        }

        if ($searchRecord->getPower() != '') {
            $query
                ->innerJoin('b.boilerPowers', 'bp');

            if (stristr($searchRecord->getPower(), '+')) {
                $query
                    ->andWhere('bp.power > :power')
                    ->setParameter('power', (int) $searchRecord->getPower());
            } else {
                $query
                    ->andWhere('bp.power <= :power')
                    ->setParameter('power', 1.2 * ((int) $searchRecord->getPower()));
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

        if ($searchRecord->getMaterial() != '') {
            $query
                ->andWhere('b.material = :material')
                ->setParameter('material', $searchRecord->getMaterial());
        }

        if ($searchRecord->isForClosedSystem()) {
            $query
                ->andWhere('b.forClosedSystem = :for_closed_system')
                ->setParameter('for_closed_system', true);
        }

        if ($sort == 'najtansze') {
            $query->addOrderBy('b.typicalModelPrice', 'ASC');
        } elseif ($sort == 'najlepsze') {
            $query->addOrderBy('b.rating', 'ASC');
        } else {
            $query
                ->addOrderBy('b.rating', 'ASC')
                ->addOrderBy('b.typicalModelPrice', 'ASC');
        }

        $boilers = $query
            ->getQuery()
            ->getResult();

        return $this->render('KrakenRankingBundle:Ranking:search.html.twig', ['form' => $form->createView(), 'boilers' => $boilers, 'search' => $searchRecord]);
    }

    /**
     * @Route("/producent/{manufacturer}/{sort}", name="ranking_boiler_manufacturer", defaults={"sort" = ""})
     * @ParamConverter("manufacturer", class="KrakenRankingBundle:Manufacturer", options={"repository_method" = "findOneBySlug"})
     */
    public function manufacturerAction(Manufacturer $manufacturer, $sort)
    {
        $search = new Search();
        $search->setManufacturer($manufacturer);

        $form = $this->createForm(new SearchForm(), $search, ['vertical' => true]);

        $query = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('b')
            ->from('KrakenRankingBundle:Boiler', 'b')
            ->where('b.manufacturer = :manufacturer')
            ->andWhere('b.published = 1')
            ->setParameter('manufacturer', $manufacturer->getId());

        if ($sort == 'najtansze') {
            $query->addOrderBy('b.typicalModelPrice', 'ASC');
        } elseif ($sort == 'najlepsze') {
            $query->addOrderBy('b.rating', 'ASC');
        } else {
            $query
                ->addOrderBy('b.rating', 'ASC')
                ->addOrderBy('b.typicalModelPrice', 'ASC');
        }

        $boilers = $query
            ->getQuery()
            ->getResult();

        return $this->render('KrakenRankingBundle:Ranking:manufacturer.html.twig', ['manufacturer' => $manufacturer, 'boilers' => $boilers, 'form' => $form->createView()]);
    }

    /**
     * @Route("/legacy/{slug}/", name="ranking_legacy_boiler_overview")
     */
    public function legacyBoilerAction($slug)
    {
        $boiler = $this->getDoctrine()
            ->getRepository('KrakenRankingBundle:Boiler')
            ->findOneBy(['slug' => $slug]);

        if (!$boiler) {
            throw $this->createNotFoundException('Lippa');
        }

        return $this->redirectToRoute('ranking_boiler_overview', ['category' => $boiler->getCategory()->getSlug(), 'boiler' => $slug], 301);
    }

    /**
     * @Route("/kotly/{slug}/", name="ranking_legacy_boiler_category")
     */
    public function legacyCategoryAction($slug)
    {
        return $this->redirectToRoute('ranking_boiler_category', ['category' => $slug], 301);
    }

    /**
     * @Route("/{category}/{sort}", name="ranking_boiler_category", defaults={"sort" = ""})
     * @ParamConverter("category", class="KrakenRankingBundle:Category", options={"repository_method" = "findOneBySlug"})
     */
    public function categoryAction(Category $category, $sort)
    {
        $this->get('session')->remove('actual_search_slug');

        $search = new Search();
        $search->setCategory($category);

        $form = $this->createForm(new SearchForm(), $search, ['vertical' => true]);
        $categories = $category->getChildrenIds();
        $categories[] = $category->getId();

        $query = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('b')
            ->from('KrakenRankingBundle:Boiler', 'b')
            ->where('b.published = 1')
            ->andWhere('b.category IN (:categories)')
            ->setParameter('categories', $categories);

        if ($sort == 'najtansze') {
            $query->addOrderBy('b.typicalModelPrice', 'ASC');
        } elseif ($sort == 'najlepsze') {
            $query->addOrderBy('b.rating', 'ASC');
        } else {
            $query
                ->addOrderBy('b.rating', 'ASC')
                ->addOrderBy('b.typicalModelPrice', 'ASC');
        }

        $boilers = $query
            ->getQuery()
            ->getResult();

        return $this->render('KrakenRankingBundle:Ranking:category.html.twig', ['category' => $category, 'boilers' => $boilers, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{category}/kociol/{boiler}", name="ranking_boiler_overview")
     * @ParamConverter("category", class="KrakenRankingBundle:Category", options={"repository_method" = "findOneBySlug"})
     * @ParamConverter("boiler", class="KrakenRankingBundle:Boiler", options={"repository_method" = "findOneBySlug"})
     */
    public function boilerAction(Category $category, Boiler $boiler)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$boiler->isPublished()) {
            throw $this->createNotFoundException('Nie ma takiego kotła :/');
        }

        $templateName = $boiler->isRejected() ? 'boilerRejected' : 'boiler';
        $experiences = $em->getRepository('KrakenRankingBundle:Experience')->findMostConfirmed($boiler);

        return $this->render('KrakenRankingBundle:Ranking:'.$templateName.'.html.twig', [
            'boiler' => $boiler,
            'experiences' => $experiences,
        ]);
    }
}
