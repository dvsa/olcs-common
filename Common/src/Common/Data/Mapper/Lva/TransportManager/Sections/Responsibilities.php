<?php

namespace Common\Data\Mapper\Lva\TransportManager\Sections;

/**
 * Class Responsibilities
 *
 * @package Common\Data\Mapper\Lva\TransportManager\Sections
 */
class Responsibilities extends AbstractSection implements TransportManagerSectionInterface
{
    use SectionSerializeTrait;

    private $typeOfTransportManager;

    private $ownerTm;

    /**
     * @param mixed $ownerTm
     */
    public function setOwnerTm($ownerTm): void
    {
        $this->ownerTm = $ownerTm;
    }

    /**
     * @param mixed $typeOfTransportManager
     */
    public function setTypeOfTransportManager($typeOfTransportManager): void
    {
        $this->typeOfTransportManager = $typeOfTransportManager;
    }

    /**
     * populate
     */
    public function populate(array $transportManagerApplication)
    {
        $this->setOwnerTm($transportManagerApplication['isOwner']);

        $this->setTypeOfTransportManager($transportManagerApplication['tmType']['description']);

        return $this;
    }
}
