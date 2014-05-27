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
    public function testOnDispatch()
    {
        $headers = $this->getMock('stdClass', ['addHeaderLine']);

        $response = $this->getMock('stdClass', ['getHeaders']);
        $response->expects($this->once())
                 ->method('getHeaders')
                 ->will($this->returnValue($headers));

        $sut = $this->getNewSut(['getResponse']);
        $sut->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $sut->preOnDispatch();
    }

    public function testSetBreadcrumb()
    {
        $navRoutes = [
            'route1' => ['foo' => 'bar', 'baz' => 'bo']
        ];

        $page = $this->getMock('stdClass', ['setParams']);

        $nav = $this->getMock('stdClass', ['findBy']);
        $nav->expects($this->once())
            ->method('findBy')
            ->with('route', 'route1')
            ->will($this->returnValue($page));

        $sl = $this->getMock('\Zend\ServiceManager\ServiceManager', ['get']);
        $sl->expects($this->once())
           ->method('get')
           ->with($this->equalTo('navigation'))
           ->will($this->returnValue($nav));

        $sut = $this->getNewSut(['getServiceLocator']);
        $sut->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($sl));

        $sut->setBreadcrumb($navRoutes);
    }

    public function testGetParams()
    {
        $params = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];

        $sut = $this->getNewSut(['getAllParams']);
        $sut->expects($this->once())
            ->method('getAllParams')
            ->will($this->returnValue($params));

        $this->assertEquals(['foo' => 'bar'], $sut->getParams(['foo']));
    }

    public function testGetAllParams()
    {
        $routeParams = ['foo' => 'bar'];
        $queryParams = ['foo' => 'bar21'];

        $sut = $this->getNewSut(['getEvent', 'getRequest']);

        // --

        $routeMatch = $this->getMock('stdClass', ['getParams']);
        $routeMatch->expects($this->once())
                   ->method('getParams')
                   ->will($this->returnValue($routeParams));

        $event = $this->getMock('stdClass', ['getRouteMatch']);
        $event->expects($this->once())
              ->method('getRouteMatch')
              ->will($this->returnValue($routeMatch));

        $sut->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($event));

        // --

        $query = $this->getMock('stdClass', ['toArray']);
        $query->expects($this->once())
              ->method('toArray')
              ->will($this->returnValue($queryParams));

        $request = $this->getMock('stdClass', ['getQuery']);
        $request->expects($this->once())
                ->method('getQuery')
                ->will($this->returnValue($query));

        $sut->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        /* print_r(array_merge($routeParams, $queryParams));
        print_r($sut->getAllParams());
        exit(); */

        //;

        $this->assertEquals(array_merge($routeParams, $queryParams), $sut->getAllParams());
    }

    /**
     *
     */
    public function testCheckForCrudActionReturnsFalse()
    {
        $paramsMock = $this->getMock('stdClass', ['fromPost']);
        $paramsMock->expects($this->at(0))
                   ->method('fromPost')
                   ->with('action')
                   ->will($this->returnValue(''));

        $sut = $this->getNewSut(['params']);
        $sut->expects($this->atLeastOnce())
            ->method('params')
            ->will($this->returnValue($paramsMock));

        $this->assertFalse($sut->checkForCrudAction());
    }

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

    public function testRedirectToRoute()
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

    public function testGetFromRoute()
    {
        $param = 'foo';
        $return = 'uouo';

        $mock = $this->getMock('stdClass', ['fromRoute']);
        $mock->expects($this->once())
             ->method('fromRoute')
             ->with($this->equalTo($param))
             ->will($this->returnValue($return));

        $sut = $this->getNewSut(['params']);
        $sut->expects($this->once())
            ->method('params')
            ->will($this->returnValue($mock));

        $this->assertSame($return, $sut->getFromRoute($param));
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
