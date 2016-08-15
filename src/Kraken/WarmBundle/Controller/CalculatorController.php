<?php

namespace Kraken\WarmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kraken\WarmBundle\Form\CalculationApartmentStepDimensionsType;
use Kraken\WarmBundle\Form\CalculationApartmentStepCeilingType;
use Kraken\WarmBundle\Form\CalculationStepCeilingType;
use Kraken\WarmBundle\Form\CalculationStepDimensionsType;
use Kraken\WarmBundle\Form\CalculationStepHeatingType;
use Kraken\WarmBundle\Form\CalculationStepLocationType;
use Kraken\WarmBundle\Form\CalculationStepWallsType;
use Kraken\WarmBundle\Form\CalculationStepOneType;
use Kraken\WarmBundle\Entity\Apartment;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;

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
            $this->get('session')->set('is_form_filled_first_time', true);
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

        $form = $this->createForm(new CalculationStepLocationType(), $calc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $obj = $form->getData();

            $this->getDoctrine()->getManager()->persist($obj);
            $this->getDoctrine()->getManager()->flush();

            $redirect = $this->generateUrl('dimensions', array(
                'slug' => base_convert($obj->getId(), 10, 36),
            ));

            return $this->redirect($redirect);
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

        if ($calc->getBuildingType() == 'apartment') {
            $form = $this->createForm(new CalculationApartmentStepDimensionsType(), $calc->getHouse());
        } else {
            $form = $this->createForm(new CalculationStepDimensionsType(), $calc->getHouse(), ['building_type' => $calc->getBuildingType()]);
        }

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

        $template = $calc->getBuildingType() == 'apartment'
            ? 'KrakenWarmBundle:Calculator:dimensions_apartment.html.twig'
            : 'KrakenWarmBundle:Calculator:dimensions.html.twig';

        return $this->render($template, array(
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

            if (!$form->get('has_isolation_inside')->getData() && $house->getInternalIsolationLayer()) {
                $em->refresh($house->getInternalIsolationLayer());
                $em->remove($house->getInternalIsolationLayer());

                if ($form->get('external_isolation_layer')->getData() == null) {
                    $em->flush();
                }
            }

            if (!$form->get('has_isolation_outside')->getData() && $house->getExternalIsolationLayer()) {
                $em->refresh($house->getExternalIsolationLayer());
                $em->remove($house->getExternalIsolationLayer());
                $em->flush();
            }

            $em->persist($house);
            $calc->setHouse($house);
            $em->persist($calc);
            $em->flush();

            $calcSlug = base_convert($calc->getId(), 10, 36);
            $redirect = $this->generateUrl('ceiling', array(
                'slug' => $calcSlug,
            ));

            return $this->redirect($redirect);
        }

        return $this->render('KrakenWarmBundle:Calculator:walls.html.twig', array(
            'calc' => $calc,
            'form' => $form->createView(),
        ));
    }

    public function ceilingAction($slug, Request $request)
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

        if ($calc->getBuildingType() == 'apartment') {
            if (!$calc->getHouse()->getApartment()) {
                $apartment = Apartment::create();
                $house->setApartment($apartment);
            }

            $form = $this->createForm(new CalculationApartmentStepCeilingType(), $house->getApartment());

            if ($house->getTopIsolationLayer()) {
                $form->get('top_isolation_layer')->setData($house->getTopIsolationLayer());
            }

            if ($house->getBottomIsolationLayer()) {
                $form->get('bottom_isolation_layer')->setData($house->getBottomIsolationLayer());
            }
        } else {
            $this->get('kraken_warm.instance')->setCustomCalculation($calc);
            $floors = $this->get('kraken_warm.floors');
            $form = $this->createForm(new CalculationStepCeilingType(), $house, [
                'top_isolation_label' => $floors->getTopIsolationLabel(),
                'bottom_isolation_label' => $floors->getBottomIsolationLabel(),
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $house = $form->getData() instanceof House ? $form->getData() : $calc->getHouse();

            $topIsolationData = $form->get('top_isolation_layer')->getData();
            $bottomIsolationData = $form->get('bottom_isolation_layer')->getData();

            if ($form->getData() instanceof Apartment) {
                $house->setTopIsolationLayer($topIsolationData);
                $house->setBottomIsolationLayer($bottomIsolationData);
            }

            $shouldHaveTopIsolation = $form->get('has_top_isolation')->getData() == 'yes' || $form->get('has_top_isolation')->getData() === true;
            $shouldHaveBottomIsolation = $form->get('has_bottom_isolation')->getData() == 'yes' || $form->get('has_bottom_isolation')->getData() === true;

            if ($topIsolationData != null && $topIsolationData->getId() != null && (!$shouldHaveTopIsolation || $topIsolationData->getMaterial() == null || !$topIsolationData->getSize())) {
                $em->refresh($topIsolationData);
                $em->remove($topIsolationData);

                if ($shouldHaveBottomIsolation || $bottomIsolationData == null) {
                    $em->flush();
                }
            }

            if ($bottomIsolationData != null && $bottomIsolationData->getId() != null && (!$shouldHaveBottomIsolation || $bottomIsolationData->getMaterial() == null || !$bottomIsolationData->getSize())) {
                $em->refresh($bottomIsolationData);
                $em->remove($bottomIsolationData);
                $em->flush();
            }

            $em->persist($house);
            $em->persist($calc);
            $em->flush();

            $calcSlug = base_convert($calc->getId(), 10, 36);
            $redirect = $this->generateUrl('heating', array(
                'slug' => $calcSlug,
            ));

            return $this->redirect($redirect);
        }

        $template = $calc->getBuildingType() == 'apartment'
            ? 'KrakenWarmBundle:Calculator:ceiling_apartment.html.twig'
            : 'KrakenWarmBundle:Calculator:ceiling.html.twig';

        return $this->render($template, array(
            'calc' => $calc,
            'form' => $form->createView(),
        ));
    }

    public function heatingAction($slug, Request $request)
    {
        if (!$this->userIsAuthor($slug, $request)) {
            throw $this->createNotFoundException('Jakiś zły masz ten link. Nic tu nie ma.');
        }

        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->findOneBy(array('id' => intval($slug, 36)));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CalculationStepHeatingType(), $calc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calc = $form->getData();
            $calc->getHouse()->setVentilationType($form['ventilation_type']->getData());

            foreach ($form['fuel_consumptions']->getData() as $item) {
                $item->setCalculation($calc);
                $em->persist($item);
            }

            $em->persist($calc);
            $em->persist($calc->getHouse());
            $em->flush();

            $calcSlug = base_convert($calc->getId(), 10, 36);
            $redirect = $this->generateUrl('result', array(
                'slug' => $calcSlug,
            ));

            if ($this->get('session')->get('is_form_filled_first_time')) {
                $this->sendInfo($calc);
            }

            return $this->redirect($redirect);
        }

        return $this->render('KrakenWarmBundle:Calculator:heating.html.twig', array(
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

    protected function sendInfo(Calculation $calc)
    {
        if ($calc->getEmail() == '') {
            return;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Podsumowanie grzewcze twojego domu')
            ->setFrom([$this->getParameter('mailer_user') => 'CieploWlasciwie.pl'])
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

        // erase e-mail as promised
        $calc->setEmail('');
        $this->getDoctrine()->getManager()->persist($calc);
        $this->getDoctrine()->getManager()->flush();
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

        if (!$calc) {
            return $this->redirectToRoute('homepage');
        }

        if (!$calc->getHouse() || !$calc->getIndoorTemperature() || ($calc->getHouse() && $calc->getHouse()->getConstructionType() == 'traditional' && !$calc->getHouse()->getPrimaryWallMaterial())) {
            if ($this->userIsAuthor($slug, $request)) {
                $this->addFlash('warning', 'Wynik nie jest gotowy, brakuje informacji o podstawowym materiale konstrukcyjnym ścian zewnętrznych. Wróć do formularza i w zakładce Ściany podaj co trzeba.');

                return $this->redirectToRoute('start', ['slug' => $slug]);
            } else {
                return $this->redirectToRoute('homepage');
            }
        }

        $this->get('session')->set('calculation_id', $calc->getId());

        $calculator = $this->get('kraken_warm.energy_calculator');
        $dimensions = $this->get('kraken_warm.dimensions');

        if ($calc->getHeatedArea() == false) {
            $nearestCity = $this->get('kraken_warm.city_locator')->findNearestCity();
            $calc->setHeatedArea($dimensions->getHeatedHouseArea());
            $calc->setHeatingPower($calculator->getMaxHeatingPower());
            $calc->setCity($nearestCity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($calc);
            $em->flush();
        }

        return $this->render('KrakenWarmBundle:Default:result.html.twig', array(
            'calc' => $calc,
            'city' => $calc->getCity(),
            'calculator' => $calculator,
            'pricing' => $this->get('kraken_warm.energy_pricing'),
            'heatingSeason' => $this->get('kraken_warm.heating_season'),
            'fuelService' => $this->get('kraken_warm.fuel'),
            'hotWater' => $this->get('kraken_warm.hot_water'),
            'classifier' => $this->get('kraken_warm.building_classifier'),
            'describer' => $this->get('kraken_warm.house_description'),
            'upgrade' => $this->get('kraken_warm.upgrade'),
            'comparison' => $this->get('kraken_warm.comparison'),
            'climate' => $this->get('kraken_warm.climate'),
            'dimensions' => $dimensions,
            'floors' => $this->get('kraken_warm.floors'),
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

        return $this->render('KrakenWarmBundle:Default:heaters.html.twig', array(
            'calc' => $calculation,
            'calculator' => $this->get('kraken_warm.energy_calculator'),
            'building' => $this->get('kraken_warm.building'),
            'climate' => $this->get('kraken_warm.climate'),
            'floors' => $this->get('kraken_warm.floors'),
            'dimensions' => $this->get('kraken_warm.dimensions'),
            'wall' => $this->get('kraken_warm.wall'),
            'house_description' => $this->get('kraken_warm.house_description'),
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

        return $this->render('KrakenWarmBundle:Calculator:myResults.html.twig', [
            'results' => $results,
            'dimensions' => $this->get('kraken_warm.dimensions'),
        ]);
    }
}
