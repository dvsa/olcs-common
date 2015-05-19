<?php

/**
 * Document Dispatch Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\DocumentDispatchHelperService;
use Common\Service\Data\CategoryDataService;
use Mockery as m;

/**
 * Document Dispatch Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentDispatchHelperServiceTest extends MockeryTestCase
{
    public function setUp()
    {
        $this->sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->sut = new DocumentDispatchHelperService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessWithNoLicenceThrowsException()
    {
        try {
            $this->sut->process(null, []);
        } catch (\RuntimeException $e) {
            $this->assertEquals('Please provide a licence parameter', $e->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testProcessWithNoDescriptionThrowsException()
    {
        try {
            $this->sut->process(null, ['licence' => 1]);
        } catch (\RuntimeException $e) {
            $this->assertEquals('Please provide a document description parameter', $e->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testWithEnglishOrgNoEmail()
    {
        $file = m::mock();
        $params = [
            'licence' => 123,
            'description' => 'foo'
        ];

        $licence = [
            'organisation' => [
                'allowEmail' => 'N'
            ],
            'translateToWelsh' => 'N'
        ];

        $this->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getWithOrganisation')
            ->with(123)
            ->andReturn($licence)
            ->getMock()
        );

        $this->setService(
            'Entity\Document',
            m::mock()
            ->shouldReceive('createFromFile')
            ->with($file, $params)
            ->getMock()
        );

        $this->setService(
            'PrintScheduler',
            m::mock()
            ->shouldReceive('enqueueFile')
            ->with($file, 'foo')
            ->getMock()
        );

        $this->sut->process($file, $params);
    }

    public function testWithWelshOrgNoEmail()
    {
        $file = m::mock();
        $params = [
            'licence' => 123,
            'description' => 'foo'
        ];

        $licence = [
            'organisation' => [
                'allowEmail' => 'N'
            ],
            'translateToWelsh' => 'Y',
            'id' => 123
        ];

        $this->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getWithOrganisation')
            ->with(123)
            ->andReturn($licence)
            ->getMock()
        );

        $this->setService(
            'Entity\Document',
            m::mock()
            ->shouldReceive('createFromFile')
            ->with($file, $params)
            ->getMock()
        );

        $this->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock()
        );

        $this->setService(
            'Entity\Task',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'category' => CategoryDataService::CATEGORY_LICENSING,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_LICENSING_GENERAL_TASK,
                    'description' => 'Welsh translation required: foo',
                    'actionDate' => '2015-01-01',
                    'urgent' => 'Y',
                    'licence' => 123,
                    'assignedToUser' => null,
                    'assignedToTeam' => 2
                ]
            )
            ->getMock()
        );

        $this->sut->process($file, $params);
    }

    public function testWithEnglishOrgEmailButNoAdminUsers()
    {
        $file = m::mock();
        $params = [
            'licence' => 123,
            'description' => 'foo'
        ];

        $licence = [
            'organisation' => [
                'allowEmail' => 'Y',
                'id' => 100
            ],
            'translateToWelsh' => 'N'
        ];

        $orgUsers = [];

        $this->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getWithOrganisation')
            ->with(123)
            ->andReturn($licence)
            ->getMock()
        );

        $this->setService(
            'Entity\Document',
            m::mock()
            ->shouldReceive('createFromFile')
            ->with($file, $params)
            ->getMock()
        );

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getAdminEmailAddresses')
            ->with(100)
            ->andReturn($orgUsers)
            ->getMock()
        );

        $this->setService(
            'PrintScheduler',
            m::mock()
            ->shouldReceive('enqueueFile')
            ->with($file, 'foo')
            ->getMock()
        );

        $this->sut->process($file, $params);
    }

    public function testWithEnglishOrgEmailWithUsers()
    {
        $file = m::mock();
        $params = [
            'licence' => 123,
            'description' => 'foo'
        ];

        $licence = [
            'organisation' => [
                'allowEmail' => 'Y',
                'id' => 100
            ],
            'translateToWelsh' => 'N',
            'licNo' => 'L12345'
        ];

        $orgUsers = ['Test User <test@user.com>'];

        $this->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getWithOrganisation')
            ->with(123)
            ->andReturn($licence)
            ->getMock()
        );

        $this->setService(
            'Entity\Document',
            m::mock()
            ->shouldReceive('createFromFile')
            ->with($file, $params)
            ->andReturn(['id' => 500])
            ->getMock()
        );

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getAdminEmailAddresses')
            ->with(100)
            ->andReturn($orgUsers)
            ->getMock()
        );

        $this->setService(
            'Entity\CorrespondenceInbox',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'document' => 500,
                    'licence'  => 123
                ]
            )
            ->getMock()
        );

        $this->setService(
            'Helper\Url',
            m::mock()
            ->shouldReceive('fromRouteWithHost')
            ->with('selfserve', 'correspondence_inbox')
            ->andReturn('http://selfserve')
            ->getMock()
        );

        $this->setService(
            'Email',
            m::mock()
            ->shouldReceive('sendTemplate')
            ->with(
                'N',
                null,
                null,
                ['Test User <test@user.com>'],
                'email.licensing-information.standard.subject',
                'markup-email-licensing-information-standard',
                [
                    'L12345',
                    'http://selfserve'
                ]
            )
            ->getMock()
        );

        $this->sut->process($file, $params);
    }

    public function testWithEnglishOrgEmailWithUsersAndContinuationEmail()
    {
        $file = m::mock();
        $params = [
            'licence' => 123,
            'description' => 'foo'
        ];

        $licence = [
            'organisation' => [
                'allowEmail' => 'Y',
                'id' => 100
            ],
            'translateToWelsh' => 'N',
            'licNo' => 'L12345'
        ];

        $orgUsers = ['Test User <test@user.com>'];

        $this->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getWithOrganisation')
            ->with(123)
            ->andReturn($licence)
            ->getMock()
        );

        $this->setService(
            'Entity\Document',
            m::mock()
            ->shouldReceive('createFromFile')
            ->with($file, $params)
            ->andReturn(['id' => 500])
            ->getMock()
        );

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getAdminEmailAddresses')
            ->with(100)
            ->andReturn($orgUsers)
            ->getMock()
        );

        $this->setService(
            'Entity\CorrespondenceInbox',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'document' => 500,
                    'licence'  => 123
                ]
            )
            ->getMock()
        );

        $this->setService(
            'Helper\Url',
            m::mock()
            ->shouldReceive('fromRouteWithHost')
            ->with('selfserve', 'correspondence_inbox')
            ->andReturn('http://selfserve')
            ->getMock()
        );

        $this->setService(
            'Email',
            m::mock()
            ->shouldReceive('sendTemplate')
            ->with(
                'N',
                null,
                null,
                ['Test User <test@user.com>'],
                'email.licensing-information.continuation.subject',
                'markup-email-licensing-information-continuation',
                [
                    'L12345',
                    'http://selfserve'
                ]
            )
            ->getMock()
        );

        $this->sut->process($file, $params, 'continuation');
    }

    public function testWithWelshOrgEmailAndUsers()
    {
        $file = m::mock();
        $params = [
            'licence' => 123,
            'description' => 'foo'
        ];

        $licence = [
            'id' => 123,
            'organisation' => [
                'allowEmail' => 'Y',
                'id' => 100
            ],
            'translateToWelsh' => 'Y',
            'licNo' => 'L12345'
        ];

        $orgUsers = ['Test User <test@user.com>'];

        $this->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getWithOrganisation')
            ->with(123)
            ->andReturn($licence)
            ->getMock()
        );

        $this->setService(
            'Entity\Document',
            m::mock()
            ->shouldReceive('createFromFile')
            ->with($file, $params)
            ->andReturn(['id' => 500])
            ->getMock()
        );

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getAdminEmailAddresses')
            ->with(100)
            ->andReturn($orgUsers)
            ->getMock()
        );

        $this->setService(
            'Entity\CorrespondenceInbox',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'document' => 500,
                    'licence'  => 123
                ]
            )
            ->getMock()
        );

        $this->setService(
            'Helper\Url',
            m::mock()
            ->shouldReceive('fromRouteWithHost')
            ->with('selfserve', 'correspondence_inbox')
            ->andReturn('http://selfserve')
            ->getMock()
        );

        $this->setService(
            'Email',
            m::mock()
            ->shouldReceive('sendTemplate')
            ->with(
                'Y',
                null,
                null,
                ['Test User <test@user.com>'],
                'email.licensing-information.standard.subject',
                'markup-email-licensing-information-standard',
                [
                    'L12345',
                    'http://selfserve'
                ]
            )
            ->getMock()
        );

        $this->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock()
        );

        $this->setService(
            'Entity\Task',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'category' => CategoryDataService::CATEGORY_LICENSING,
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_LICENSING_GENERAL_TASK,
                    'description' => 'Welsh translation required: foo',
                    'actionDate' => '2015-01-01',
                    'urgent' => 'Y',
                    'licence' => 123,
                    'assignedToUser' => null,
                    'assignedToTeam' => 2
                ]
            )
            ->getMock()
        );

        $this->sut->process($file, $params);
    }

    private function setService($service, $mock)
    {
        $this->sm->shouldReceive('get')
            ->with($service)
            ->andReturn($mock);
    }
}
