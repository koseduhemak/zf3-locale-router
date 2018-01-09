<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use Zend\Stdlib\RequestInterface;

final class QueryStrategy extends AbstractExtractStrategy
{
    const PARAM_NAME = 'locale';

    /** @var string */
    protected $paramName;

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

        $queryParam = $request->getQuery($this->getParamName(), false);

        if ($queryParam) {
            $locale = $this->getLanguage($queryParam);
        }

        $result->setLocale($locale);

        return $result;
    }

    /**
     * @return string
     */
    public function getParamName()
    {
        if (null === $this->paramName) {
            return self::PARAM_NAME;
        }

        return (string) $this->paramName;
    }
}
