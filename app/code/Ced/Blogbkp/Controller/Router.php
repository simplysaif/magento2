<?php



 /**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_Blog
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\Blog\Controller;

/**
 * Blog  Controller Router
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */

class Router implements \Magento\Framework\App\RouterInterface
{

    /**
     * @var const FRONTEND_NAME = 'blog'
     */

    const FRONTEND_NAME = 'blog';

    /**
     * @var \Magento\Framework\App\ActionFactory
     */

    protected $actionFactory;

    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */

    protected $_eventManager;


    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */

    protected $_storeManager;

    /**
     * Page factory
     * @var \Magento\Cms\Model\PageFactory
     */

    protected $_pageFactory;


    /**
     * Config primary
     * @var \Magento\Framework\App\State
     */

    protected $_appState;


    /**
     * Url
     * @var \Magento\Framework\UrlInterface
     */

    protected $_url;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */

    protected $_response;

    /**
     * configuartion
     * @var \\Magento\Framework\App\Config\ScopeConfigInterface
     */

    protected $_scopeConfig;

    /**
     * $_frontendName
     */

    private $_frontendName;


    /**
     * $_objectManager
     *
     * @var \Magento\Framework\App\ObjectManager $objectManager
     */

    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\ActionFactory       $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface  $eventManager
     * @param \Magento\Framework\App\ObjectManager       $objectManager
     * @param \Magento\Framework\UrlInterface            $url
     * @param \Magento\Cms\Model\PageFactory             $pageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface   $response
     */

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResponseInterface $response
    ) {

        $this->resultForwardFactory= $resultForwardFactory;
        $this->actionFactory = $actionFactory;
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->_response = $response;

    }

    /**

     * @param \Magento\Framework\App\ResponseInterface $response
     */

    
    private function getFrontendName()
    {

        if (!$this->_frontendName) {
            $this->_frontendName = $this->_scopeConfig->getValue('ced_blog/general/route_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);
            if (!$this->_frontendName) {
                $this->_frontendName = self::FRONTEND_NAME;

            }
        }
        return $this->_frontendName;

    }

    

    /**
     * Validate and Match Blog Url and modify request
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */

    public function match(\Magento\Framework\App\RequestInterface $request)
    {

        $identifier = trim($request->getPathInfo(), '/');
        $idarray = explode('/', $identifier);
        $helper = $this->_objectManager->create('Ced\Blog\Helper\Data')->enableModule();
        if($helper) {
            if ($idarray[0] != $this->getFrontendName()) {
                return false;
            }

            if (!isset($idarray[1]) || empty($idarray[1])) {
                $controllerName = 'index';
                $actionName = 'index';
                $params = $request->getParams();
                $request->setModuleName(self::FRONTEND_NAME)
                    ->setControllerName($controllerName)
                    ->setActionName($actionName)
                    ->setParams($params);
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $this->getFrontendName());
            }
            unset($idarray[0]);
            $identifier = implode("/", $idarray);
            $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);

            $this->_eventManager->dispatch(
                'blog_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );

            $identifier = $condition->getIdentifier();
            if ($condition->getRedirectUrl()) {
                $this->_response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create('Magento\Framework\App\Action\Redirect');
            }

            if (!$condition->getContinue()) {

                return null;
            }
            $actionName = false;
            $controllerName = false;
            $params = array();
            $identy  = explode('/', $identifier);
          
            if($identy[0]=='post') {
                $post_collection = $this->_objectManager->create('Ced\Blog\Model\BlogPost');
                if(isset($identy[1]))
                {
                    $id=$post_collection->checkIdentifier($identy[1]);
                }
                if(isset($id)) {
                       $actionName = $post_collection->getRouteActionName();
                       $controllerName = $post_collection->getRouteControllerName();
                       $params = array_merge($params, array_merge($post_collection->getRouteParams($id), $request->getParams()));
                }else {
                    $request->setActionName('noroute');
                    $request->setDispatched(false);
                    return;
                }
            }

            if($identy[0]=='author') {
                $author_collection = $this->_objectManager->create('Ced\Blog\Model\BlogPost');
                if($id = $author_collection->checkAuthor($identy[1])) {
                    $actionName = 'profile';
                    $controllerName ='index';
                    $params = array_merge($params, array_merge($author_collection->getRouteParams($id), $request->getParams()));
                }else {
                    $request->setActionName('noroute');
                    $request->setDispatched(false);
                    return;
                }
            }

            if($identy[0]=='tag') {
                $tag_collection = $this->_objectManager->create('Ced\Blog\Model\BlogPost');
                if($id = $tag_collection->checkTag($identy[3])) {
                    $actionName = 'tags';
                    $controllerName = 'index';
                    $params = array_merge($params, array_merge($tag_collection->getRouteParams($id), $request->getParams()));
                }else {
                    $request->setActionName('noroute');
                    $request->setDispatched(false);
                    return;
                }
            }

            if($identy[0]=='category') {
                $category_collection = $this->_objectManager->create('Ced\Blog\Model\Blogcat');
                if($id = $category_collection->checkIdentifier($identy[1])) {
                    $controllerName = $category_collection->getRouteControllerName();
                    $actionName = $category_collection->getRouteActionName();
                    $params = array_merge($params, array_merge($category_collection->getRouteParams($id), $request->getParams()));
                }else {

                    $request->setActionName('noroute');
                    $request->setDispatched(false);
                    return;

                }

            }

            $request->setModuleName(self::FRONTEND_NAME)
                ->setControllerName($controllerName)
                ->setActionName($actionName)
                ->setParams($params);
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $this->getFrontendName(). '/'.$identifier); 
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        }else{
            $request->setActionName('noroute');
            $request->setDispatched(false);
        }
    }
}

