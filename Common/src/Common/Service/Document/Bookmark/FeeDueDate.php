<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Document\Bookmark\Interfaces\DateHelperAwareInterface;
use Common\Service\Document\Bookmark\Formatter\Date;
use Common\Service\Helper\DateHelperService;
use DateTime;

/**
 * Fee due date bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeeDueDate extends DynamicBookmark implements DateHelperAwareInterface
{
    const TARGET_DAYS = 15;

    private $dateHelper;

    public function setDateHelper(DateHelperService $helper)
    {
        $this->dateHelper = $helper;
    }

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Fee',
            'data' => [
                'id' => $data['fee']
            ],
            'bundle' => []
        ];

        return $query;
    }

    public function render()
    {
        $target = $this->dateHelper->calculateDate(
            $this->data['invoicedDate'],
            self::TARGET_DAYS,
            // ignore weekends
            true,
            // ignore public holidays
            true
        );

        return Date::format([$target]);
    }
}
