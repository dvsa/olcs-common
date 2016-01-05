<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Dvsa\Olcs\Transfer\Query\RefData\RefDataList;
use Common\Service\Data\AbstractDataService;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Class RefData
 * @package Common\Service
 */
class RefData extends AbstractDataService implements ListData
{
    use ListDataTrait;

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @param $category
     * @return array
     */
    public function fetchListData($category)
    {
        if (is_null($this->getData($category))) {

            $languagePreferenceService = $this->getServiceLocator()->get('LanguagePreference');
            $params = [
                'refDataCategory' => $category,
                'language' => $languagePreferenceService->getPreference()
            ];
            $this->setData($category, false);
            $dtoData = RefDataList::create($params);

            $response = $this->handleQuery($dtoData);
            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }
            $this->setData($category, false);
            if (isset($response->getResult()['results'])) {
                $this->setData($category, $response->getResult()['results']);
            }
        }

        return $this->getData($category);
    }
}
