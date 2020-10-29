<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use Laminas\Http\Request;
use Laminas\Stdlib\RequestInterface;

final class UriPathStrategy extends AbstractExtractStrategy
{
    public function setStrategyOptions(array $options = [])
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

            if (is_null($locale)) {
                $locale = $this->options->getRootLanguage();
            }
        }

        $result->setLocale($locale);

        return $result;
    }
}
