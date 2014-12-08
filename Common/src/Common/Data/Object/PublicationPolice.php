<?php

namespace Common\Data\Object;

use Zend\Stdlib\ArrayObject;

/**
 * Class Publication
 * @package Common\Data\Object\PublicationPolice
 */
class PublicationPolice extends ArrayObject
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $publicationLinkId;

    /**
     * @var string
     */
    public $olbsDob;

    /**
     * @var int
     */
    public $olbsId;

    /**
     * @var string
     */
    public $birthDate;

    /**
     * @var string
     */
    public $familyName;

    /**
     * @var string
     */
    public $forename;
}