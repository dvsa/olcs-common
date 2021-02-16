<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\RefData;
use Common\Util\Escape;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class Licence
 * @package Common\Data\Object\Search
 */
class Cases extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Case';
    /**
     * @var string
     */
    protected $key = 'case';

    /**
     * @var string
     */
    protected $searchIndices = 'case';

    /**
     * Contains an array of the instantiated filters classes.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        if (empty($this->filters)) {
            $this->filters = [
                new Filter\LicenceStatus(),
                new Filter\ApplicationStatus(),
                new Filter\CaseType(),
                new Filter\CaseStatus(),
            ];
        }

        return $this->filters;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return [
            ['title' => 'Case type', 'name'=> 'caseTypeDesc'],
            [
                'title' => 'Case Id',
                'name'=> 'caseId',
                'formatter' => function ($data, $column, $sl) {
                    $authService = $sl->get(AuthorizationService::class);

                    if ($authService->isGranted(RefData::PERMISSION_INTERNAL_IRHP_ADMIN)) {
                        return Escape::html($data['caseId']);
                    }

                    return '<a href="/case/details/' . $data['caseId'] . '">' . Escape::html($data['caseId']) . '</a>';
                }
            ],
            ['title' => 'Case type', 'name'=> 'caseStatusDesc'],
            [
                'title' => 'Licence number',
                'name'=> 'licNo',
                'formatter' => function ($data) {
                    return '<a href="/licence/' . $data['licId'] . '">' . $data['licNo'] . '</a>';
                }
            ],
            ['title' => 'Licence status', 'name'=> 'licStatusDesc'],
            [
                'title' => 'Application Id',
                'name'=> 'appId',
                'formatter' => function ($data) {
                    if (!empty($data['appId'])) {
                        return '<a href="/application/' . $data['appId'] . '">'
                        . $data['appId']
                        . '</a>';
                    } else {
                        return 'N/a';
                    }
                }
            ],
            [
                'title' => 'Application Status',
                'name'=> 'appStatusDesc',
                'formatter' => function ($data) {
                    if (!empty($data['appStatusDesc'])) {
                        return $data['appStatusDesc'];
                    } else {
                        return 'N/a';
                    }
                }
            ],
            [
                'title' => 'Name',
                'formatter' => function ($data, $column, $sl) {
                    $urlHelper = $sl->get('Helper\Url');

                    if (!empty($data['tmId'])) {
                        $url = $urlHelper->fromRoute(
                            'transport-manager/details',
                            ['transportManager' => $data['tmId']]
                        );
                        $link = $data['tmForename'] . ' ' . $data['tmFamilyName'];
                    } else {
                        $url = $urlHelper->fromRoute(
                            'operator/business-details',
                            ['organisation' => $data['orgId']]
                        );
                        $link = $data['orgName'];
                    }

                    $authService = $sl->get(AuthorizationService::class);

                    if ($authService->isGranted(RefData::PERMISSION_INTERNAL_IRHP_ADMIN)) {
                        return Escape::html($link);
                    }

                    return '<a href="' . $url . '">' . Escape::html($link) . '</a>';
                }
            ],
            ['title' => 'Case status', 'name'=> 'caseStatusDesc'],
        ];
    }
}
