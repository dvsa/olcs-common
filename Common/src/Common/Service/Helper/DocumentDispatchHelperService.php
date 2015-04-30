<?php

/**
 * Document Dispatch Helper Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Helper;

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

        $documentId = $this->getServiceLocator()->get('Entity\Document')->createFromFile($file, $params);

        if (!$organisation['allowEmail']) {
            return $this->printDocument($file, $params);
        }

        // all good; but we need to check we have >= 1 admin
        // user to send the email to
        $orgUsers = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getAdminUsers($organisation['id']);

        $users = [];
        foreach ($orgUsers as $user) {
            if (isset($user['user']['emailAddress'])) {
                $users[] = $user['user'];
            }
        }

        if (empty($users)) {
            // oh well, fallback to a printout
            return $this->printDocument($file, $params);
        }

        foreach ($users as $user) {
            $this->emailDocument($documentId, $licenceId);
        }

        if ($licence['translateToWelsh']) {
            return $this->printDocument($file, $params);
        }
    }

    private function emailDocument($documentId, $params)
    {
        $this->getServiceLocator()
            ->get('Email')
            ->sendEmail($fromName, $fromEmail, $to, $subject, $body);

        $this->getServiceLocator()
            ->get('Entity\CorrespondenceInbox')
            ->create(
                [
                    'document' => $documentId,
                    'licence'  => $licenceId
                ]
            );
    }

    private function printDocument($file, $description)
    {
        return $this->getServiceLocator()
            ->get('PrintScheduler')
            ->enqueueFile($file, $description);
    }
}
