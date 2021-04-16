<?php

declare(strict_types=1);

namespace Common\Form\Elements\Validators\Messages;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorPluginManager;

/**
 * @see \CommonTest\Form\Elements\Validators\Messages\ValidatorDefaultMessageProviderTest
 */
class ValidatorDefaultMessageProvider
{
    /**
     * @var ValidatorPluginManager
     */
    protected $pluginManager;

    /**
     * @var string
     */
    private $validatorName;

    /**
     * @param ValidatorPluginManager $pluginManager
     * @param string $validatorName
     */
    public function __construct(ValidatorPluginManager $pluginManager, string $validatorName)
    {
        $this->pluginManager = $pluginManager;
        $this->validatorName = $validatorName;
    }

    /**
     * @return string
     */
    public function getValidatorName(): string
    {
        return $this->validatorName;
    }

    /**
     * @param string $messageKey
     * @return string|null
     */
    public function __invoke(string $messageKey): ?string
    {
        $validator = $this->pluginManager->get($this->getValidatorName());
        assert($validator instanceof AbstractValidator, 'Expected validator to be an instance of AbstractValidator');
        return $validator->getMessageTemplates()[$messageKey] ?? null;
    }
}
