<?php
namespace Common\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Common\Service\Data\Search\Search;
use Olcs\Service\Data\Search\SearchType;
use Zend\Session\Container;

/**
 * Class ElasticSearch - Generates and processes calls to Elastic Search
 *
 * @package Olcs\Mvc\Controller\Plugin
 */
class ElasticSearch extends AbstractPlugin
{
    private $containerName;

    private $searchData;

    /**
     * Layout template to use for the results page - defaults to main-search-results with index nav,
     * filter form and results table
     * @var
     */
    private $layoutTemplate;

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
        $layoutTemplate = isset($options['layout_template']) ? $options['layout_template'] : 'main-search-results';
        $currentRouteMatch = $this->getController()->getEvent()->getRouteMatch()->getMatchedRouteName();
        $pageRoute = isset($options['page_route']) ? $options['page_route'] : $currentRouteMatch;

        $this->setContainerName($containerName);
        $this->setLayoutTemplate($layoutTemplate);
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
        // Crazy race condition means that we need to "build" the form here!
        /** @var \Olcs\Service\Data\Search\Search $searchService **/
        $searchService = $this->getController()->getServiceLocator()->get('DataServiceManager')->get(Search::class);

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

        /**
         * This might
         */
        $this->getFiltersForm();
        $data = $this->getSearchForm()->getObject();
        //override with get route index unless request is post

        $this->processSearchData();

        //update data with information from route, and rebind to form so that form data is correct
        $data['index'] = $this->params()->fromRoute('index');
        $this->getSearchForm()->setData($data);

        if (empty($data['search'])) {
            $this->flashMessenger()->addErrorMessage('Please provide a search term');
            return $this->redirectToRoute('dashboard');
        }

        /** @var Search $searchService **/
        $searchService = $this->getServiceLocator()->get('DataServiceManager')->get(Search::class);

        $searchService->setQuery($this->getRequest()->getQuery())
            ->setRequest($this->getRequest())
            ->setIndex($data['index'])
            ->setSearch($data['search']);

        $view = new ViewModel();

        /** @var SearchType $searchService **/
        $searchTypeService = $this->getServiceLocator()->get('DataServiceManager')->get(SearchType::class);

        $view->indexes = $searchTypeService->getNavigation('internal-search', ['search' => $sd['search']]);
        $view->results = $searchService->fetchResultsTable();

        $layout = 'layout/' . $this->getLayoutTemplate();
        $view->setTemplate($layout);

        return $this->renderView($view, 'Search results');
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
        $navigation = $this->getServiceLocator()->get('Navigation');
        if (!empty($this->navigationId)) {
            $navigation->findOneBy('id', $this->navigationId)->setActive();
        }

        return true;
    }

    public function extractSearchData()
    {
        $container = new Container($this->getContainerName());

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

        /**
         * Now remove all the data we don't want in the query string.
         */
        $incomingParameters = array_diff_key($incomingParameters, array_flip($remove));

        $incomingParameters = array_merge($container->getArrayCopy(), $incomingParameters);

        $container->exchangeArray($incomingParameters);

        return $container->getArrayCopy();
    }

    public function generateNavigation($view)
    {
        /** @var SearchType $searchService **/
        $searchTypeService = $this->getController()->getServiceLocator()
            ->get('DataServiceManager')
            ->get(SearchType::class);

        $sd = $this->getSearchData();
        $view->indexes = $searchTypeService->getNavigation('internal-search', ['search' => $sd['search']]);

        return $view;
    }

    public function generateResults($view)
    {
        $data = $this->getSearchForm()->getObject();
        $data['index'] = $this->getController()->params()->fromRoute('index');

        /** @var Search $searchService **/
        $searchService = $this->getController()->getServiceLocator()->get('DataServiceManager')->get(Search::class);

        $searchService->setQuery($this->getController()->getRequest()->getQuery())
            ->setRequest($this->getController()->getRequest())
            ->setIndex($data['index'])
            ->setSearch($data['search']);

        $view->results = $searchService->fetchResultsTable();

        $layout = 'layout/' . $this->getLayoutTemplate();
        $view->setTemplate($layout);

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
     * @param mixed $layoutTemplate
     */
    public function setLayoutTemplate($layoutTemplate)
    {
        $this->layoutTemplate = $layoutTemplate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLayoutTemplate()
    {
        return $this->layoutTemplate;
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
}
