<?php


namespace LocaleRouterTest\Mocks;


use LocaleRouter\Entity\LocaleUserInterface;

class UserMock implements LocaleUserInterface
{
    /** @var string */
    protected $locale;

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

}