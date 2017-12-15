<?php

namespace LocaleRouter\Entity;

interface LocaleUserInterface extends \ZF2LanguageRoute\Entity\LocaleUserInterface
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
