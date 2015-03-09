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

    /**
     * @NOTE: only jpegs with an extension of .jpeg are supported at
     * the moment. If this needs to change then feel free to alter
     * the API of this method but make sure the RTF parser can handle
     * the new format
     */
    protected function getImage($name, $width = null, $height = null)
    {
        $info = [];
        $type = 'jpeg';
        $path = __DIR__ . '/../Image/' . $name . '.' . $type;

        if (!file_exists($path)) {
            throw new RuntimeException('Image path ' . $path . ' does not exist');
        }

        // it's an extra conditional but no point hitting the disk twice
        // if we don't need to read any metadata...
        if ($width === null || $height === null) {
            $info = getimagesize($path);
        }

        if ($width === null) {
            $width = $info[0];
        }

        if ($height === null) {
            $height = $info[1];
        }

        $data = file_get_contents($path);

        return $this->getParser()->renderImage($data, $width, $height, 'jpeg');
    }

    abstract public function render();
}
