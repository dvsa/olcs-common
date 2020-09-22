<?php

namespace Common\Service\Qa\DataTransformer;

class ApplicationStepsPostDataTransformer
{
    /** @var DataTransformerProvider */
    private $dataTransformerProvider;

    /**
     * Create service instance
     *
     * @param DataTransformerProvider $dataTransformerProvider
     *
     * @return ApplicationStepsPostDataTransformer
     */
    public function __construct(DataTransformerProvider $dataTransformerProvider)
    {
        $this->dataTransformerProvider = $dataTransformerProvider;
    }

    /**
     * Convert fieldset post data from the frontend to a format suitable for backend submission
     *
     * @param array $applicationSteps
     * @param array $applicationStepsPostData
     *
     * @return array
     */
    public function getTransformed(array $applicationSteps, array $applicationStepsPostData)
    {
        foreach ($applicationSteps as $applicationStep) {
            $fieldsetName = $applicationStep['fieldsetName'];

            $dataTransformer = $this->dataTransformerProvider->getTransformer($applicationStep['slug']);
            if ($dataTransformer) {
                $applicationStepsPostData[$fieldsetName] = $dataTransformer->getTransformed(
                    $applicationStepsPostData[$fieldsetName]
                );
            }
        }

        return $applicationStepsPostData;
    }
}
