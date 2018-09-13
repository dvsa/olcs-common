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

    private $ownerTm;

    private $typeOfTransportManager;

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
     *
     * @param array $transportManagerApplication
     */
    public function populate(array $transportManagerApplication)
    {
        $this->setOwnerTm($transportManagerApplication['isOwner']);

        $this->setTypeOfTransportManager($transportManagerApplication['tmType']['description']);

        return $this;
    }

}
