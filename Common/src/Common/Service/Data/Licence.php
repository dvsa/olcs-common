<?php

namespace Common\Service\Data;

use Dvsa\Olcs\Transfer\Query\Licence\Licence as LicenceQry;
use Dvsa\Olcs\Transfer\Query\Licence\OperatingCentres as OcQry;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Class Licence
 * @package Olcs\Service
 */
class Licence extends AbstractDataService
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @param integer|null $id
     * @return array
     */
    public function fetchLicenceData($id = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (empty($id)) {
            return [];
        }

        if (is_null($this->getData($id))) {
            $dtoData = LicenceQry::create(['id' => $id]);
            $response = $this->handleQuery($dtoData);
            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }
            $data = $response->getResult();
            $this->setData($id, $data);
        }
        return $this->getData($id);
    }

    /**
     * Fetches an array of OperatingCentres for the licence.
     * @param null $id
     * @return array
     */
    public function fetchOperatingCentreData($id = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData('oc_' .$id))) {
            $dtoData = OcQry::create(['id' => $id]);
            $response = $this->handleQuery($dtoData);
            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }
            $data = $response->getResult();
            $this->setData('oc_' .$id, $data);
        }

        return $this->getData('oc_' . $id);
    }

    /**
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
