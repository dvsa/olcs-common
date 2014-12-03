<?php

namespace Common\Data\Object;

/**
 * Class Bundle
 * @package Common\Data\Object
 */
class Bundle implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $children = [];

    /**
     * @var array
     */
    protected $criteria = [];

    /**
     * Addsa a child to the bundle
     *
     * @param $name
     * @param Bundle $bundle
     * @return $this
     */
    public function addChild($name, Bundle $bundle = null)
    {
        if ($bundle) {
            $this->children[$name] = $bundle;
        } else {
            $this->children[] = $name;
        }

        return $this;
    }

    /**
     * Adds a criteria to the bundle, either pass an array containing the fields and values to use in an OR statement or
     * pass both field and value to add an AND statement.
     *
     * This method probably needs expanding to provide more powerful filtering capabilities.
     *
     * @param $field
     * @param null $value
     * @return $this
     */
    public function addCriteria($field, $value = null)
    {
        if (is_array($field)) {
            $this->criteria[] = $field;
        } else {
            $this->criteria[$field] = $value;
        }

        return $this;
    }

    /**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 */
    public function jsonSerialize()
    {
        $return = [];

        if (!empty($this->children)) {
            $return['children'] = $this->children;
        }

        if (!empty($this->criteria)) {
            $return['criteria'] = $this->criteria;
        }

        return $return;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return json_encode($this);
    }
}
