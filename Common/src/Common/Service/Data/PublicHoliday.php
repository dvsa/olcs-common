<?php

namespace Common\Service\Data;

use Common\Util\RestClient;
use DateTime as PHPDateTime;
use Common\Service\Data\Licence as LicenceService;

/**
 * Class PublicHoliday
 *
 * @package Common\Service\Data
 */
class PublicHoliday extends AbstractData
{
    protected $categories = [
        'isScotland',
        'isWales',
        'isNi',
        'isEngland',
    ];

    protected $serviceName = 'PublicHoliday';

    /**
     * @var LicenceService
     */
    protected $licenceService;

    /**
     * @param LicenceService $licenceService
     * @return $this
     */
    public function setLicenceService(LicenceService $licenceService)
    {
        $this->licenceService = $licenceService;
        return $this;
    }

    /**
     * @return LicenceService
     */
    public function getLicenceService()
    {
        return $this->licenceService;
    }

    public function getList(array $data = null)
    {
        $data = $this->getRestClient()->get('', $data);

        if (!isset($data['Results']) || empty($data['Results'])) {
            return null;
        }

        return $data['Results'];
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @param $category
     * @return array
     */
    public function fetchPublicHolidays(PHPDateTime $dateFrom, PHPDateTime $dateTo)
    {
        // Why? Because dates are objects which are passed by reference,
        // when we modify $dateTo below, that would have an effetc elsewhere.
        $dateFrom = clone $dateFrom;
        $dateTo = clone $dateTo;

        /* $licenceService = $this->getLicenceService();
        if ($licenceService !== null && $licenceService->getId() !== null) {
            $category = $this->categories[$licenceService->fetchLicenceData()['trafficArea']];
        } else { */
            $category = 'isEngland';
        /* } */

        $dateTo->add(\DateInterval::createFromDateString('14 Days'));

        $params = [
            'publicHolidayDate' => '=>' . $dateFrom->format('Y-m-d'),
            'publicHolidayDate' => '<=' . $dateTo->format('Y-m-d'),
            'limit' => '10000'
        ];

        if ( (null === $this->getData($category)) && (null !== ($data = $this->getList($params))) ) {

            $this->setData($category, $data);
        }

        return $this->getData($category);
    }

    public function fetchPublicHolidaysArray(PHPDateTime $dateFrom, PHPDateTime $dateTo)
    {
        return $this->formatData($this->fetchPublicHolidays($dateFrom, $dateTo));
    }

    public function formatData($data)
    {
        $outdata = [];

        foreach ($data as $item) {
            $outdata[] = $item['publicHolidayDate'];
        }

        return $outdata;
    }
}
