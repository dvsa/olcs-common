<?php

/**
 * Translator Delegator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Translator\Translator as ZendTranslator;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Translator Delegator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslatorDelegator extends Translator
{
    /**
     * @var ZendTranslator
     */
    protected $translator;

    /**
     * @var array
     */
    private $replacements;

    public function __construct(TranslatorInterface $translator, array $replacements)
    {
        $this->translator = $translator;
        $this->replacements = $replacements;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array([$this->translator, $method], $args);
    }

    public function translate($message, $textDomain = 'default', $locale = null)
    {
        return $this->replaceVariables($this->translator->translate($message, $textDomain, $locale));
    }

    protected function replaceVariables($message)
    {
        return str_replace(array_keys($this->replacements), array_values($this->replacements), $message);
    }
}
