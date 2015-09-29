<?php

namespace Common\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

/**
 * Class ElasticSearch - Generates and processes calls to Elastic Search
 *
 * @package Olcs\Mvc\Controller\Plugin
 */
class ElasticSearch extends AbstractPlugin
{
    /**
     * Session container name
     * @var string
     */
    private $containerName;

    /**
     * Search Data
     * @var array
     */
    private $searchData;

    /**
     * Search type service
     * @var \Common\Service\Data\Search\SearchType
     */
    protected $searchTypeService;

    /**
     * Search service
     * @var \Common\Service\Data\Search
     */
    protected $searchService;

    /**
     * Navigation service
     * @var \Zend\Navigation\Navigation
     */
    protected $navigationService;

    /**
     * Page route to determine where forms should post and redirect to
     * @var string
     */
    private $pageRoute;

    /**
     * Invokes the plugin
     *
     * @param array $options
     * @return $this
     */
    public function __invoke($options = [])
    {
        $containerName = isset($options['container_name']) ? $options['container_name'] : 'global_search';

        if (isset($options['page_route'])) {
            $pageRoute = $options['page_route'];
        } else {
            $pageRoute = $this->getController()->getEvent()->getRouteMatch()->getMatchedRouteName();
        }

        $this->setContainerName($containerName);
        $this->setPageRoute($pageRoute);

        $this->setSearchData($this->extractSearchData());

        return $this;
    }

    /**
     * At first glance this seems a little unnecessary, but we need to intercept the post
     * and turn it into a get. This way the search URL contains the search params.
     */
    public function postAction()
    {
        $sd = $this->getSearchData();

        /**
         * Remove the "index" key from the incoming parameters.
         */
        $index = $sd['index'];
        unset($sd['index']);

        return $this->getController()->redirect()->toRoute(
            $this->getPageRoute(),
            ['index' => $index, 'action' => 'search'],
            ['query' => $sd, 'code' => 303],
            true
        );
    }

    public function backAction()
    {
        $sd = $this->getSearchData();

        /**
         * Remove the "index" key from the incoming parameters.
         */
        $index = $sd['index'];
        unset($sd['index']);

        return $this->getController()->redirect()->toRoute(
            'search',
            ['index' => $index, 'action' => 'search'],
            ['query' => $sd, 'code' => 303],
            true
        );
    }

    public function processSearchData()
    {
        $incomingParameters = [];

        if ($routeParams = $this->getController()->params()->fromRoute()) {
            $incomingParameters += $routeParams;
        }

        if ($postParams = $this->getController()->params()->fromPost()) {
            $incomingParameters += $postParams;
        }

        if ($queryParams = (array) $this->getController()->getRequest()->getQuery()) {
            $incomingParameters = array_merge($incomingParameters, $queryParams);
        }

        //there are multiple places search data can come from:
        //route, query, post and session

        //there are lots of params we are interested in:
        //filters, index, search query, page, limit

        //a post request can come from two forms a) the filter form, b) the query form
        $form = $this->getSearchForm();
        $form->setData($incomingParameters);

        if ($form->isValid()) {
            //save to session, reset filters in session...
            //get index from post as well, override what is in the route match
            $data = $form->getData();
            $this->getController()->getEvent()->getRouteMatch()->setParam('index', $data['index']);
        }

        $data = $this->getSearchForm()->getObject();

        //update data with information from route, and rebind to form so that form data is correct
        $data['index'] = $this->getController()->params()->fromRoute('index');
        $this->getSearchForm()->setData($data);

        if (empty($data['search'])) {
            $this->getController()->flashMessenger()->addErrorMessage('Please provide a search term');
            return $this->getController()->redirectToRoute('dashboard');
        }
    }

    /**
     * Returns the header search form.
     *
     * @return \Olcs\Form\Model\Form\HeaderSearch
     */
    public function getSearchForm()
    {
        $form = $this->getController()->getViewHelperManager()
            ->get('placeholder')
            ->getContainer('headerSearch')
            ->getValue();

        return $form;
    }

    /**
     * Returns the search filter form.
     *
     * @return \Olcs\Form\Model\Form\SearchFilter
     */
    public function getFiltersForm()
    {
        /** @var \Zend\Form\Form $form */
        $form = $this->getController()->getViewHelperManager()
            ->get('placeholder')
            ->getContainer('searchFilter')
            ->getValue();

        $sd = $this->getSearchData();

        $url = $this->getController()->url()->fromRoute(
            $this->getPageRoute(),
            ['index' => $sd['index'], 'action' => 'post'],
            ['query' => ['search' => $sd['search']]]
        );

        $form->setAttribute('action', $url);
        $form->setData($sd);

        return $form;
    }

    public function searchAction()
    {
        $sd = $this->getSearchData();

        $this->getFiltersForm();
        $data = $this->getSearchForm()->getObject();
        //override with get route index unless request is post

        $this->processSearchData();

        //update data with information from route, and rebind to form so that form data is correct
        $data['index'] = $this->getController()->params()->fromRoute('index');
        $this->getSearchForm()->setData($data);

        if (empty($data['search'])) {
            $this->flashMessenger()->addErrorMessage('Please provide a search term');
            return $this->redirectToRoute('dashboard');
        }

        $this->getSearchService()->setQuery($this->getController()->getRequest()->getQuery())
            ->setRequest($this->getController()->getRequest())
            ->setIndex($data['index'])
            ->setSearch($data['search']);

        $view = new ViewModel();
        $view->setTemplate('new/sections/search/pages/results');

        $view->indexes = $this->getSearchTypeService()->getNavigation('internal-search', ['search' => $sd['search']]);
        $view->results = $this->getSearchService()->fetchResultsTable();

        return $this->getController()->renderView($view, 'Search results');
    }

    /**
     * Sets the navigation to that secified in the controller. Useful for when a controller is
     * 100% reresented by a single navigation object.
     *
     * @see $this->navigationId
     *
     * @return boolean true
     */
    public function setNavigationCurrentLocation()
    {
        if (!empty($this->navigationId)) {
            $this->getNavigationService()->findOneBy('id', $this->navigationId)->setActive();
        }

        return true;
    }

    public function extractSearchData()
    {
        $remove = [
            'controller',
            'action',
            'module',
            'submit'
        ];

        $incomingParameters = [];

        if ($routeParams = $this->getController()->params()->fromRoute()) {
            $incomingParameters += $routeParams;
        }

        if ($queryParams = (array) $this->getController()->params()->fromQuery()) {
            $incomingParameters = array_merge($incomingParameters, $queryParams);
        }

        if ($postParams = (array) $this->getController()->params()->fromPost()) {
            $incomingParameters = array_merge($incomingParameters, $postParams);
        }

        // added this line as a quick fix for broken UT
        $incomingParameters['search'] = isset($incomingParameters['search']) ? $incomingParameters['search'] : '';

        $key = md5($this->getContainerName() . '_' . str_replace(' ', '', $incomingParameters['search']));
        $container = new Container($key);

        /**
         * Now remove all the data we don't want in the query string.
         */
        $incomingParameters = array_diff_key($incomingParameters, array_flip($remove));

        $incomingParameters = array_merge($container->getArrayCopy(), $incomingParameters);

        $container->exchangeArray($incomingParameters);

        return $container->getArrayCopy();
    }

    public function configureNavigation()
    {
        $sd = $this->getSearchData();

        $this->getController()->getViewHelperManager()
            ->get('placeholder')
            ->getContainer('horizontalNavigationContainer')
            ->set($this->getSearchTypeService()->getNavigation('internal-search', ['search' => $sd['search']]));
    }

    public function generateResults($view)
    {
        $data = $this->getSearchForm()->getObject();
        $data['index'] = $this->getController()->params()->fromRoute('index');

        $this->getSearchService()->setQuery($this->getController()->getRequest()->getQuery())
            ->setRequest($this->getController()->getRequest())
            ->setIndex($data['index'])
            ->setSearch($data['search']);

        $view->results = $this->getSearchService()->fetchResultsTable();
        $view->setTemplate('new/sections/search/pages/results');

        return $view;
    }

    /**
     * @param mixed $containerName
     */
    public function setContainerName($containerName)
    {
        $this->containerName = $containerName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContainerName()
    {
        return $this->containerName;
    }

    /**
     * @param mixed $searchData
     */
    public function setSearchData($searchData)
    {
        $this->searchData = $searchData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSearchData()
    {
        return $this->searchData;
    }

    /**
     * @param string $pageRoute
     */
    public function setPageRoute($pageRoute)
    {
        $this->pageRoute = $pageRoute;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageRoute()
    {
        return $this->pageRoute;
    }

    /**
     * @param \Common\Service\Data\Search\Search $searchService
     * @return ElasticSearch
     */
    public function setSearchService($searchService)
    {
        $this->searchService = $searchService;
        return $this;
    }

    /**
     * @return \Common\Service\Data\Search\Search
     */
    public function getSearchService()
    {
        return $this->searchService;
    }

    /**
     * @param \Common\Service\Data\Search\SearchType $searchTypeService
     * @return ElasticSearch
     */
    public function setSearchTypeService($searchTypeService)
    {
        $this->searchTypeService = $searchTypeService;
        return $this;
    }

    /**
     * @return \Common\Service\Data\Search\SearchType
     */
    public function getSearchTypeService()
    {
        return $this->searchTypeService;
    }

    /**
     * @param \Zend\Navigation\Navigation $navigationService
     */
    public function setNavigationService($navigationService)
    {
        $this->navigationService = $navigationService;
    }

    /**
     * @return \Zend\Navigation\Navigation
     */
    public function getNavigationService()
    {
        return $this->navigationService;
    }
}
