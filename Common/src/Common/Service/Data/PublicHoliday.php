<?php

namespace Common\Service\Data;

use Common\Util\RestClient;
use DateTime as PHPDateTime;
use Common\Service\Data\Licence as LicenceService;
use Common\Service\Data\LicenceServiceTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PublicHoliday
 *
 * @package Common\Service\Data
 */
class PublicHoliday extends AbstractData
{
    use LicenceServiceTrait;

    protected $serviceName = 'PublicHoliday';

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
     * @return array
     */
    public function fetchPublicHolidays(PHPDateTime $dateFrom, PHPDateTime $dateTo)
    {
        // Why? Because dates are objects which are passed by reference,
        // when we modify $dateTo below, that would have an effetc elsewhere.
        $dateFrom = clone $dateFrom;
        $dateTo = clone $dateTo;

        $licenceService = $this->getLicenceService();

        $fields = [
            'isScotland',
            'isWales',
            'isNi',
            'isEngland',
        ];

        $fieldToSearch = 'isEngland';

        $licence = $licenceService->fetchLicenceData();

        if ($licence) {

            $trafficAreaArray = $licence['trafficArea'];
            foreach ($fields as $key) {
                if (array_key_exists($key, $trafficAreaArray) && $trafficAreaArray[$key] === true) {
                    $fieldToSearch = $key;
                }
            }
        }

        // define the limit as a reasonable multiple of number of days the original difference to ensure we cover
        $limit = ceil(($dateTo->diff($dateFrom, true)->format('%a') * 0.04))+10;

        $params = [
            $fieldToSearch => '1',
            'limit' => $limit,
            'sort' => 'publicHolidayDate'
        ];
        if ($dateFrom < $dateTo) {
            $params['publicHolidayDate'] = '>=' . $dateFrom->format('Y-m-d');
            $params['order'] = 'ASC';
        } else {
            $params['publicHolidayDate'] = '<=' . $dateFrom->format('Y-m-d');
            $params['order'] = 'DESC';
        }

        // OLCS-11222-cache-fix
        // set category to a unique string to reduce the number of DB calls
        $category = $fieldToSearch . $params['publicHolidayDate'] . $params['order'];

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

        if (is_array($data)) {
            foreach ($data as $item) {
                $outdata[] = $item['publicHolidayDate'];
            }
        }

        return $outdata;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return PublicHoliday
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $licenceService = $serviceLocator->get('DataServiceManager')->get('Common\Service\Data\Licence');

        $this->setLicenceService($licenceService);

        return $this;
    }
}
