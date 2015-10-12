<?php

namespace Common\Data\Object\Search;
use Common\Data\Object\Search\Aggregations\Terms\BusRegStatus;
use Common\Data\Object\Search\Aggregations\Terms\TrafficArea;

/**
 * Class BusReg
 * @package Common\Data\Object\Search
 */
class BusRegSelfServe extends InternalSearchAbstract
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
                'title' => 'Registration no',
                'name'=> 'regNo',
                'formatter' => function ($data) {

                    return '<a href="/view-details/licence/'
                    . $data['licId'] . '/bus/' . $data['busregId']
                    . '/details">' . $data['regNo'] . '</a> <br>' . $data['busRegStatus'];
                }
            ],
            [
                'title' => 'Operator name',
                'name'=> 'orgName',
                'formatter' => function ($data) {

                    $orgName = $data['orgName'];

                    return $orgName;
                }
            ],
            ['title' => 'Variation number', 'name'=> 'variationNo'],
            [
                'title' => 'Date first registered / cancelled',
                'formatter' => 'Date',
                'name'=> 'date_1stReg'
            ],
            ['title' => 'Service no', 'name'=> 'serviceNo'],
            ['title' => 'Start point', 'name'=> 'startPoint'],
            ['title' => 'Finish point', 'name'=> 'finishPoint']
        ];
    }
}
