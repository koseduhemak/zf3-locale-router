<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use Zend\Http\Request;
use Zend\Stdlib\RequestInterface;

final class UriPathStrategy extends AbstractExtractStrategy
{
    public function setStrategyOptions($options = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function extractLocale(RequestInterface $request, $baseUrl)
    {
        $result = new StrategyResultModel();
        $locale = null;

        if ($request instanceof Request) {
            $uri           = $request->getUri();
            $baseUrlLength = strlen($baseUrl);
            $path          = ltrim(substr($uri->getPath(), $baseUrlLength), '/');
            $pathParts     = explode('/', $path);

            $locale = $this->getLanguage($pathParts[0]);
        }

        $result->setLocale($locale);

        return $result;
    }
}
