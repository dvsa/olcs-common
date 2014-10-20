<?php
namespace Common\Service\Document\Bookmark\Base;

/**
 * Abstract bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractBookmark
{
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

    public function getSnippet()
    {
        $className = explode('\\', get_called_class());
        $className = array_pop($className);

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
