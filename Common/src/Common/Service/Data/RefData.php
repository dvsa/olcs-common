<?php

namespace Common\Service\Data;

use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Dvsa\Olcs\Transfer\Query\RefData\RefDataList;

/**
 * Class RefData
 *
 * @package Common\Service\Data
 */
class RefData extends AbstractListDataService
{
    /**
     * Fetch list data
     *
     * @param string $category Category
     *
     * @return array
     * @throw UnexpectedResponseException
     */
    public function fetchListData($category = null)
    {
        if (is_null($this->getData($category))) {

            $languagePreferenceService = $this->getServiceLocator()->get('LanguagePreference');
            $params = [
                'refDataCategory' => $category,
                'language' => $languagePreferenceService->getPreference()
            ];
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
