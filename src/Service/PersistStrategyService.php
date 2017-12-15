<?php

namespace LocaleRouter\Service;

use LocaleRouter\Strategy\Persist\PersistStrategyInterface;

class PersistStrategyService
{
    /** @var array */
    protected $strategies = [];

    public function saveLocale($locale, $response)
    {
        /** @var PersistStrategyInterface $strategy */
        foreach ($this->strategies as $strategy) {
            $response = $strategy->save($locale, $response);
        }

        return $response;
    }

    /**
     * @return array
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * @param array $strategies
     */
    public function setStrategies($strategies)
    {
        $this->strategies = $strategies;
    }
}
