<?php

namespace App\ESM\Service\Utils;

trait TranslatableTrait
{
    /**
     * @var bool|string
     */
    protected $translationDomain = false;
    /**
     * @var array
     */
    protected $translationArgs = [];

    /**
     * @return bool|string
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    /**
     * @param bool|string $translationDomain
     *
     * @return $this
     */
    public function setTranslationDomain($translationDomain): self
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }

    public function getTranslationArgs(): array
    {
        return $this->translationArgs;
    }

    /**
     * @return $this
     */
    public function setTranslationArgs(array $translationArgs): self
    {
        $this->translationArgs = $translationArgs;

        return $this;
    }
}
