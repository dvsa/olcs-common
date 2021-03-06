<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms\BusRegStatus;
use Common\Data\Object\Search\Aggregations\Terms\TrafficArea;
use Common\Util\Escape;

/**
 * Class BusReg
 * @package Common\Data\Object\Search
 */
class BusReg extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Bus registrations';
    /**
     * @var string
     */
    protected $key = 'bus_reg';

    /**
     * @var string
     */
    protected $searchIndices = 'busreg';

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
                new TrafficArea(),
                new BusRegStatus(),
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
            [
                'title' => 'Registration number',
                'name'=> 'regNo',
                'formatter' => function ($data) {

                    return '<a href="/licence/'
                    . $data['licId'] . '/bus/' . $data['busregId']
                    . '/details">' . $data['regNo'] . '</a>';
                }
            ],
            [
                'title' => 'Operator name',
                'name'=> 'orgName',
                'formatter' => function ($data, $column, $sl) {
                    $url = $sl->get('Helper\Url')->fromRoute(
                        'operator/business-details',
                        ['organisation' => $data['orgId']]
                    );
                    return '<a href="' . $url . '">' . Escape::html($data['orgName']) . '</a>';
                }
            ],
            ['title' => 'Variation number', 'name'=> 'variationNo'],
            ['title' => 'Status', 'name'=> 'busRegStatus'],
            [
                'title' => 'Date first registered / cancelled',
                'formatter' => 'Date',
                'name'=> 'date_1stReg'
            ],
            ['title' => 'Service number', 'name'=> 'serviceNo'],
            ['title' => 'Start point', 'name'=> 'startPoint'],
            ['title' => 'Finish point', 'name'=> 'finishPoint']
        ];
    }
}
