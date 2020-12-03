<?php

namespace Common\Service\Qa\Custom\Common;

use Common\Form\Annotation\CustomAnnotationBuilder;
use Common\Form\Model\Fieldset\MultipleFileUpload;
use Zend\Form\Factory as FormFactory;
use Zend\Form\InputFilterProviderFieldset;

class FileUploadFieldsetGenerator
{
    /** @var FormFactory */
    private $formFactory;

    /** @var CustomAnnotationBuilder */
    private $customAnnotationBuilder;

    /**
     * Create service instance
     *
     * @param FormFactory $formFactory
     * @param CustomAnnotationBuilder $customAnnotationBuilder
     *
     * @return FileUploadFieldsetGenerator
     */
    public function __construct(FormFactory $formFactory, CustomAnnotationBuilder $customAnnotationBuilder)
    {
        $this->formFactory = $formFactory;
        $this->customAnnotationBuilder = $customAnnotationBuilder;
    }

    /**
     * Generate a fieldset for the purpose of uploading files
     *
     * @return InputFilterProviderFieldset
     */
    public function generate()
    {
        $multipleFileUploadSpec = $this->customAnnotationBuilder->getFormSpecification(
            MultipleFileUpload::class
        );

        $multipleFileUploadSpec['type'] = InputFilterProviderFieldset::class;
        $multipleFileUploadSpec['input_filter']['fileCount']['continue_if_empty'] = true;

        $fieldset = $this->formFactory->create($multipleFileUploadSpec);
        $fieldset->setInputFilterSpecification($multipleFileUploadSpec['input_filter']);

        return $fieldset;
    }
}
