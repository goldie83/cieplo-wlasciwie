<?php

namespace Kraken\WarmBundle\Controller;

use Kraken\WarmBundle\Form\CalculationFormType;
use Kraken\WarmBundle\Form\HouseApartmentType;
use Kraken\WarmBundle\Form\HouseType;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Layer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CalculatorController extends Controller
{
    public function indexAction($slug = null)
    {
        $calc = null;

        if ($slug) {
            if (!$this->userIsAuthor($slug)) {
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

        $form = $this->createForm(new CalculationFormType(), $calc);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $obj = $form->getData();

                $isEditing = $obj->getId() != null;

                $em->persist($obj);
                $em->flush();

                $calcSlug = base_convert($obj->getId(), 10, 36);
                $redirect = $this->generateUrl('details', array(
                    'slug' => $calcSlug
                ));

                if (!$isEditing) {
                    $request = $this->get('request');
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

        return $this->render('KrakenWarmBundle:Default:index.html.twig', array(
            'calc' => $calc,
            'form' => $form->createView()
        ));
    }

    protected function userIsAuthor($slug)
    {
        $request = $this->get('request');
        $cookieValue = $request->cookies->get('sup_bro');
        $slugs = explode(';', $cookieValue);

        return in_array($slug, $slugs);
    }

    public function detailsAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $calc = $this->getDoctrine()
            ->getRepository('KrakenWarmBundle:Calculation')
            ->findOneBy(array('id' => intval($slug, 36)));

        if (!$calc || !$this->userIsAuthor($slug)) {
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

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());
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
            ->setContentType("text/html")
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

        $data = [];
        $raw = $this->get('kraken_warm.energy_pricing')->getEnergySourcesComparison();
        $fuelTypes = array_keys($raw);

        foreach ($fuelTypes as $fuelType) {
            $data[] = [
                'fuel_type' => $fuelType,
                'price' => $raw[$fuelType]['price'],
                'amount' => $raw[$fuelType]['amount'],
                'consumption' => round($raw[$fuelType]['amount']/$raw[$fuelType]['trade_amount'], 1),
                'trade_amount' => $raw[$fuelType]['trade_amount'],
                'trade_unit' => $raw[$fuelType]['trade_unit'],
                'label' => $raw[$fuelType]['label'],
                'version' => $raw[$fuelType]['detail'],
                'efficiency' => $raw[$fuelType]['efficiency']*100,
                'setup_cost' => $raw[$fuelType]['setup_cost'],
                'maintenance_time' => $raw[$fuelType]['maintenance_time'],
                'is_legacy' => $raw[$fuelType]['is_legacy'],
            ];
        }

        $response = [
            'variants' => $data,
            'currentVariant' => [
                'cost' => (double)$calc->getFuelCost(),
                'time' => 200//FIXME (double)$calc->getMaintenanceTime()
            ],
        ];

        return new JsonResponse($response);
    }

    public function resultAction($slug)
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
            'fuels' => $this->get('kraken_warm.energy_pricing')->getFuels(),
            'building' => $building,
            'pricing' => $pricing,
            'heatingSeason' => $heatingSeason,
            'punch' => $this->get('kraken_warm.punchline'),
            'classifier' => $this->get('kraken_warm.building_classifier'),
            'upgrade' => $this->get('kraken_warm.upgrade'),
            'comparison' => $this->get('kraken_warm.comparison'),
            'houseDescription' => $building->getHouseDescription(),
            'calc' => $calc,
            'city' => $calc->getCity(),
            'isAuthor' => $this->userIsAuthor($slug),
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

        $this->get('session')->set('calculation_id', $calculation->getId());

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
                1 => $ids
            ))
            ->getQuery()
            ->getResult();

        return $this->render('KrakenWarmBundle:Calculator:myResults.html.twig', array('results' => $results));
    }
}
