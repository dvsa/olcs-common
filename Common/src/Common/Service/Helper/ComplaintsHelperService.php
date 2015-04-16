<?php

/**
 * Complaints helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Complaints helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ComplaintsHelperService extends AbstractHelperService
{
    /**
     * From cases, create a sorted array of complaints
     *
     * @param array $cases
     *
     * @return array
     */
    public function sortCasesOpenClosed($cases)
    {
        // sort out the data from the service from cases into complaints
        $rows = [];
        foreach ($cases as $case) {
            foreach ($case['complaints'] as $complaint) {
                $row = [];
                $row['caseId'] = $case['id'];
                $row['complaintDate'] = $complaint['complaintDate'];
                $row['complainantContactDetails'] = $complaint['complainantContactDetails'];
                $row['ocComplaints'] = $complaint['ocComplaints'];
                $row['description'] = $complaint['description'];
                $row['status'] = $complaint['status'];
                $rows[] = $row;
            }
        }

        // sort by complaintDate
        usort(
            $rows,
            function ($a, $b) {
                return $a['complaintDate'] < $b['complaintDate'];
            }
        );

        // sort the results so that open cases are first but still in date order
        $openComplaints = array();
        $closedComplaints = array();
        foreach ($rows as $row) {
            if ($row['status']['id'] === \Common\Service\Entity\ComplaintEntityService::COMPLAIN_STATUS_CLOSED) {
                $closedComplaints[] = $row;
            } else {
                $openComplaints[] = $row;
            }
        }

        return array_merge($openComplaints, $closedComplaints);
    }
}
