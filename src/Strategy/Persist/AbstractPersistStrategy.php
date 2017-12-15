<?php

namespace LocaleRouter\Strategy\Persist;

use Zend\Stdlib\ResponseInterface;

abstract class AbstractPersistStrategy implements PersistStrategyInterface
{
    public function save($locale, ResponseInterface $response)
    {
        // TODO: Implement save() method.
    }

    public function setStrategyOptions(array $options = [])
    {
        // TODO: Implement setStrategyOptions() method.
    }
}
