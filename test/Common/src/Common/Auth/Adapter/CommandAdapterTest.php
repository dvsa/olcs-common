<?php
declare(strict_types=1);

namespace CommonTest\Auth\Adapter;

use Common\Auth\Adapter\CommandAdapter;
use Common\Service\Cqrs\Command\CommandSender;
use Common\Service\Cqrs\Response;
use Dvsa\Olcs\Transfer\Command\Auth\Login;
use Laminas\Authentication\Result;
use Laminas\ServiceManager\ServiceManager;
use Mockery\MockInterface;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Mockery as m;

/**
 * Class CommandAdapterTest
 * @see CommandAdapter
 */
class CommandAdapterTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var CommandAdapter
     */
    protected $sut;

    /**
     * @test
     */
    public function authenticate_ReturnsFailureWhen_CommandReturnsNotOk()
    {
        // Setup
        $cmdResult = [
            'messages' => [
                'failed'
            ]
        ];

        $response = $this->response(false, $cmdResult);
        $commandSender = $this->commandSender($response);

        $sut = $this->setupSut($commandSender);
        $sut->setIdentity('username');
        $sut->setCredential('password');

        // Execute
        $result = $sut->authenticate();

        //Assert
        static::assertEquals(Result::FAILURE, $result->getCode());
        static::assertEquals($cmdResult['messages'], $result->getMessages());
    }

    /**
     * @test
     * @dataProvider commandResultDataProvider
     */
    public function authenticate_ReturnsResultObject_FromCommandResult(int $code, ?array $identity, ?array $messages)
    {
        // Setup
        $cmdResult = [
            'flags' => [
                'code' => $code,
                'identity' => $identity,
                'messages' => $messages
            ]
        ];


        $response = $this->response(true, $cmdResult);
        $commandSender = $this->commandSender($response);

        $sut = $this->setupSut($commandSender);
        $sut->setIdentity('username');
        $sut->setCredential('password');

        // Execute
        $result = $sut->authenticate();

        //Assert
        static::assertEquals($code, $result->getCode());
        static::assertEquals($identity, $result->getIdentity());
        static::assertEquals($messages, $result->getMessages());
    }

    public function commandResultDataProvider()
    {
        return [
            'with id and messages' => [
                'code' => 1,
                'identity' => [
                    'id' => 1
                ],
                'messages' => [
                    'message'
                ]
            ],
            'with id and mo messages' => [
                'code' => 1,
                'identity' => [
                    'id' => 1
                ],
                'messages' => []
            ],
            'with messages and no id' => [
                'code' => 1,
                'identity' => [],
                'messages' => [
                    'message'
                ]
            ],
        ];
    }

    /**
     * @param bool $isOk
     * @param array $result
     * @return Response|MockInterface
     */
    protected function response(bool $isOk, array $result)
    {
        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('isOk')
            ->andReturn($isOk);
        $mockResponse->shouldReceive('getResult')
            ->andReturn($result);

        return $mockResponse;
    }

    /**
     * @param $response
     * @return CommandSender|MockInterface
     */
    protected function commandSender(Response $response)
    {
        $mockSender = m::mock(CommandSender::class);
        $mockSender->shouldReceive('send')
            ->andReturn($response);

        return $mockSender;
    }

    /**
     * @param $commandSender
     * @return CommandAdapter
     */
    protected function setupSut(CommandSender $commandSender): CommandAdapter
    {
        return new CommandAdapter($commandSender);
    }
}
