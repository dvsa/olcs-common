<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\Service\Table\Formatter\Date;
use Common\Service\Table\Formatter\Translate;

/**
 * Class People
 * @package Common\Data\Object\Search
 */
class PeopleSelfserve extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'People';

    /**
     * @var string
     */
    protected $key = 'people';

    /**
     * @var string
     */
    protected $searchIndices = 'person';

    /**
     * Contains an array of the instantiated filters classes.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        return [
            'paginate' => [
                'limit' => [
                    'options' => [10, 25, 50, 100]
                ]
            ],
            'layout' => 'headless'
        ];
    }

    /**
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        if (empty($this->filters)) {

            $this->filters = [
                new Filter\OrgType(),
                new Filter\LicenceType(),
                new Filter\LicenceStatus(),
                new Filter\GoodsOrPsv(),
            ];
        }

        return $this->filters;
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return [
            [
                'title' => 'Licence number',
                'name'=> 'licNo',
                'formatter' => static fn($data) => '<a class="govuk-link" href="/view-details/licence/' . $data['licId'] . '">' . $data['licNo'] . '</a>'
            ],
            [
                'title' => 'Licence status',
                'name'=> 'licStatusDesc',
                'formatter'=> Translate::class,
            ],
            [
                'title' => 'Operator name',
                'name'=> 'orgName',
                'formatter' => static fn($data) => $data['orgName']
            ],
            [
                'title' => 'Name',
                'formatter' => static fn($row) => $row['personFullname']
            ],
            [
                'permissionRequisites' => ['partner-user', 'partner-admin'],
                'title' => 'Date of birth',
                'formatter' => Date::class,
                'name' => 'personBirthDate'
            ]
        ];
    }
}
