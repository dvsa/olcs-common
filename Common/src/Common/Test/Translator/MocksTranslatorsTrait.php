<?php

namespace Common\Test\Translator;

use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceManager;
use Mockery\MockInterface;

trait MocksTranslatorsTrait
{
    abstract protected function serviceManager(): ServiceManager;

    /**
     * @return MockInterface|TranslatorInterface
     */
    protected function translator(): MockInterface
    {
        return $this->serviceManager()->get(TranslatorInterface::class);
    }

    /**
     * @return MockInterface|Translator
     */
    protected function setUpDefaultTranslator(): MockInterface
    {
        $instance = $this->setUpMockService(Translator::class);
        $instance->shouldReceive('translate')->andReturnUsing(static fn($key) => $key)->byDefault();
        return $instance;
    }
}
