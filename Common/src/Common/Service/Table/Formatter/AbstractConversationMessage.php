<?php

declare(strict_types=1);

namespace Common\Service\Table\Formatter;

use DateTimeImmutable;
use DateTimeInterface;

abstract class AbstractConversationMessage implements FormatterPluginManagerInterface
{
    protected string $rowTemplate;

    /**
     * status
     *
     * @param array $row Row data
     * @param array $column Column data
     *
     * @inheritdoc
     */
    public function format($row, $column = null): string
    {
        $senderName = $this->getSenderName($row);

        $latestMessageCreatedAt = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $row["createdOn"]);
        $date = $latestMessageCreatedAt->format('l j F Y \a\t H:ia');

        $fileList = $this->getFileList($row);

        $firstReadBy = $this->getFirstReadBy($row);

        // If createdBy (User) has a Team, they are an internal user.
        $internalCaseworkerTeam = (empty($row['createdBy']['team'])) ? '' : '<p class="govuk-caption-m">' . $senderName . '<br/>Caseworker Team</p>';

        // If running on 'selfserve' remove caseworker's family name.
        if (str_contains(__FILE__, 'selfserve') && $internalCaseworkerTeam) {
            $caseworkerFamilyName = explode(' ', $senderName)[1];
            $senderName = str_replace($caseworkerFamilyName, '', $senderName);
            $internalCaseworkerTeam = str_replace($caseworkerFamilyName, '', $internalCaseworkerTeam);
        }

        return strtr($this->rowTemplate, [
            '{senderName}' => $senderName,
            '{messageDate}' => $date,
            '{messageBody}' => nl2br($row['messagingContent']['text']),
            '{caseworkerFooter}' => $internalCaseworkerTeam,
            '{fileList}' => $fileList,
            '{firstReadBy}' => $firstReadBy,
        ]);
    }

    /**
     * From https://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
     * originally from Chris Jester-Young.
     */
    protected function readableBytes(int $bytes): string
    {
        $base = log($bytes) / log(1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(1024 ** ($base - floor($base)), 2) . $suffixes[floor($base)];
    }

    /**
     * Returns HTML - The first user to read the given message
     */
    protected function getFirstReadBy(array $row): string
    {
        if (
            !isset($row['userMessageReads'])
            || !is_array($row['userMessageReads'])
            || count($row['userMessageReads']) === 0
        ) {
            return '';
        }

        $firstRead = null;
        while ($firstRead = array_pop($row['userMessageReads'])) {
            if ($firstRead === null || $row['createdBy']['id'] !== $firstRead['user']['id']) {
                break;
            }
        }

        if ($firstRead === null) {
            return '';
        }

        $firstReadOn = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $firstRead["createdOn"]);

        if (isset($firstRead['user']['contactDetails']['person'])) {
            $firstReadBy = $firstRead['user']['contactDetails']['person']['forename'] . ' ' .
                           $firstRead['user']['contactDetails']['person']['familyName'];
        } elseif (isset($firstRead['user']['contactDetails']['emailAddress'])) {
            $firstReadBy = $firstRead['user']['contactDetails']['emailAddress'];
        } else {
            $firstReadBy = $firstRead['user']['loginId'];
        }

        return sprintf(
            '<hr/><p><em>First read by %s on %s</em></p>',
            $firstReadBy,
            $firstReadOn->format('l j F Y \a\t H:ia'),
        );
    }

    /**
     * Returns HTML - File/Attachments list for given message
     */
    protected function getFileList(array $row): string
    {
        $fileList = array_map(
            fn($doc) => sprintf(
                '<li class="file"><a href="/file/%s" class="govuk-link">%s</a> <span>%s</span></li>',
                $doc['id'],
                $doc['description'],
                $this->readableBytes($doc['size']),
            ),
            $row['documents'],
        );
        if ($fileList !== []) {
            return '
                <h3 class="file__heading">Attachments</h3>
                <div class="file-uploader">
                    <ul>' . implode('', $fileList) . '</ul>
                </div>
            ';
        }

        return '';
    }

    protected function getSenderName(array $row): string
    {
        if (!empty($row['createdBy']['contactDetails']['person'])) {
            $person = $row['createdBy']['contactDetails']['person'];
            $senderName = $person['forename'] . " " . $person['familyName'];
        } else {
            $senderName = $row['createdBy']['loginId'];
        }

        return $senderName;
    }
}
