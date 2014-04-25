<?php
namespace UiucCms\Bundle\TestUtilityBundle\Translation\Translator;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Translates message id to itself.
 * Reference: http://braincrafted.com/symfony2-functional-test-translation-key/
 */
class IdentityTranslator implements TranslatorInterface
{
    public function trans(
        $id,
        array $parameters = array(),
        $domain = null,
        $locale = null
    ) {
        return $id;
    }

    public function transChoice(
        $id,
        $number,
        array $parameters = array(),
        $domain = null,
        $locale = null
    ) {
        return $id;
    }

    public function setLocale($locale)
    {
        // do nothing
    }

    public function getLocale()
    {
        return '--';
    }

    public function setFallbackLocale($locale)
    {
        // do nothing
    }

    public function setFallbackLocales($locale)
    {
        // do nothing
    }

    public function getFallbackLocales()
    {
        return array();
    }

    public function addResource($resource)
    {
        // do nothing
    }
}
