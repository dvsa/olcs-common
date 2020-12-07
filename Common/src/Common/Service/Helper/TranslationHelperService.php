<?php

/**
 * Translation Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Translation Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslationHelperService extends AbstractHelperService
{
    /**
     * Allows you to replace variables after the string is translated
     *
     * @param string $translationKey
     * @param array  $arguments
     * @param string $translateToWelsh 'Y' or 'N', Force the translation into welsh
     * @return string
     */
    public function translateReplace($translationKey, array $arguments, $translateToWelsh = 'N')
    {
        return vsprintf($this->translate($translationKey, $translateToWelsh), $arguments);
    }

    /**
     * Format a translation string
     *
     * @param string $format
     * @param array $messages
     * @return string
     */
    public function formatTranslation($format, $messages)
    {
        if (!is_array($messages)) {
            return $this->wrapTranslation($format, $messages);
        }

        array_walk(
            $messages,
            function (&$value) {
                $value = $this->translate($value);
            }
        );

        return vsprintf($format, $messages);
    }

    /**
     * Wrap a translated message with the wrapper
     *
     * @param string $wrapper
     * @param string $message
     * @return string
     */
    public function wrapTranslation($wrapper, $message)
    {
        return sprintf($wrapper, $this->translate($message));
    }

    /**
     * Translate a message
     *
     * @param string $message
     * @return string
     */
    public function translate($message, $translateToWelsh = 'N')
    {
        $locale = ($translateToWelsh === 'Y') ? 'cy_GB' : null;
        return $this->getTranslator()->translate($message, 'default', $locale);
    }

    /**
     * Get translator
     *
     * @return \Laminas\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->getServiceLocator()->get('translator');
    }
}
