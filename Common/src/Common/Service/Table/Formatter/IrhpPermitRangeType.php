<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Zend\ServiceManager\ServiceManager;

/**
 * IRHP Permit Range table - Type column formatter
 */
class IrhpPermitRangeType implements FormatterInterface
{
    /**
     * Format
     *
     * Returns a formatted column
     *
     * @param array          $data
     * @param array          $column
     * @param ServiceManager $sm
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (!$data['irhpPermitStock']['irhpPermitType']['isBilateral']) {
            return 'N/A';
        }

        $key = sprintf(
            'permits.irhp.range.type.%s.%s',
            $data['cabotage'] ? 'cabotage' : 'standard',
            $data['journey']['id'] == RefData::JOURNEY_SINGLE ? 'single' : 'multiple'
        );

        return $sm->get('translator')->translate($key);
    }
}
