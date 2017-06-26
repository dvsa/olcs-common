<?php

namespace Common\Service\Table\Formatter;

/**
 * @author Dmitry Golubev <d.e.golubev@gmail.com>
 */
class CaseEntityNrStatus implements FormatterInterface
{
    const URL_TEMPLATE = '<a href="%s">%s</a>';

    const TEMPLATE_LIC = '%s (%s)';
    const TEMPLATE_APP = '%s (%s)<br />/%s (%s)';

    /**
     * Return traffic area name
     *
     * @param array                               $data   Data
     * @param array                               $column Column data
     * @param \Zend\ServiceManager\ServiceManager $sm     Service manager
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $hlprUrl = $sm->get('Helper\Url');

        $typeId = $data['caseType']['id'];

        //  transport manager
        if ($typeId === \Common\RefData::CASE_TYPE_TM) {
            $tmId = $data['transportManager']['id'];

            return sprintf(
                self::URL_TEMPLATE,
                $hlprUrl->fromRoute('transport-manager', ['transportManager' => $tmId]),
                $tmId
            );
        }

        //  licence
        $lic = $data['licence'];

        $licLink = sprintf(
            self::URL_TEMPLATE,
            $hlprUrl->fromRoute('lva-licence', ['licence' => $lic['id']]),
            $lic['licNo']
        );

        $licStatus = $lic['status']['description'];

        if ($typeId === \Common\RefData::CASE_TYPE_LICENCE) {
            return sprintf(self::TEMPLATE_LIC, $licLink, $licStatus);
        }

        //  application
        $app = $data['application'];
        $appId = $app['id'];

        $appLink = sprintf(
            self::URL_TEMPLATE,
            $hlprUrl->fromRoute('lva-application', ['application' => $appId]),
            $appId
        );

        $appStatus = $app['status']['description'];

        return sprintf(self::TEMPLATE_APP, $licLink, $licStatus, $appLink, $appStatus);
    }
}
