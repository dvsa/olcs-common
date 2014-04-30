<?php

/**
 * MissingTranslationProcessor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Translator;

/**
 * MissingTranslationProcessor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MissingTranslationProcessor implements EventProcessor
{
    /**
     * Process an event
     *
     * @param object $e
     * @return string
     */
    public static function processEvent($e)
    {
        $translator = $e->getTarget();
        $params = $e->getParams();

        $message = $params['message'];

        if (preg_match_all('/\{([^\}]+)\}/', $message, $matches)) {

            foreach ($matches[0] as $key => $match) {
                $message = str_replace($match, $translator->translate($matches[1][$key]), $message);
            }
        }

        return $message;
    }
}
