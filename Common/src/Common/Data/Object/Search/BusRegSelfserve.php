<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms\BusRegStatus;
use Common\Data\Object\Search\Aggregations\Terms\TrafficArea;
use Common\Util\Escape;

/**
 * Class BusReg
 * @package Common\Data\Object\Search
 */
class BusRegSelfserve extends InternalSearchAbstract
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
        if (count($this->filters) === 0) {
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
                'formatter' => function ($data, $column, $serviceLocator) {
                    $url = $serviceLocator->get('Helper\Url')->fromRoute(
                        'search-bus/details',
                        ['busRegId' => $data['busregId']]
                    );
                    return sprintf(
                        '<a href="%s">%s</a><br/>%s',
                        $url,
                        Escape::html($data['regNo']),
                        $data['busRegStatus']
                    );
                }
            ],
            [
                'title' => 'Operator name',
                'name'=> 'orgName',
                'formatter' => function ($data) {
                    return Escape::html($data['orgName']);
                },
            ],
            ['title' => 'Service number', 'name'=> 'serviceNo'],
            ['title' => 'Start point', 'name'=> 'startPoint'],
            ['title' => 'Finish point', 'name'=> 'finishPoint'],
        ];
    }
}
