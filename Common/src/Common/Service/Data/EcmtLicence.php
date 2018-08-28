<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Common\Service\Data\AbstractDataService;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Dvsa\Olcs\Transfer\Query\Organisation\Organisation;

/**
 * Class EcmtLicence
 *
 * @package Common\Service\Data
 */
class EcmtLicence extends AbstractDataService implements ListData
{

    /**
     * Format data
     *
     * @param array $data Data
     *
     * @return array
     */
    public function formatData(array $data)
    {
        $optionData = [];

        foreach ($data as $item) {

            $licence = [];
            $licence['value'] = $item['id'];
            $licence['label'] = $item['licNo'] . ' (' . $item['trafficArea'] . ')';

            if($item['licenceType']['id'] === \Common\RefData::LICENCE_TYPE_RESTRICTED) {
                $licence['attributes'] = [
                    'class' => 'input--trips restricted-licence '
                ];
                $licence['label_attributes'] = [
                    'class' => 'form-control form-control--radio restricted-licence-label '
                ];
                $optionData[] = $licence;

                $licence = [];
                $licence['value'] = '';
                $licence['label'] = 'permits.form.ecmt-licence.restricted-licence.hint';
                $licence['label_attributes'] = [
                    'class' => 'form-control form-control--radio restricted-licence-hint'
                ];
                $licence['attributes'] = [
                    'class' => 'input--trips visually-hidden'
                ];
                $optionData[] = $licence;
            } else {
                $optionData[] = $licence;
            }

        }

        return $optionData;
    }

    /**
     * Fetch list options
     *
     * @param array|string $context   Context
     * @param bool         $useGroups Use groups
     *
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $data = $this->fetchListData();

        if (!$data) {
            return [];
        }

        return $this->formatData($data);
    }

    /**
     * Fetch list data
     *
     * @return array
     */

    public function fetchListData()
    {

        if (is_null($this->getData('Organisation'))) {

            $authenticationService = $this->getServiceLocator()->get('Common\Rbac\IdentityProvider');
            $user = $authenticationService->getIdentity();

            if (empty($user->getUserData()['organisationUsers'])) {
                throw new Exception('no-organisation-error');
            }
            $params = [
                'id' => $user->getUserData()['organisationUsers'][0]['organisation']['id']
            ];

            $dtoData = Organisation::create($params);
            $response = $this->handleQuery($dtoData);

            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }

            $this->setData('Organisation', false);

            if (isset($response->getResult()['eligibleEcmtLicences']['result'])) {
                $this->setData('Organisation', $response->getResult()['eligibleEcmtLicences']['result']);
            }
        }

        return $this->getData('Organisation');
    }
}
