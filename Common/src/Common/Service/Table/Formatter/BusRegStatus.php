<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceLocatorInterface;

class BusRegStatus implements FormatterInterface
{
    /**
     * @param array                        $data   data array
     * @param array                        $column column info
     * @param null|ServiceLocatorInterface $sm     service locator
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        //standardise the format of the data, so this can be used by multiple tables
        //we set the data even if the busReg key is blank
        if (array_key_exists('busReg', $data)) {
            $data = $data['busReg'];
        }

        $translator = $sm->get('translator');

        /** @var \Common\View\Helper\Status $statusHelper */
        $statusHelper = $sm->get('ViewHelperManager')->get('status');

        //status field will be different, depending on whether the data has come from bus reg applications,
        //txc inbox or ebsr submission table
        if (isset($data['busRegStatus'])) {
            $statusId = $data['busRegStatus'];
            $statusDescription = $data['busRegStatusDesc'];
        } else {
            $statusId = $data['status']['id'];
            $statusDescription = $data['status']['description'];
        }

        $status = [
            'id' => $statusId,
            'description' => $translator->translate($statusDescription),
        ];

        return $statusHelper->__invoke($status);
    }
}
