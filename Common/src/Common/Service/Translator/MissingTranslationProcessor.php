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
    /**
     * @var Zend\View\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @param Zend\View\Renderer\RendererInterface
     */
    public function __construct($renderer, $resolver)
    {
        $this->renderer = $renderer;
        $this->resolver = $resolver;
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

            // handles nested translation keys inside curly braces {}
            foreach ($matches[0] as $key => $match) {
                $message = str_replace($match, $translator->translate($matches[1][$key]), $message);
            }

        } else {
            // handles partials as translations. Note we only try to resolve keys
            // that match a pattern, to avoid having to run the template resolver
            // against ALL missing translations
            if (strpos($message, 'markup-') === 0) {
                $locale    = $params['locale'];
                $partial   = $locale . '/' . $message; // e.g. en_GB/my-translation-key
                $foundPath = $this->resolver->resolve($partial);
                if ($foundPath !== false) {
                    $message = $this->renderer->render($partial);
                }
            }

        }

        return $message;
    }
}
