<?php

/**
 * Document Dispatch Helper Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Helper;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Data\CategoryDataService;

/**
 * Document Dispatch Helper Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentDispatchHelperService extends AbstractHelperService
{
    public function process($file, $params = [])
    {
        if (!isset($params['licence'])) {
            throw new \RuntimeException('Please provide a licence parameter');
        }

        $licenceId = $params['licence'];

        $licence = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getWithOrganisation($licenceId);

        $organisation = $licence['organisation'];

        // we have to create the document early doors because we need its ID
        // if we're going to go on to email it
        $documentId = $this->getServiceLocator()->get('Entity\Document')->createFromFile($file, $params);

        if (!$organisation['allowEmail']) {
            return $this->attemptPrint($licence, $file, $params);
        }

        // all good; but we need to check we have >= 1 admin
        // user to send the email to
        $orgUsers = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getAdminUsers($organisation['id']);

        $users = [];
        foreach ($orgUsers as $user) {
            if (isset($user['user']['emailAddress'])) {
                $details = $user['user']['contactDetails']['person'];
                $users[] = sprintf(
                    '%s %s <%s>',
                    $details['forename'],
                    $details['familyName'],
                    $user['user']['emailAddress']
                );
            }
        }

        if (empty($users)) {
            // oh well, fallback to a printout
            return $this->attemptPrint($licence, $file, $params);
        }

        $this->getServiceLocator()
            ->get('Entity\CorrespondenceInbox')
            ->save(
                [
                    'document' => $documentId,
                    'licence'  => $licenceId
                ]
            );

        $url = $this->getServiceLocator()->get('Helper\Url')->fromRouteWithHost(
            UrlHelperService::EXTERNAL_HOST,
            'correspondence_inbox'
        );

        $params = [
            $licence['licNo'],
            $url
        ];

        $this->getServiceLocator()
            ->get('Email')
            ->sendTemplate(
                null,
                null,
                $users,
                // @TODO: get from Steve
                'NEED TO ADD',
                'markup-email-dispatch-document',
                $params
            );

        if ($licence['translateToWelsh']) {
            return $this->generateTranslationTask();
        }
    }

    private function attemptPrint($licence, $file, $description)
    {
        if ($licence['translateToWelsh']) {
            return $this->generateTranslationTask();
        }

        // okay; go ahead and print

        return $this->getServiceLocator()
            ->get('PrintScheduler')
            ->enqueueFile($file, $description);
    }

    private function generateTranslationTask()
    {
        $this->getServiceLocator()
            ->get('Entity\Task')
            ->save(
                [
                    'category' => CategoryDataService::CATEGORY_LICENSING,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_LICENSING_GENERAL_TASK,
                    'description' => 'Welsh translation required: <x>',
                    'actionDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(),
                    'urgent' => true,
                    // @TODO
                    'assignedToUser' => null,
                    'assignedToTeam' => null
                ]
            );
    }
}
