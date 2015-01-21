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
class MissingTranslationProcessor
{
    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    /**
     * Process an event
     *
     * @param object $e
     * @return string
     */
    public function processEvent($e)
    {
        $translator = $e->getTarget();
        $params = $e->getParams();

        $message = $params['message'];

        if (preg_match_all('/\{([^\}]+)\}/', $message, $matches)) {

            foreach ($matches[0] as $key => $match) {
                $message = str_replace($match, $translator->translate($matches[1][$key]), $message);
            }
        } else {

            $locale = $params['locale'];

            $partial = __DIR__ . '/../../../../config/language/partials/' . $locale . '/' . $message . '.phtml';

            if (file_exists($partial)) {

                $renderer = $this->sm->get('ViewRenderer');

                $message = $renderer->render($locale . '/' . $message);
            }
        }

        return $message;
    }
}
