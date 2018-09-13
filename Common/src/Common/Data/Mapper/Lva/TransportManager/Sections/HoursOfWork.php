<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;


class HoursOfWork extends AbstractSection implements TransportManagerSectionInterface
{
    use SectionSerializeTrait;

    private $hoursMon;
    private $hoursTue;
    private $hoursWed;
    private $hoursThu;
    private $hoursFri;
    private $hoursSat;
    private $hoursSun;

    /**
     * @param mixed $hoursMon
     */
    public function setHoursMon($hoursMon): void
    {
        $this->hoursMon = $hoursMon;
    }

    /**
     * @param mixed $hoursTue
     */
    public function setHoursTue($hoursTue): void
    {
        $this->hoursTue = $hoursTue;
    }

    /**
     * @param mixed $hoursWed
     */
    public function setHoursWed($hoursWed): void
    {
        $this->hoursWed = $hoursWed;
    }

    /**
     * @param mixed $hoursThu
     */
    public function setHoursThu($hoursThu): void
    {
        $this->hoursThu = $hoursThu;
    }

    /**
     * @param mixed $hoursFri
     */
    public function setHoursFri($hoursFri): void
    {
        $this->hoursFri = $hoursFri;
    }

    /**
     * @param mixed $hoursSat
     */
    public function setHoursSat($hoursSat): void
    {
        $this->hoursSat = $hoursSat;
    }

    /**
     * @param mixed $hoursSun
     */
    public function setHoursSun($hoursSun): void
    {
        $this->hoursSun = $hoursSun;
    }


    public function populate(array $transportManagerApplication)
    {
        $properties = array_keys(get_object_vars($this));
        array_map(function ($v) use ($transportManagerApplication) {
            $this->{'set' . ucfirst($v)}($transportManagerApplication[$v]);
        }, $properties);

        return $this;
    }
}
