<?php

/**
 * Test AbstractActionController
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Controller;

/**
 * Test AbstractActionController
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class AbstractActionControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dpCheckForCrudAction
     */
    public function testCheckForCrudAction($id, $route, $params, $return)
    {
        $paramsMock = $this->getMock('stdClass', ['fromPost']);
        $paramsMock->expects($this->at(0))
                   ->method('fromPost')
                   ->with('action')
                   ->will($this->returnValue('edit'));
        $paramsMock->expects($this->at(1))
                   ->method('fromPost')
                   ->with('id')
                   ->will($this->returnValue($id));

        $redirectMock = $this->getMock('stdClass', ['toRoute']);
        $redirectMock->expects($this->any())
                     ->method('toRoute')
                     ->will($this->returnValue($return));

        $sut = $this->getNewSut(['redirect', 'params']);
        $sut->expects($this->once())
            ->method('redirect')
            ->will($this->returnValue($redirectMock));
        $sut->expects($this->atLeastOnce())
            ->method('params')
            ->will($this->returnValue($paramsMock));

        $this->assertSame($return, $sut->checkForCrudAction($route, $params));
    }

    public function dpCheckForCrudAction()
    {
        $data = [
            ['1', 'yoyo', ['foo' => 'bar'], 'redirect-return-value'],
            ['', 'yoyo', ['foo' => 'bar'], false]
        ];

        return $data;
    }

    public function testBuildTable()
    {
        $table = 'hello';
        $results = ['234'];
        $data = ['foo' => 'bar'];

        $return = 'yeah';

        $sut = $this->getNewSut(['getTable', 'getServiceLocator']);
        $sut->expects($this->once())
            ->method('getTable')
            ->with($table, $results, $data, true)
            ->will($this->returnValue($return));

        $this->assertSame($return, $sut->buildTable($table, $results, $data));
    }

    public function testGetTable()
    {
        $table = 'testing-table';
        $results = ['1'];
        $data = array(['sd']);
        $render = true;

        $url = 'yo';

        $return = 'yeah';

        $dataModified = $data;
        $dataModified['url'] = $url;

        $table = $this->getMock('stdClass', ['buildTable']);
        $table->expects($this->once())
              ->method('buildTable')
              ->with($table, $results, $dataModified, $render)
              ->will($this->returnValue($return));

        $pm = $this->getMock('stdClass', ['get']);
        $pm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('url'))
            ->will($this->returnValue($url));



        $sl = $this->getMock('stdClass', ['get']);
        $sl->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Table'))
            ->will($this->returnValue($table));

        $sut = $this->getNewSut(['getPluginManager', 'getServiceLocator']);
        $sut->expects($this->once())
            ->method('getPluginManager')
            ->will($this->returnValue($pm));
        $sut->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($sl));

        $this->assertSame($return, $sut->getTable($table, $results, $data, $render));
    }

    public function testGetViewModel()
    {
        $params = array(['foo'=>'bar', 'baz'=>'whatever']);

        $sut = $this->getNewSut(['url']);

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $view = $sut->getViewModel($params));

        $this->assertSame($params, $view->getVariables());
    }

    public function testGetUrlFromRoute()
    {
        $route = 'hello';
        $params = array(['f']);

        $return = 'yeah';

        $urlMock = $this->getMock('stdClass', ['fromRoute']);
        $urlMock->expects($this->once())
                ->method('fromRoute')
                ->with($route, $params)
                ->will($this->returnValue($return));

        $sut = $this->getNewSut(['url']);
        $sut->expects($this->once())
            ->method('url')
            ->will($this->returnValue($urlMock));

        $this->assertSame($return, $sut->getUrlFromRoute($route, $params));
    }

    public function testGetFromRoute()
    {
        $route = 'craig-route';
        $params = array('1');
        $options = array('2');
        $reuse = true;

        $return = 'yeah';

        $redirectMock = $this->getMock('stdClass', ['toRoute']);
        $redirectMock->expects($this->once())
                     ->method('toRoute')
                     ->with($route, $params, $options, $reuse)
                     ->will($this->returnValue($return));

        $sut = $this->getNewSut(['redirect']);
        $sut->expects($this->once())
            ->method('redirect')
            ->will($this->returnValue($redirectMock));

        $this->assertSame($return, $sut->redirectToRoute($route, $params, $options, $reuse));
    }

    public function testGetSetLoggedInUser()
    {
        $sut = $this->getNewSut();

        $id = 'dsfakjf';
        $this->assertSame($id, $sut->setLoggedInUser($id)->getLoggedInUser());
    }


    public function getNewSut($methods = array())
    {
        $methods = array_merge($methods, ['log', 'addErrorMessage']);

        $mock = $this->getMock('\Common\Controller\AbstractActionController', $methods);

        return $mock;
    }
}