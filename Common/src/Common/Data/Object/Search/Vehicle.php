<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\Util\Escape;

/**
 * Class Vehicle
 * @package Common\Data\Object\Search
 */
class Vehicle extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Vehicle';
    /**
     * @var string
     */
    protected $key = 'vehicle';

    /**
     * @var string
     */
    protected $searchIndices = 'vehicle_current|vehicle_removed';

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
            ];
        }

        return $this->filters;
    }

    public function getSettings()
    {
        $settings = parent::getSettings();

        return array_merge(
            $settings,
            [
                'crud' => array(
                    'actions' => array(
                        'vehicleSet26' => array(
                            'class' => 'govuk-button govuk-button--secondary js-require--multiple',
                            'requireRows' => true,
                            'label' => 'Set Sec26',
                        ),
                        'vehicleRemove26' => array(
                            'class' => 'govuk-button govuk-button--secondary js-require--multiple',
                            'requireRows' => true,
                            'label' => 'Remove section 26'
                        ),
                    )
                ),
            ]
        );
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return [
            [
                'title' => 'Licence number',
                'name'=> 'licNo',
                'formatter' => function ($data) {
                    return '<a class="govuk-link" href="/licence/' . $data['licId'] . '">' . $data['licNo'] . '</a>';
                }
            ],
            ['title' => 'Licence status', 'name'=> 'licStatusDesc'],
            [
                'title' => 'Operator name',
                'name'=> 'orgName',
                'formatter' => function ($data, $column, $sl) {
                    $url = $sl->get('Helper\Url')->fromRoute(
                        'operator/business-details',
                        ['organisation' => $data['orgId']]
                    );
                    return '<a class="govuk-link" href="' . $url . '">' . Escape::html($data['orgName']) . '</a>';
                }
            ],
            [
                'title' => 'VRM',
                'formatter' => function ($data) {
                    $section26 = (isset($data['section_26']) && $data['section_26']) ? ' (sec26)' : '';
                    return $data['vrm'] . $section26;
                }
            ],
            ['title' => 'Disc Number', 'name'=> 'discNo'],
            [
                'title' => 'Specified date',
                'formatter' => 'Date',
                'name'=> 'specifiedDate'
            ],
            [
                'title' => 'Removed date',
                'formatter' => 'Date',
                'name'=> 'removalDate'
            ],
            [
                'title' => '',
                'width' => 'checkbox',
                'type' => 'Checkbox',
                'idIndex' => 'vehId',
            ],
        ];
    }
}
