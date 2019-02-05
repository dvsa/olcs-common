<?php


namespace Common\Data\Mapper\Licence\Surrender\Sections;

use Common\Data\Mapper\Licence\Surrender\CommunityLicence;
use Common\Data\Mapper\Licence\Surrender\CurrentDiscs;
use Common\Data\Mapper\Licence\Surrender\OperatorLicence;
use Common\Service\Helper\TranslationHelperService;
use Zend\Mvc\Controller\Plugin\Url;

/**
 * Class SurrenderSection
 *
 * @package Common\Data\Mapper\Licence\Surrender\Sections
 */
class SurrenderSection
{
    use MakeSectionTrait;

    const DISC_SECTION = 'current-discs';
    const OPERATORLICENCE_SECTION = 'operator-licence';
    const COMMUNITYLICENCE_SECTION = 'community-licence';

    private $heading;

    /**
     * @var array
     */
    private $data;
    /**
     * @var Url
     */
    private $urlHelper;
    /**
     * @var TranslationHelperService
     */
    private $translator;
    private $section;


    public function __construct(
        array $data,
        Url $urlHelper,
        TranslationHelperService $translator,
        $section
    ) {

        $this->data = $data;
        $this->urlHelper = $urlHelper;
        $this->translator = $translator;
        $this->section = $section;
    }

    /**
     * @param mixed $heading
     */
    public function setHeading($heading): void
    {
        $this->heading = $heading;
    }

    /**
     * @return array
     */
    protected function makeQuestions(): array
    {
        return $this->getDataForSection($this->section);
    }

    /**
     * @param $section
     *
     * @return array
     */
    private function getDataForSection($section): array
    {
        $questions = [];
        switch ($section) {
            case self::DISC_SECTION:
                $data = CurrentDiscs::mapFromResult($this->data['surrender']);
                $discInformation = array_column($data, 'info');
                $questions = $this->createDiscSection($discInformation);
                break;
            case self::OPERATORLICENCE_SECTION:
                $operatorLicence = OperatorLicence::mapFromResult($this->data['surrender']);
                $data['operatorLicenceDocument'] = $operatorLicence;
                $questions = $this->createDocumentQuestions($data);
                break;
            case self::COMMUNITYLICENCE_SECTION:
                $data['communityLicenceDocument'] = CommunityLicence::mapFromResult($this->data['surrender']);
                $questions = $this->createDocumentQuestions($data);
                break;
        }
        return $questions;
    }


    protected function makeChangeLink($label = null)
    {
        $returnRoutes = [
            'operatorLicenceDocument' => 'operator-licence',
            'communityLicenceDocument' => 'community-licence'
        ];
        $changeLink = 'licence/surrender/current-discs/review/GET';

        if ($this->section !== self::DISC_SECTION && !is_null($label)) {
            $changeLink = 'licence/surrender/' . $returnRoutes[$label] . '/review/GET';
        } elseif (is_null($label)) {
            return $this->makeChangeLink(array_search($this->section, $returnRoutes));
        }
        return [
            'sectionLink' => $this->urlHelper->fromRoute($changeLink, [], [], true)
        ];
    }

    /**
     * @param       $data
     *
     * @return array
     */
    private function createDocumentQuestions(array $data): array
    {
        $questions = [];
        foreach ($data as $k => $document) {
            $questions [] = [
                'label' => $this->translator->translate('surrender.review.label.documents.' . $k),
                'answer' => $this->translator->translate('licence.surrender.review.label.documents.answer' . $document[$k][$k] ?? ''),
                'changeLinkInHeading' => $this->displayChangeLinkInHeading,
                'change' => $this->makeChangeLink($k)
            ];
        }
        return $questions;
    }

    /**
     * @param array $discInformation
     * @param array $questions
     *
     * @return array
     */
    private function createDiscSection(array $discInformation): array
    {
        $questions = [];
        $sections = ['possession', 'lost', 'stolen'];
        foreach ($discInformation as $k => $currentDiscs) {
            $labelSuffix = $sections[$k];
            $questions [] = [
                'label' => $this->translator->translate('licence.surrender.review.label.discs.' . $labelSuffix),
                'answer' => $currentDiscs['number'] ?? '0',
                'changeLinkInHeading' => $this->displayChangeLinkInHeading,
                'change' => $this->makeChangeLink()
            ];
        }
        return $questions;
    }
}
