<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Strategy\StrategyInterface;
use Laminas\Stdlib\RequestInterface;

interface ExtractStrategyInterface extends StrategyInterface
{
    public function extractLocale(RequestInterface $request, $baseUrl);
}
