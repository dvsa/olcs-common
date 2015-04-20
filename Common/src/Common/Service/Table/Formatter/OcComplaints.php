<?php

/**
 * OcComplaints.php
 */
namespace Common\Service\Table\Formatter;

/**
 * Class OcComplaints
 *
 * Format results for the table.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class OcComplaints implements FormatterInterface
{
    /**
     * Get the complaints for the operating centre and return them.
     *
     * @param array $data The row data.
     * @param array $column The column data.
     * @param null $sm The service manager.
     *
     * @return mixed
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $count = 0;

        foreach ($data['operatingCentre']['ocComplaints'] as $complaint) {
            if (!is_null($complaint['complaint'])) {
                $count++;
            }
        }

        return $count;
    }
}
