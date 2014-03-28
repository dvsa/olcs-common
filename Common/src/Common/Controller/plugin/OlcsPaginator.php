<?php

/*
 * Extends Zend Paginator. Takes in a list object and returns a paginator object to be
 * used in list pagination.
 *
 * @author Mike Cooper
 */

namespace Common\Controller\Plugin;

use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Paginator\Adapter\Collection as Adapter;
use Zend\Paginator\Paginator;

class OlcsPaginator extends \Zend\Paginator\Paginator {
    
    public $paginator;
    
    private $numPerPage = 10;
    
    public function __construct() {
    }
    
    public function getPaginatedResults($params, $controller)
    {
        $pageDetails = $this->getPageDetails($controller);
        $data = $controller->getListData(null, null, $pageDetails['limit'], $pageDetails['offset']);
        return $this->createPaginator($controller, $data, $pageDetails['page'], $controller->route);
    }
    
    public function getPageDetails($controller)
    {
        $routeParams = $controller->getEvent()->getRouteMatch()->getParams();
        if (isset($routeParams['s'])) {
            $this->setNumPerPage($routeParams['s']);
        }
        $currentPage = isset($routeParams['page']) ? $routeParams['page'] : 1;
        $limit = $this->getNumPerPage();

        return array(
            'offset' => $this->getOffset($currentPage, $limit),
            'limit' => $limit,
            'page' => $currentPage,
        );
    }

    public function createPaginator($controller, $data, $currentPage, $route)
    {
        $this->setPaginator($data, $currentPage);
        $this->route = $route;
        $this->routeParams = $controller->getEvent()->getRouteMatch()->getParams();
        $this->queryString = $controller->getRequest()->getQuery()->toArray();
        return $this;
    }
    
    public function setPaginator($data, $currentPage=1, $pageRange=5, $itemsPerPage=null) 
    {
        $data = (object)$data;
        if (!empty($itemsPerPage)) $this->numPerPage = $itemsPerPage;
        $collection = new ArrayCollection($data->listData);
        parent::__construct(new Adapter($collection));
        
        $this->setCurrentPageNumber(1)
                ->setItemCountPerPage($this->numPerPage)
                ->setPageRange(5);
        $this->realCurrentPage = $currentPage; // Sets the current page to the page passed in the url
        $this->pageCount = ceil($data->listCount / $this->numPerPage);
        $this->totalCount = $data->listCount;
    }
    
    public function GetOffset($currentPage, $numPerPage=null) 
    {
        if (!empty($numPerPage)) $this->numPerPage = $numPerPage;
        return $currentPage == 1 ? 0 : (($currentPage - 1) * $this->numPerPage);
    }
    
    public function getNumPerPage() 
    {
        return $this->numPerPage;
    }
    
    public function setNumPerPage($numPerPage) 
    {
        return $this->numPerPage = $numPerPage;
    }
    
    public function setThisPage($thisPage) 
    {
        return $this->thisPage = $numPerPage;
    }
    
}
