<?php

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetails implements BusinessRuleInterface
{
    public function validate($orgId, $data, $natureOfBusinesses, $contactDetailsId = null)
    {
        $validated = [
            'id' => $orgId,
            'version' => $data['version'],
            'natureOfBusinesses' => $natureOfBusinesses
        ];

        if (isset($data['data']['companyNumber']['company_number'])) {
            $validated['companyOrLlpNo'] = $data['data']['companyNumber']['company_number'];
        }

        if (isset($data['data']['name'])) {
            $validated['name'] = $data['data']['name'];
        }

        if ($contactDetailsId) {
            $validated['contactDetails'] = $contactDetailsId;
        }

        return $validated;
    }
}
