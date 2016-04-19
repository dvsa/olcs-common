<?php

namespace Common\Service\Table\Formatter\TaskAllocationRule;

/**
 * TaskAllocationRuleCriteria
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Criteria implements \Common\Service\Table\Formatter\FormatterInterface
{
    /**
     * Comment value
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data)
    {
        if (
            isset($data['goodsOrPsv']['id']) &&
            $data['goodsOrPsv']['id'] === \Common\RefData::LICENCE_CATEGORY_GOODS_VEHICLE
        ) {
            $content = ($data['isMlh'] === true) ? 'Goods, MLH' : 'Goods, Non-MLH';
        } else {
            $content = 'N/A';

        }

        return $content;
    }
}
