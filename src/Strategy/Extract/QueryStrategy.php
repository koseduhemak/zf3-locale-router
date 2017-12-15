<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use Zend\Stdlib\RequestInterface;

final class QueryStrategy extends AbstractExtractStrategy
{
    /** @var string */
    protected $paramName = 'lang';

    public function setStrategyOptions(array $options = [])
    {
        if (array_key_exists('paramName', $options)) {
            $this->paramName = $options['paramName'];
        }
    }

    public function extractLocale(RequestInterface $request, $baseUrl)
    {
        $result = new StrategyResultModel();
        $locale = null;

        $queryParam = $request->getQuery($this->paramName, false);

        if ($queryParam) {
            $locale = $this->getLanguage($queryParam);
        }

        $result->setLocale($locale);

        return $result;
    }
}
