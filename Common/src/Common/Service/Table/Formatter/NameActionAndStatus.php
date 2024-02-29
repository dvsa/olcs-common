<?php

namespace Common\Service\Table\Formatter;

use Common\Rbac\Traits\Permission;
use Common\Util\Escape;
use LmcRbacMvc\Service\AuthorizationService;

class NameActionAndStatus implements FormatterPluginManagerInterface
{
   use Permission;

    public const BUTTON_FORMAT = '<button data-prevent-double-click="true" class="action-button-link" role="link" '
    . 'data-module="govuk-button" type="submit" name="table[action][edit][%d]">%s</button>';

    public function __construct(AuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Format a name with default edit action & associated status
     *
     * @param array $data   data row
     * @param array $column column specification
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $title = !empty($data['title']['description']) ? $data['title']['description'] . ' ' : '';
        $name = Escape::html($title . $data['forename'] . ' ' . $data['familyName']);
        $newMarker = '';

        if (isset($data['status']) && ($data['status'] == 'new')) {
            $newMarker = ' <span class="overview__status green">New</span>';
        }

        if ($this->isInternalReadOnly()) {
            return $name . $newMarker;
        }

        return sprintf(self::BUTTON_FORMAT, intval($data['id']), $name) . $newMarker;
    }
}
