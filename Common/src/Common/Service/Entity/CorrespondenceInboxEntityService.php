<?php

/**
 * Correspondence Inbox (email) Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Correspondence Inbox (email) Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class CorrespondenceInboxEntityService extends AbstractLvaEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'CorrespondenceInbox';

    /**
     * Bundle for displaying the correspondence in a table.
     *
     * @var array
     */
    protected $completeBundle = [
        'children' => [
            'document',
            'licence'
        ]
    ];

    protected $reminderBundle = [
        'children' => [
            'licence' => [
                'criteria' => [
                    'translateToWelsh' => false
                ],
                'children' => [
                    'organisation'
                ]
            ],
            'document' => [
                'children' => [
                    'continuationDetails'
                ]
            ]
        ]
    ];

    /**
     * Get the full correspondence record by its primary identifier.
     *
     * @param $id The correspondence id
     *
     * @return array The correspondence record.
     */
    public function getById($id)
    {
        return parent::get($id, $this->completeBundle);
    }

    /**
     * Get all correspondence using an array of licence identifiers
     * where the licence organisation matches to passed organisation
     * identifier.
     *
     * @param int $organisationId
     *
     * @return array
     */
    public function getCorrespondenceByOrganisation($organisationId = null)
    {
        $licences = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getAll(
                array(
                    'organisation' => $organisationId
                )
            );

        $ids = array_map(
            function ($licence) {
                return $licence['id'];
            },
            $licences['Results']
        );

        return $this->getAll(array('licence' => $ids), $this->completeBundle);
    }

    public function getAllRequiringReminder($minDate, $maxDate)
    {
        return $this->filterEmptyLicences(
            $this->getAll(
                [
                    'accessed' => 'N',
                    ['createdOn' => '>= ' . $minDate],
                    ['createdOn' => '<= ' . $maxDate],
                    // don't fetch ones we've already sent...
                    'emailReminderSent' => 'NULL',
                    // ... but also ignore ones we may have printed but
                    // *not* sent reminders for - e.g. if org has no email
                    // addresses (somehow) - without this check we'd continually
                    // try and email the reminder long after the print threshold
                    // had been reached
                    'printed' => 'NULL'
                ],
                $this->reminderBundle
            )
        );
    }

    public function getAllRequiringPrint($minDate, $maxDate)
    {
        return $this->filterEmptyLicences(
            $this->getAll(
                [
                    'accessed' => 'N',
                    ['createdOn' => '>= ' . $minDate],
                    ['createdOn' => '<= ' . $maxDate],
                    // unlike the previous method, print queries don't
                    // care about the emailReminderSent flag; whether a reminder
                    // has or hasn't been sent doesn't affect whether it needs
                    // printing or not
                    'printed' => 'NULL'
                ],
                $this->reminderBundle
            )
        );
    }

    private function filterEmptyLicences($data)
    {
        return array_filter(
            $data['Results'],
            function ($v) {
                return isset($v['licence']);
            }
        );
    }
}
