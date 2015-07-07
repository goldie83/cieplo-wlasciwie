<?php

namespace Kraken\WarmBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Kraken\WarmBundle\Entity\Calculation;

class InstanceService
{
    protected $em;
    protected $session;
    protected $cachedInstance = null;
    protected $customCalculation = null;

    public function __construct(SessionInterface $session, EntityManager $em)
    {
        $this->session = $session;
        $this->em = $em;
    }

    public function setCustomCalculation(Calculation $calc)
    {
        $this->customCalculation = $calc;
    }

    public function get()
    {
        if ($this->customCalculation != null) {
            return $this->customCalculation;
        }

        $instanceId = $this->session->get('calculation_id');

        if ($this->cachedInstance != null && $this->cachedInstance->getId() == $instanceId) {
            return $this->cachedInstance;
        }

        if (is_int($instanceId)) {
            $instance = $this->em->getRepository('KrakenWarmBundle:Calculation')->find($instanceId);
        }

        if (!isset($instance) || !$instance instanceof Calculation) {
            throw new \RuntimeException('There is no Calculation instance here');
        }

        $this->cachedInstance = $instance;

        return $instance;
    }
}
