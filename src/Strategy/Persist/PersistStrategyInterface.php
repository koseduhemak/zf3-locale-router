<?php


namespace LocaleRouter\Strategy\Persist;

use LocaleRouter\Strategy\StrategyInterface;
use Zend\Stdlib\ResponseInterface;

interface PersistStrategyInterface extends StrategyInterface
{
    public function save($locale, ResponseInterface $response);

    public function setStrategyOptions(array $options = []);
}
