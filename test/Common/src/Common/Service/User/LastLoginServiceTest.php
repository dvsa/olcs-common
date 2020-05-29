<?php

/**
 * People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
use Common\Service\Helper\FormHelperService;
use Common\Service\Lva\PeopleLvaService;
use Common\Service\User\LastLoginService;
use Dvsa\Olcs\Transfer\Command\User\UpdateUserLastLoginAt;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\Form\Element;
use Zend\Form\FieldsetInterface;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Text\Table\Table;


/**
 * Class LastLoginServiceTest
 * @package CommonTest\Service\User
 */
class LastLoginServiceTest extends MockeryTestCase
{
    const TOKEN = "exampleToken";

    /** @var LastLoginService */
    private $sut;

    /**
     * @var CommandSender|m\LegacyMockInterface|m\MockInterface
     */
    private $commandSender;

    public function setup()
    {
        $this->commandSender = m::mock(CommandSender::class);
        $this->sut = new LastLoginService($this->commandSender);
    }

    public function testCommandIsInstantiatedWithToken()
    {
        $this->commandSender
            ->shouldReceive('send')
            ->with(
                m::on(function($command) {
                    $this->assertInstanceOf(UpdateUserLastLoginAt::class, $command);
                    $this->assertEquals($command->getSecureToken(), self::TOKEN);
                    return true;
                })
            );

        $this->sut->updateLastLogin(self::TOKEN);
    }
}
