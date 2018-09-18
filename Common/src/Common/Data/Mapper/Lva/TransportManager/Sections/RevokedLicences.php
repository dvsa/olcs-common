<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;


class RevokedLicences extends AbstractSection implements TransportManagerSectionInterface
{
    use SectionSerializeTrait;

    private $revokedLicences;

    public function populate(array $transportManagerApplication)
    {
        $this->revokedLicences = 'None added';
        $revokedLicemces = $transportManagerApplication['transportManager'][''];

    }
}