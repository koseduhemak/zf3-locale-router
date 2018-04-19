<?php

namespace LocaleRouter\Entity;

interface LocaleUserInterface
{
    /**
     * @return string|null
     */
    public function getLocale();

    /**
     * @param $locale
     *
     * @return string
     */
    public function setLocale($locale);
}
