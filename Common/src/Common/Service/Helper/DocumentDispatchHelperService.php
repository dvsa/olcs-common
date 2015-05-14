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
    public function process($file, $params = [], $isContinuation = false)
    {
        // @TODO: adhere to continuation flag

        if (!isset($params['licence'])) {
            throw new \RuntimeException('Please provide a licence parameter');
        }

        if (!isset($params['description'])) {
            throw new \RuntimeException('Please provide a document description parameter');
        }

        $licenceId = $params['licence'];

        $description = $params['description'];

        $licence = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getWithOrganisation($licenceId);

        $organisation = $licence['organisation'];

        // we have to create the document early doors because we need its ID
        // if we're going to go on to email it
        $document = $this->getServiceLocator()->get('Entity\Document')->createFromFile($file, $params);
        $documentId = $document['id'];

        if ($organisation['allowEmail'] === 'N') {
            $this->attemptPrint($licence, $file, $description);
            return $documentId;
        }

        // all good; but we need to check we have >= 1 admin
        // user to send the email to
        $users = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getAdminEmailAddresses($organisation['id']);

        if (empty($users)) {
            // oh well, fallback to a printout
            $this->attemptPrint($licence, $file, $description);
            return $documentId;
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
                $licence['translateToWelsh'],
                null,
                null,
                $users,
                'email.licensing-information.subject',
                'markup-email-dispatch-document',
                $params
            );

        // even if we've successfully emailed we always create a translation task for Welsh licences
        if ($licence['translateToWelsh'] === 'Y') {
            $this->generateTranslationTask($licence, $description);
        }

        return $documentId;
    }

    private function attemptPrint($licence, $file, $description)
    {
        if ($licence['translateToWelsh'] === 'Y') {
            return $this->generateTranslationTask($licence, $description);
        }

        return $this->getServiceLocator()
            ->get('PrintScheduler')
            ->enqueueFile($file, $description);
    }

    private function generateTranslationTask($licence, $description)
    {
        $this->getServiceLocator()
            ->get('Entity\Task')
            ->save(
                [
                    'category' => CategoryDataService::CATEGORY_LICENSING,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_LICENSING_GENERAL_TASK,
                    'description' => 'Welsh translation required: ' . $description,
                    'actionDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(),
                    'urgent' => 'Y',
                    'licence' => $licence['id'],
                    // @TODO: need proper auth solution here to assign this task to current user
                    // and current team. Steve Liversedge is going to create a followup story to
                    // address this (and other tasks needing to follow the same rules) later.
                    // No point creating a stubbed service / method since this is probably going
                    // to be handled at quite a low level (e.g. rest client itself)
                    'assignedToUser' => null,
                    'assignedToTeam' => 2
                ]
            );
    }
}
