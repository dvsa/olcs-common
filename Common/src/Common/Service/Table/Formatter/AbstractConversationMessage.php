<?php

declare(strict_types=1);

namespace Common\Service\Table\Formatter;

use DateTimeImmutable;
use DateTimeInterface;

abstract class AbstractConversationMessage implements FormatterPluginManagerInterface
{
    /**
     * status
     *
     * @param array $row Row data
     * @param array $column Column data
     *
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = null): string
    {
        if (!empty($row['createdBy']['team'])) {
            $senderName = 'Case Worker';
        } elseif (!empty($row['createdBy']['contactDetails']['person'])) {
            $person = $row['createdBy']['contactDetails']['person'];
            $senderName = $person['forename'] . " " . $person['familyName'];
        } else {
            $senderName = $row['createdBy']['loginId'];
        }

        $latestMessageCreatedAt = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $row["createdOn"]);
        $date = $latestMessageCreatedAt->format('l j F Y \a\t H:ia');

        $fileList = array_map(
            fn($doc) => sprintf(
                '<li class="file"><a href="/file/%s" class="govuk-link">%s</a> <span>%s</span></li>',
                $doc['id'],
                $doc['description'],
                $this->readableBytes($doc['size']),
            ),
            $row['documents'],
        );
        if (count($fileList) > 0) {
            $fileList = '
                <h3 class="file__heading">Attachments</h3>
                <div class="file-uploader">
                    <ul>' . implode('', $fileList) . '</ul>
                </div>
            ';
        }

        $rowTemplate = '
            <div class="govuk-!-margin-bottom-6">
                <div class="govuk-summary-card">
                    <div class="govuk-summary-card__title-wrapper">
                        <h2 class="govuk-summary-card__title">%s</h2>
                        <h2 class="govuk-summary-card__title govuk-summary-card__date">%s</h2>
                    </div>
                    <div class="govuk-summary-card__content">
                        <p class="govuk-body">%s</p>
                        %s
                        %s
                    </div>
                </div>
            </div>
        ';

        $firstReadyBy = $this->getFirstReadBy($row);

        return vsprintf(
            $rowTemplate,
            [
                $senderName,
                $date,
                nl2br($row['messagingContent']['text']),
                $fileList ?: '',
                $firstReadyBy,
            ],
        );
    }

    /**
     * From https://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
     * originally from Chris Jester-Young.
     */
    public function readableBytes(int $bytes): string
    {
        $base = log($bytes) / log(1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(1024 ** ($base - floor($base)), 2) . $suffixes[floor($base)];
    }

    public function getFirstReadBy(array $row): string
    {
        if (count($row['userMessageReads']) === 0) {
            return '';
        }

        $firstRead = array_pop($row['userMessageReads']);
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
}
