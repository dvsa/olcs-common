<?php

namespace Common\Data\Object;

/**
 * Class Publication
 * @package Common\Data\Object\Publication
 */
class Publication
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

public $hearingData;

    /**
     * @return int
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param int $application
     * @return $this
     */
    public function setApplication($application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * @return int
     */
    public function getBusReg()
    {
        return $this->busReg;
    }

    /**
     * @param $busReg
     * @return $this
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @param $licence
     * @return $this
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrigPubDate()
    {
        return $this->origPubDate;
    }

    /**
     * @param $origPubDate
     * @return $this
     */
    public function setOrigPubDate($origPubDate)
    {
        $this->origPubDate = $origPubDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getPi()
    {
        return $this->pi;
    }

    /**
     * @param $pi
     * @return $this
     */
    public function setPi($pi)
    {
        $this->pi = $pi;
        return $this;
    }

    /**
     * @return string
     */
    public function getPubType()
    {
        return $this->pubType;
    }

    /**
     * @param $pubType
     * @return $this
     */
    public function setPubType($pubType)
    {
        $this->pubType = $pubType;
        return $this;
    }

    /**
     * @return int
     */
    public function getPublicationId()
    {
        return $this->publicationId;
    }

    /**
     * @param $publicationId
     * @return $this
     */
    public function setPublicationId($publicationId)
    {
        $this->publicationId = $publicationId;
        return $this;
    }

    /**
     * @return int
     */
    public function getPublicationNo()
    {
        return $this->publicationNo;
    }

    /**
     * @param $publicationNo
     * @return $this
     */
    public function setPublicationNo($publicationNo)
    {
        $this->publicationNo = $publicationNo;
        return $this;
    }

    /**
     * @return int
     */
    public function getPublicationSection()
    {
        return $this->publicationSection;
    }

    /**
     * @param $publicationSection
     * @return $this
     */
    public function setPublicationSection($publicationSection)
    {
        $this->publicationSection = $publicationSection;
        return $this;
    }

    /**
     * @return string
     */
    public function getText1()
    {
        return $this->text1;
    }

    /**
     * @param $text1
     * @return $this
     */
    public function setText1($text1)
    {
        $this->text1 = $text1;
        return $this;
    }

    /**
     * @return string
     */
    public function getText2()
    {
        return $this->text2;
    }

    /**
     * @param $text2
     * @return $this
     */
    public function setText2($text2)
    {
        $this->text2 = $text2;
        return $this;
    }

    /**
     * @return string
     */
    public function getText3()
    {
        return $this->text3;
    }

    /**
     * @param $text3
     * @return $this
     */
    public function setText3($text3)
    {
        $this->text3 = $text3;
        return $this;
    }

    /**
     * @return int
     */
    public function getTmPiHearing()
    {
        return $this->tmPiHearing;
    }

    /**
     * @param $tmPiHearing
     * @return $this
     */
    public function setTmPiHearing($tmPiHearing)
    {
        $this->tmPiHearing = $tmPiHearing;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * @param $trafficArea
     * @return $this
     */
    public function setTrafficArea($trafficArea)
    {
        $this->trafficArea = $trafficArea;
        return $this;
    }
}
