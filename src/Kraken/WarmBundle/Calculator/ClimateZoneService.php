<?php

namespace Kraken\WarmBundle\Calculator;

use Kraken\WarmBundle\Service\InstanceService;
use Geometry\Polygon;

class ClimateZoneService
{
    protected $instance;

    protected $designTemperatures = array(
        1 => -16,
        2 => -18,
        3 => -20,
        4 => -22,
        5 => -24,
    );

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance;
    }

    public function getClimateZone()
    {
        $polygons = [
            [
                // (5) Zakopane i okolice
                [49.428840, 19.646301],
                [49.188884, 19.606476],
                [49.179908, 20.190125],
                [49.427054, 20.217590],
            ],
            [
                // (5) Suwałki i okolice
                [53.797406, 21.857300],
                [54.438103, 21.807861],
                [54.457267, 23.609619],
                [53.816869, 23.620605],
            ],
            [
                // (4) Karpaty
                [49.795450, 18.599854],
                [49.660517, 19.918213],
                [49.553726, 20.720215],
                [49.596470, 21.697998],
                [49.546598, 23.104248],
                [48.922499, 23.027344],
                [49.131408, 18.555908],
            ],
            [
                // (4) Mazury
                [51.495065, 24.093018],
                [51.805218, 22.434082],
                [51.805218, 21.961670],
                [53.189579, 21.692505],
                [53.189579, 20.319214],
                [54.428518, 20.253296],
                [54.607074, 23.796387],
            ],
            [
                // (2) Wielkopolska, centrum, Dolny Śląsk
                [52.485921, 14.051514],
                [52.596273, 15.183105],
                [54.238155, 18.072510],
                [54.193132, 18.808594],
                [54.412537, 19.008320],
                [54.482805, 19.863281],
                [54.127041, 19.467773],
                [53.859007, 18.923950],
                [53.419354, 18.775635],
                [53.054422, 18.369141],
                [52.616390, 18.687744],
                [51.692990, 19.226074],
                [50.883976, 19.275513],
                [50.999929, 18.237305],
                [50.972265, 16.847534],
                [51.141448, 16.064758],
                [51.252822, 14.551392],
            ],
            [
                // (1) Pomorze
                [54.136696, 14.062500],
                [52.445966, 14.040527],
                [52.549636, 15.172119],
                [54.201010, 18.061523],
                [54.156001, 18.819580],
                [54.393352, 18.995361],
                [55.341642, 18.819580],
            ],
        ];

        $polygonToClimateZoneMap = [
            // this has to match order of polygons above!
            0 => 5,
            1 => 5,
            2 => 4,
            3 => 4,
            4 => 2,
            5 => 1,
        ];

        $lat = $this->instance->get()->getLatitude();
        $lon = $this->instance->get()->getLongitude();

        foreach ($polygons as $i => $polygon) {
            $poly = new Polygon($polygon);

            if ($poly->pip($lat, $lon)) {
                return $polygonToClimateZoneMap[$i];
            }
        }

        return 3;
    }

    public function getDesignOutdoorTemperature()
    {
        return $this->designTemperatures[$this->getClimateZone()];
    }
}
