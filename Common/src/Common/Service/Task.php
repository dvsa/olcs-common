<?php

namespace Common\Service;

/**
 * Class Task
 * @package Common\Service
 */
class Task
{
    const NR_SUB_CATEGORY_DEFAULT = 47;
    const NR_CATEGORY_DEFAULT = 2;
    const NR_TEAM_DEFAULT = 2;
    const NR_USER_DEFAULT = 1;
    const NR_URGENT_DEFAULT = 'Y';
    const NR_DEFAULT_DESCRIPTION = 'ERRU case has been automatically created';

    /**
     * @param int $caseId
     * @param int $licenceId
     * @return array
     */
    public function createNrTask($caseId, $licenceId)
    {
        $data['case'] = $caseId;
        $data['licence'] = $licenceId;
        $data['assignedToTeam'] = self::NR_TEAM_DEFAULT;
        $data['assignedToUser'] = self::NR_USER_DEFAULT;
        $data['category'] = self::NR_CATEGORY_DEFAULT;
        $data['subCategory'] = self::NR_SUB_CATEGORY_DEFAULT;
        $data['urgent'] = self::NR_URGENT_DEFAULT;
        $data['openDate'] = date('Y-m-d');
        $data['actionDate'] = date('Y-m-d', strtotime('+7 days'));
        $data['description'] = self::NR_DEFAULT_DESCRIPTION;

        return $data;
    }
}
