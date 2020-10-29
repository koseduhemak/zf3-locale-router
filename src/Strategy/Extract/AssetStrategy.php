<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use Laminas\Stdlib\RequestInterface;

/**
 * This class checks whether the requested uri should deliver an asset and should therefore not be redirected.
 * If it is an asset, we return false to stop further processing of other strategies in SlmLocale\Locale\Detector.
 *
 * Example config:
 * 'slm_locale' => [
 *     'default' => 'de_DE',
 *     'supported' => ['en_GB', 'de_DE'],
 *     'strategies' => [
 *         [
 *             'name' => SlmLocale\Strategy\AssetStrategy::class,
 *             'options' => [
 *                 'file_extensions' => [
 *                     'css', 'js'
 *                 ]
 *             ]
 *         ],
 *         'query',
 *         'cookie',
 *         'acceptlanguage'
 *     ],
 *     'mappings' => [
 *         'en' => 'en_GB',
 *         'de' => 'de_DE',
 *     ]
 * ],
 *
 * This example config would ignore the file_extensions ".css", ".CSS", ".js", ".JS".
 *
 * Class AssetStrategy
 *
 * @package SlmLocale\Strategy
 */
final class AssetStrategy extends AbstractExtractStrategy
{
    /** @var  array */
    private $file_extensions = ['js', 'css', 'jpg', 'jpeg', 'gif', 'png'];

    public function extractLocale(RequestInterface $request, $baseUrl)
    {
        $result    = new StrategyResultModel();
        $path      = $request->getUri();
        $path      = parse_url($path, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // if the file extension of the uri is found within the configured file_extensions, we do not rewrite and skip further processing
        if (in_array($extension, $this->file_extensions)) {
            $result->setProcessingStopped(true);
        }

        return $result;
    }

    public function setStrategyOptions(array $options = [])
    {
        if (array_key_exists('file_extensions', $options)) {
            $this->file_extensions = (array) $options['file_extensions'];
        }
    }
}
