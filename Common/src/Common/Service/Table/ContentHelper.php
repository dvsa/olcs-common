<?php

/**
 * Content Helper
 *
 * Helps with rendering of content and partials (For Table Builder)
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table;

/**
 * Content Helper
 *
 * Helps with rendering of content and partials (For Table Builder)
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

class ContentHelper
{
    /**
     * The location of the partials
     *
     * @var string
     */
    private $location;

    /**
     * $object to be used in scope
     *
     * @var object
     */
    private $object;

    /**
     * Cached partials
     *
     * @var array
     */
    private $partials = array();

    /**
     * Pass in the location of the partials
     *
     * @param string $location
     * @param object $object
     */
    public function __construct($location = '', $object = null)
    {
        $this->location = rtrim($location, '/') . '/';

        $this->object = $object;
    }

    /**
     * Render layout
     *
     * @param string $name
     * @return string
     */
    public function renderLayout($name)
    {
        $partialFile = $this->location . 'layouts/' . $name . '.phtml';

        if (!file_exists($partialFile)) {

            throw new \Exception('Partial not found: ' . $partialFile);
        }

        ob_start();
            require($partialFile);
            $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Replace vars into content
     *
     * @param string $content
     * @param array $vars
     * @return string
     */
    public function replaceContent($content, $vars = array())
    {
        $content = $this->replacePartials($content);

        foreach ($vars as $key => $val) {

            if (is_string($val) || is_numeric($val)) {

                $content = str_replace('{{' . $key . '}}', (string)$val, $content);
            }
        }

        return preg_replace('/(\{\{[a-zA-Z0-9\/\[\]]+\}\})/', '', $content);
    }

    /**
     * Render an attribute string from an array
     *
     * @param array $attrs
     * @return string
     */
    public function renderAttributes($attrs)
    {
        $attributes = array();

        foreach ($attrs as $name => $value) {

            $attributes[] = $name .= '="' . $value . '"';
        }

        return implode(' ', $attributes);
    }

    /**
     * Replace partials in the content
     *
     * @param string $content
     * @return string
     */
    private function replacePartials($content)
    {
        if (preg_match_all('/(\{\{\[([a-zA-Z\/]+)\]\}\})/', $content, $matches)) {

            $partials = array();

            foreach ($matches[2] as $match) {

                $partials[$match] = $match;
            }

            foreach ($partials as $partial) {

                $content = str_replace('{{[' . $partial .']}}', $this->getPartial($partial), $content);
            }
        }

        return $content;
    }

    /**
     * Get a partials content
     *
     * @param string $partial
     * @return string
     */
    private function getPartial($partial)
    {
        if (!isset($this->partials[$partial])) {

            $this->partials[$partial] = '';

            $filename = $this->location . $partial . '.phtml';

            if (file_exists($this->location . $partial . '.phtml')) {

                $this->partials[$partial] = file_get_contents($filename);
            }
        }

        return $this->partials[$partial];
    }
}