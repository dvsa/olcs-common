<?php

/**
 * File
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\File;

/**
 * File
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class File
{
    /**
     * Holds the identifier
     *
     * @var string
     */
    protected $identifier;

    /**
     * Holds the name
     *
     * @var string
     */
    protected $name;

    /**
     * Holds the path
     *
     * @var string
     */
    protected $path;

    /**
     * Holds the file size
     *
     * @var int
     */
    protected $size;

    /**
     * Holds the actual file content
     *
     * @var string
     */
    protected $content;

    /**
     * Setter for identifier
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Getter for identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Setter for name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Getter for name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter for path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Getter for path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Setter for size
     *
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Getter for size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Setter for content
     *
     * @param int $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Getter for content
     *
     * @return int
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getExtension()
    {
        if (empty($this->name)) {
            // we may want to handle this differently, unsure of rules as of yet
            return '';
        }

        $extPos = strrpos($this->name, '.');
        if ($extPos === false) {
            return '';
        }

        return substr($this->name, $extPos + 1);
    }

    public function getRealType()
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($this->getContent());
    }

    /**
     * Populate properties from data
     *
     * @param array $data
     */
    public function fromData($data)
    {
        $propertyMap = array(
            'name' => array('name', 'filename'),
            'path' => array('tmp_name'),
            'size' => array('size'),
            'content' => array('content'),
            'identifier' => array('identifier')
        );

        foreach ($data as $key => $value) {
            foreach ($propertyMap as $name => $map) {
                if (in_array($key, $map)) {
                    $this->{'set' . ucwords($name)}($value);
                    break;
                }
            }
        }
    }

    /**
     * Export properties as array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'identifier' => $this->getIdentifier(),
            'name' => $this->getName(),
            'path' => $this->getPath(),
            'size' => $this->getSize(),
            'content' => $this->getContent()
        );
    }
}
