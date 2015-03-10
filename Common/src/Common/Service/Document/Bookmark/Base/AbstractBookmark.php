<?php
namespace Common\Service\Document\Bookmark\Base;

use RuntimeException;

/**
 * Abstract bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractBookmark
{
    /**
     * By default, all bookmarks are not assumed to have been preformatted.
     * This indicates that the parser should replace any relevant characters
     * such as newlines with its own representation (e.g. \par, <br>, etc)
     */
    const PREFORMATTED = false;

    protected $token = null;

    protected $parser = null;

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function isStatic()
    {
        return static::TYPE === 'static';
    }

    public function isPreformatted()
    {
        return static::PREFORMATTED;
    }

    public function getSnippet($className = null)
    {
        if ($className === null) {
            $className = explode('\\', get_called_class());
            $className = array_pop($className);
        }

        $fileExt = $this->getParser()->getFileExtension();
        $path = __DIR__ . '/../Snippet/' . $className . '.' . $fileExt;

        return file_get_contents($path);
    }

    public function setParser($parser)
    {
        $this->parser = $parser;
    }

    public function getParser()
    {
        return $this->parser;
    }

    abstract public function render();
}
