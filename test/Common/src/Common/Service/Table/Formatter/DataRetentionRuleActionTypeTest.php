<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DataRetentionRuleActionType;
use Mockery as m;

/**
 * DataRetentionRule Link test
 */
class DataRetentionRuleActionTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $data = [
            "actionType" => [
                "description"=> "DR action type",
                "displayOrder"=> null,
                "id"=> "Automate",
                "olbsKey"=> null,
                "parent"=> null,
                "refDataCategoryId"=> "action_type",
                "version"=> 1
            ],
            "createdBy"=> null,
            "createdOn"=> "2017-10-12T16:54:14+0000",
            "customProcedure"=> null,
            "deletedDate"=> null,
            "description"=> "IRFO Operator expired",
            "id"=> 1,
            "isCustomRule"=> false,
            "isEnabled"=> false,
            "lastModifiedBy"=> null,
            "lastModifiedOn"=> "2017-10-12T16:54:14+0000",
            "maxDataSet"=> 1000,
            "populateProcedure"=> "sp_populate_irfo_operator_expired",
            "retentionPeriod"=> "60"
        ];

        $this->assertEquals($data['actionType']['id'], DataRetentionRuleActionType::format($data));
    }
}
