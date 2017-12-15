<?php

namespace LocaleRouter\Entity;

use Doctrine\ORM\Mapping as ORM;

trait LocaleUserTrait
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $locale;

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
