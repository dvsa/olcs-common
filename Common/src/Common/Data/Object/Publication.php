<?php

namespace Common\Data\Object;
use Zend\Stdlib\ArrayObject;

/**
 * Class Publication
 * @package Common\Data\Object\Publication
 */
class Publication extends ArrayObject
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $publication;

    /**
     * @var int
     */
    public $publicationNo;

    /**
     * @var string
     */
    public $trafficArea;

    /**
     * @var string
     */
    public $pubType;

    /**
     * @var int
     */
    public $application;

    /**
     * @var int
     */
    public $licence;

    /**
     * @var int
     */
    public $pi;

    /**
     * @var int
     */
    public $tmPiHearing;

    /**
     * @var int
     */
    public $busReg;

    /**
     * @var string
     */
    public $text1;

    /**
     * @var string
     */
    public $text2;

    /**
     * @var string
     */
    public $text3;

    /**
     * @var int
     */
    public $publicationSection;

    /**
     * @var string
     */
    public $origPubDate;
}
