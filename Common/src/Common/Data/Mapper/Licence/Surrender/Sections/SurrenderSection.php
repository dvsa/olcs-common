<?php


namespace Common\Data\Mapper\Licence\Surrender\Sections;


use Common\Data\Mapper\Licence\Surrender\CommunityLicence;
use Common\Data\Mapper\Licence\Surrender\CurrentDiscs;
use Common\Data\Mapper\licence\Surrender\OperatorLicence;
use Common\Service\Helper\TranslationHelperService;
use Zend\Mvc\Controller\Plugin\Url;

class SurrenderSection
{
    use MakeSectionTrait;

    const DISC_SECTION = 'CurrentDiscs';
    const DOCUMENTS_SECTION = ['OperatorLicence', 'CommunityLicence'];

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

    protected function makeQuestions(): array
    {
        return $this->getDataForSection($this->section);
    }

    private function getDataForSection($section): array
    {
        switch ($section) {
            case self::DISC_SECTION:
                $data = CurrentDiscs::mapFromResult($this->data);

                
                    $questions [] = [
                        'label' => $this->translator->translate(''),
                        'answer' => $this->data[''],
                        'changeLinkInHeading' => $this->displayChangeLinkInHeading
                    ];
                }


                break;
            case self::DOCUMENTS_SECTION:
                $data = [
                    'operatorLicence' => OperatorLicence::mapFromResult($this->data),
                    'communityLicence' => CommunityLicence::mapFromResult($this->data)
                ];
                break;

        }
        return [];
    }


    protected function makeChangeLink()
    {
        if ($this->section === self::DISC_SECTION) {
            return false;
        }
    }
}