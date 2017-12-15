<?php

namespace LocaleRouter\Model;

class StrategyResultModel
{
    /** @var string */
    protected $locale = null;

    /** @var bool */
    protected $processingStopped = false;

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return bool
     */
    public function isProcessingStopped()
    {
        return $this->processingStopped;
    }

    /**
     * @param bool $processingStopped
     */
    public function setProcessingStopped($processingStopped)
    {
        $this->processingStopped = $processingStopped;
    }
}
