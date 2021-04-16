<?php

declare(strict_types=1);

namespace CommonTest\Form\Elements\Validators\Messages;

use Common\Form\Elements\Validators\Messages\ValidatorDefaultMessageProvider;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorInterface;
use Laminas\Validator\ValidatorPluginManager;
use Mockery\MockInterface;

/**
 * @see ValidatorDefaultMessageProvider
 */
class ValidatorDefaultMessageProviderTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const MESSAGE_KEY = 'MESSAGE KEY';
    protected const DEFAULT_MESSAGE = 'DEFAULT MESSAGE';
    protected const VALIDATOR_NAME = 'VALIDATOR NAME';
    protected const VALIDATOR_MANAGER = 'ValidatorManager';

    /**
     * @var
     */
    protected $sut;

    /**
     * @test
     */
    public function getValidatorName_IsCallable()
    {
        // Setup
        $this->sut = $this->setUpSut(static::VALIDATOR_NAME);

        // Assert
        $this->assertIsCallable([$this->sut, 'getValidatorName']);
    }

    /**
     * @test
     * @depends getValidatorName_IsCallable
     */
    public function __construct_SetsValidatorClassReference()
    {
        // Setup
        $this->sut = $this->setUpSut(static::VALIDATOR_NAME);

        // Assert
        $this->assertEquals(static::VALIDATOR_NAME, $this->sut->getValidatorName());
    }

    /**
     * @test
     */
    public function __invoke_IsCallable()
    {
        // Setup
        $this->sut = $this->setUpSut(static::VALIDATOR_NAME);

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ThrowsException_IfValidastorCannotBeResolved()
    {
        // Setup
        $this->sut = $this->setUpSut(static::VALIDATOR_NAME);

        // Expect
        $this->expectException(ServiceNotFoundException::class);

        // Execute
        $this->sut->__invoke(static::MESSAGE_KEY);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsNull_WhenValidatorDoesNotHaveAMessageTemplate()
    {
        // Setup
        $this->sut = $this->setUpSut(static::VALIDATOR_NAME);
        $validator = $this->setUpMockValidator();
        $this->validators()->setService(static::VALIDATOR_NAME, $validator);

        // Execute
        $defaultMessage = $this->sut->__invoke(static::MESSAGE_KEY);

        // Assert
        $this->assertNull($defaultMessage);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsMessageTemplate()
    {
        // Setup
        $this->sut = $this->setUpSut(static::VALIDATOR_NAME);
        $validator = $this->setUpMockValidator();
        $validator->shouldReceive('getMessageTemplates')->andReturn([
            static::MESSAGE_KEY => static::DEFAULT_MESSAGE,
        ]);
        $this->validators()->setService(static::VALIDATOR_NAME, $validator);

        // Execute
        $defaultMessage = $this->sut->__invoke(static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::DEFAULT_MESSAGE, $defaultMessage);
    }

    protected function setUp(): void
    {
        $this->setUpServiceManager();
    }

    /**
     * @inheritDoc
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService('ValidatorManager', new ValidatorPluginManager());
    }

    /**
     * @return MockInterface|ValidatorInterface
     */
    protected function setUpMockValidator(): MockInterface
    {
        return $this->setUpMockService(AbstractValidator::class);
    }

    /**
     * @return ValidatorPluginManager
     */
    protected function validators(): ValidatorPluginManager
    {
        return $this->serviceManager()->get(static::VALIDATOR_MANAGER);
    }

    /**
     * @param string $validatorName
     * @return ValidatorDefaultMessageProvider
     */
    public function setUpSut(string $validatorName): ValidatorDefaultMessageProvider
    {
        return new ValidatorDefaultMessageProvider($this->validators(), $validatorName);
    }
}
