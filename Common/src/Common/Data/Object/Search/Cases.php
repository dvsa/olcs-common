<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;

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
                'formatter' => function ($data) {

                    //die('<pre>' . print_r($data, 1));

                    return '<a href="/case/details/' . $data['caseId'] . '">' . $data['caseId'] . '</a>';
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
                'formatter' => function ($data) {
                    if (!empty($data['tmId'])) {
                        return '<a href="/transport-manager/' . $data['tmId'] . '">'
                               . $data['tmForename'] . ' '
                               . $data['tmFamilyName']
                               . '</a>';

                    } else {

                        return '<a href="/operator/' . $data['orgId'] . '">' . $data['orgName'] . '</a>';

                    }
                }
            ],
            ['title' => 'Case status', 'name'=> 'caseStatusDesc'],
        ];
    }
}
