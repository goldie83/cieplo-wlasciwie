<?php

namespace Kraken\WarmBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kraken\WarmBundle\Form\CalculationFormType;
use Kraken\WarmBundle\Form\CalculationStepDimensionsType;
use Kraken\WarmBundle\Form\CalculationStepLocationType;
use Kraken\WarmBundle\Form\CalculationStepWallsType;
use Kraken\WarmBundle\Form\CalculationStepOneType;
use Kraken\WarmBundle\Form\HouseApartmentType;
use Kraken\WarmBundle\Form\HouseType;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Wall;
use Kraken\WarmBundle\Entity\Layer;

class CalculatorController extends Controller
{
    public function startAction($slug = null, Request $request)
    {
        $calc = null;

        if ($slug) {
            if (!$this->userIsAuthor($slug, $request)) {
                throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
            }

            $calc = $this->getDoctrine()
                ->getRepository('KrakenWarmBundle:Calculation')
                ->findOneBy(array('id' => intval($slug, 36)));
        }

        if (!$calc) {
            $calc = Calculation::create();
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new CalculationStepOneType(), $calc);

        if ($request->isMethod('post')) {

            $form->bind($request);

            if ($form->isValid()) {
                $calculation = $form->getData();
                $isEditing = $calculation->getId() != null;

                $calculation->setHeatedArea(null); // to reassign city & recalculate cached values
                $em->persist($calculation);
                $em->flush();

                $calcSlug = base_convert($calculation->getId(), 10, 36);
                $redirect = $this->generateUrl('location', array(
                    'slug' => $calcSlug,
                ));

                if (!$isEditing) {
                    $cookieValue = $request->cookies->get('sup_bro');
                    $slugs = explode(';', $cookieValue);

                    if (!in_array($calcSlug, $slugs)) {
                        $slugs[] = $calcSlug;
                    }

                    $response = new RedirectResponse($redirect);
                    $cookie = new Cookie('sup_bro', implode(';', $slugs), time() + 3600 * 24 * 365);
                    $response->headers->setCookie($cookie);

                    return $response;
                }

                return $this->redirect($redirect);
            }
        }

        return $this->render('KrakenWarmBundle:Calculator:start.html.twig', array(
            'calc' => $calc,
            'form' => $form->createView(),
        ));
    }

    public function locationAction($slug, Request $request)
    {
        if (!$this->userIsAuthor($slug, $request)) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->findOneBy(array('id' => intval($slug, 36)));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CalculationStepLocationType(), $calc);

        if ($request->isMethod('post')) {
            $form->bind($request);

            if ($form->isValid()) {
                $obj = $form->getData();

                $obj->setHeatedArea(null); // to reassign city & recalculate cached values
                $em->persist($obj);
                $em->flush();

                $calcSlug = base_convert($obj->getId(), 10, 36);
                $redirect = $this->generateUrl('dimensions', array(
                    'slug' => $calcSlug,
                ));

                return $this->redirect($redirect);
            }
        }

        return $this->render('KrakenWarmBundle:Calculator:location.html.twig', array(
            'calc' => $calc,
            'form' => $form->createView(),
        ));
    }

    public function dimensionsAction($slug, Request $request)
    {
        if (!$this->userIsAuthor($slug, $request)) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->findOneBy(array('id' => intval($slug, 36)));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CalculationStepDimensionsType(), $calc->getHouse());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $house = $form->getData();

            $em->persist($house);
            $calc->setHouse($house);
            $em->persist($calc);
            $em->flush();

            $calcSlug = base_convert($calc->getId(), 10, 36);
            $redirect = $this->generateUrl('walls', array(
                'slug' => $calcSlug,
            ));

            return $this->redirect($redirect);
        }

        return $this->render('KrakenWarmBundle:Calculator:dimensions.html.twig', array(
            'calc' => $calc,
            'form' => $form->createView(),
        ));
    }

    public function wallsAction($slug, Request $request)
    {
        if (!$this->userIsAuthor($slug, $request)) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->findOneBy(array('id' => intval($slug, 36)));

        $em = $this->getDoctrine()->getManager();

        $house = $calc->getHouse();

        if ($house->getConstructionType() == '') {
            $house->setConstructionType('traditional');
            $house->setWindowsType('new_double_glass');
            $house->setDoorsType('new_wooden');
        }

        $form = $this->createForm(new CalculationStepWallsType(), $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $house = $form->getData();

            $em->persist($house);
            $calc->setHouse($house);
            $em->persist($calc);
            $em->flush();

            $calcSlug = base_convert($calc->getId(), 10, 36);
            $redirect = $this->generateUrl('walls', array(
                'slug' => $calcSlug,
            ));

            return $this->redirect($redirect);
        }

        return $this->render('KrakenWarmBundle:Calculator:walls.html.twig', array(
            'calc' => $calc,
            'form' => $form->createView(),
        ));
    }

    protected function userIsAuthor($slug, Request $request)
    {
        $cookieValue = $request->cookies->get('sup_bro');
        $slugs = explode(';', $cookieValue);

        return in_array($slug, $slugs);
    }

    public function detailsAction($slug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->findOneBy(array('id' => intval($slug, 36)));

        if (!$calc || !$this->userIsAuthor($slug, $request)) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $isEditing = $calc->getHouse() != null;
        $buildingType = $calc->getBuildingType();
        $template = 'single_house';
        $house = $isEditing
            ? $calc->getHouse()
            : House::create();

        if (in_array($buildingType, array('single_house', 'double_house', 'row_house'))) {
            $form = $this->createForm(new HouseType(), $house);
        } else {
            $template = 'apartment';
            $form = $this->createForm(new HouseApartmentType(), $house);
        }

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $house = $form->getData();

                //TODO that's the only relation which produces empty Layer record. Dafuq.
                if (!$house->getRoofIsolationLayer()) {
                    $house->setRoofIsolationLayer(null);
                }

                //TODO what the hell?
                if ($house->getGroundFloorIsolationLayer()) {
                    if (!$house->getGroundFloorIsolationLayer()->getMaterial() || !$house->getGroundFloorIsolationLayer()->getSize()) {
                        $em->remove($house->getGroundFloorIsolationLayer());
                        $house->setGroundFloorIsolationLayer(null);
                    }
                }

                if ($house->getHighestCeilingIsolationLayer()) {
                    if (!$house->getHighestCeilingIsolationLayer()->getMaterial() || !$house->getHighestCeilingIsolationLayer()->getSize()) {
                        $em->remove($house->getHighestCeilingIsolationLayer());
                        $house->setHighestCeilingIsolationLayer(null);
                    }
                }

                if ($house->getRoofIsolationLayer()) {
                    if (!$house->getRoofIsolationLayer()->getMaterial() || !$house->getRoofIsolationLayer()->getSize()) {
                        $em->remove($house->getRoofIsolationLayer());
                        $house->setRoofIsolationLayer(null);
                    }
                }

                if ($house->getBasementFloorIsolationLayer()) {
                    if (!$house->getBasementFloorIsolationLayer()->getMaterial() || !$house->getBasementFloorIsolationLayer()->getSize()) {
                        $em->remove($house->getBasementFloorIsolationLayer());
                        $house->setBasementFloorIsolationLayer(null);
                    }
                }

                if ($house->getLowestCeilingIsolationLayer()) {
                    if (!$house->getLowestCeilingIsolationLayer()->getMaterial() || !$house->getLowestCeilingIsolationLayer()->getSize()) {
                        $em->remove($house->getLowestCeilingIsolationLayer());
                        $house->setLowestCeilingIsolationLayer(null);
                    }
                }

                $em->persist($house);
                $calc->setHouse($house);

                foreach ($house->getWalls() as $i => $wall) {
                    if ($wall->getIsolationLayer()) {
                        if (!$wall->getIsolationLayer()->getMaterial() || !$wall->getIsolationLayer()->getSize()) {
                            $em->remove($wall->getIsolationLayer());
                            $wall->setIsolationLayer(null);
                        }
                    }

                    if ($wall->getOutsideLayer()) {
                        if (!$wall->getOutsideLayer()->getMaterial() || !$wall->getOutsideLayer()->getSize()) {
                            $em->remove($wall->getOutsideLayer());
                            $wall->setOutsideLayer(null);
                        }
                    }

                    if ($wall->getExtraIsolationLayer()) {
                        if (!$wall->getExtraIsolationLayer()->getMaterial() || !$wall->getExtraIsolationLayer()->getSize()) {
                            $em->remove($wall->getExtraIsolationLayer());
                            $wall->setExtraIsolationLayer(null);
                        }
                    }

                    $wall->setHouse($house);
                    $em->persist($wall);
                }

                $em->persist($calc);
                $em->flush();

                if (!$isEditing) {
                    $this->sendInfo($calc);
                }

                return $this->redirect($this->generateUrl('result', array('slug' => $calc->getSlug())));
            }
        }

        return $this->render('KrakenWarmBundle:Default:'.$template.'.html.twig', array(
            'form' => $form->createView(),
            'calc_slug' => $calc->getSlug(),
        ));
    }

    protected function sendInfo(Calculation $calc)
    {
        if ($calc->getEmail() == '') {
            return;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Podsumowanie grzewcze twojego domu')
            ->setFrom(array('juzefwt@gmail.com' => 'CieploWlasciwie.pl'))
            ->setTo($calc->getEmail())
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(
                    'KrakenWarmBundle:Calculator:email.html.twig',
                    array('calculation' => $calc)
                )
            )
        ;
        $this->get('mailer')->send($message);
    }

    public function breakdownAction($id)
    {
        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->find($id);

        if (!$calc) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $this->get('kraken_warm.instance')->setCustomCalculation($calc);

        $data = array();
        $breakdownData = $this->get('kraken_warm.building')->getEnergyLossBreakdown();

        foreach ($breakdownData as $key => $val) {
            $data[] = array($key, $val);
        }

        $breakdown = array(
            'type' => 'pie',
            'name' => 'Udział w stratach ciepła',
            'data' => $data,
        );

        return new JsonResponse($breakdown);
    }

    public function fuelsAction($id)
    {
        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->find($id);

        if (!$calc) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $this->get('kraken_warm.instance')->setCustomCalculation($calc);

        $data = [];
        $fuels = [];
        $variants = $this->get('kraken_warm.energy_pricing')->getHeatingVariantsComparison();
        $fuelEntities = $this->get('kraken_warm.energy_pricing')->getFuels();
        $variantTypes = array_keys($variants);

        foreach ($fuelEntities as $fuelEntity) {
            $fuels[$fuelEntity->getType()] = [
                'name' => $fuelEntity->getName(),
                'price' => (double) $fuelEntity->getPrice(),
                'trade_amount' => (int) $fuelEntity->getTradeAmount(),
                'trade_unit' => $fuelEntity->getTradeUnit(),
            ];
        }

        foreach ($variantTypes as $variantType) {
            $fuelType = $variants[$variantType]['fuel_type'];

            $data[] = [
                'type' => $variantType,
                'label' => $variants[$variantType]['label'],
                'version' => $variants[$variantType]['detail'],
                'amount' => $variants[$variantType]['amount'],
                'consumption' => round($variants[$variantType]['amount'] / $fuels[$fuelType]['trade_amount'], 1),
                'fuel_type' => $fuelType,
                'efficiency' => $variants[$variantType]['efficiency'] * 100,
                'setup_costs' => $variants[$variantType]['setup_costs'],
                'maintenance_time' => $variants[$variantType]['maintenance_time'],
            ];
        }

        $response = [
            'variants' => $data,
            'fuels' => $fuels,
            'currentVariant' => [
                'cost' => $calc->getFuelCost(),
                'time' => $this->get('kraken_warm.energy_pricing')->getMaintenanceTime($calc),
            ],
        ];

        return new JsonResponse($response);
    }

    public function customDataAction($id)
    {
        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->find($id);

        if (!$calc) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        if (isset($payload['fuels'])) {
            $customFuels = [];
            foreach ($payload['fuels'] as $type => $stuff) {
                $customFuels[$type] = round($stuff['price'], 2);
            }

            if (is_array($customFuels) && !empty($customFuels)) {
                $customData = json_decode($calc->getCustomData(), true);
                $customData['fuels'] = $customFuels;
                $calc->setCustomData(json_encode($customData));

                $em = $this->getDoctrine()->getManager();
                $em->persist($calc);
                $em->flush();
            }
        }

        return new Response('', 204);
    }

    public function resultAction($slug, Request $request)
    {
        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->findOneBy(array('id' => intval($slug, 36)));

        if (!$calc || !$calc->getHouse()) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $this->get('session')->set('calculation_id', $calc->getId());

        $calculator = $this->get('kraken_warm.energy_calculator');
        $building = $this->get('kraken_warm.building');
        $heatingSeason = $this->get('kraken_warm.heating_season');
        $pricing = $this->get('kraken_warm.energy_pricing');
        $fuelService = $this->get('kraken_warm.fuel');

        if ($calc->getHeatedArea() == false) {
            $nearestCity = $this->get('kraken_warm.city_locator')->findNearestCity();
            $calc->setHeatedArea($building->getHeatedHouseArea());
            $calc->setHeatingPower($calculator->getMaxHeatingPower());
            $calc->setCity($nearestCity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($calc);
            $em->flush();
        }

        return $this->render('KrakenWarmBundle:Default:result.html.twig', array(
            'calculator' => $calculator,
            'building' => $building,
            'pricing' => $pricing,
            'heatingSeason' => $heatingSeason,
            'fuelService' => $fuelService,
            'punch' => $this->get('kraken_warm.punchline'),
            'classifier' => $this->get('kraken_warm.building_classifier'),
            'describer' => $this->get('kraken_warm.house_description'),
            'upgrade' => $this->get('kraken_warm.upgrade'),
            'comparison' => $this->get('kraken_warm.comparison'),
            'climate' => $this->get('kraken_warm.climate'),
            'calc' => $calc,
            'city' => $calc->getCity(),
            'isAuthor' => $this->userIsAuthor($slug, $request),
        ));
    }

    public function heatersAction($slug)
    {
        $calculation = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->findOneBy(array('id' => intval($slug, 36)));

        if (!$calculation || !$calculation->getHouse()) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $this->get('kraken_warm.instance')->setCustomCalculation($calculation);

        $calculationulator = $this->get('kraken_warm.energy_calculator');
        $building = $this->get('kraken_warm.building');

        return $this->render('KrakenWarmBundle:Default:heaters.html.twig', array(
            'calculator' => $calculationulator,
            'building' => $building,
            'punch' => $this->get('kraken_warm.punchline'),
            'climate' => $this->get('kraken_warm.climate'),
            'calc' => $calculation,
        ));
    }

    public function myResultsAction()
    {
        $request = $this->get('request');
        $cookieValue = $request->cookies->get('sup_bro');
        $slugs = explode(';', $cookieValue);

        $ids = array();
        foreach ($slugs as $slug) {
            $ids[] = intval($slug, 36);
        }

        $results = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('KrakenWarmBundle:Calculation', 'c')
            ->innerJoin('c.house', 'h')
            ->where('c.id IN (?1)')
            ->setParameters(array(
                1 => $ids,
            ))
            ->getQuery()
            ->getResult();

        return $this->render('KrakenWarmBundle:Calculator:myResults.html.twig', array('results' => $results));
    }
}
