<?php

namespace Common\Service\Qa\Custom\Common;

use Common\Form\Model\Fieldset\MultipleFileUpload;
use Laminas\Form\Annotation\AnnotationBuilder;
use Laminas\Form\Factory as FormFactory;
use Laminas\Form\InputFilterProviderFieldset;

class FileUploadFieldsetGenerator
{
    private FormFactory $formFactory;

    private $customAnnotationBuilder;

    /**
     * Create service instance
     *
     * @param AnnotationBuilder $customAnnotationBuilder
     * @return FileUploadFieldsetGenerator
     */
    public function __construct(FormFactory $formFactory, $customAnnotationBuilder)
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
