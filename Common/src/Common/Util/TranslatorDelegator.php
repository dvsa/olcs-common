<?php

/**
 * Translator Delegator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\I18n\Translator;

/**
 * Translator Delegator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslatorDelegator extends Translator
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    private $replacements;

    /**
     * TranslatorDelegator constructor.
     *
     * @param TranslatorInterface $translator   Transloator
     * @param array               $replacements Array of tokens that can be replaced in translations
     */
    public function __construct(TranslatorInterface $translator, array $replacements)
    {
        $this->translator = $translator;
        $this->replacements = $replacements;
    }

    /**
     * Proxy to any translator methods
     *
     * @param string $method Method to call
     * @param array  $args   Methods arguments
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        return call_user_func_array([$this->translator, $method], $args);
    }

    /**
     * Translate a message
     *
     * @param string $message    Message to be translated
     * @param string $textDomain Domain for translations
     * @param string $locale     Locale to be translated to
     *
     * @return string
     */
    public function translate($message, $textDomain = 'default', $locale = null)
    {
        if ($message === null) {
            return '';
        }

        return $this->replaceVariables($this->translator->translate($message, $textDomain, $locale));
    }

    /**
     * Replace token in translated string
     *
     * @param string $message Message being translated
     *
     * @return string
     */
    protected function replaceVariables($message)
    {
        return str_replace(array_keys($this->replacements), array_values($this->replacements), $message);
    }
}
