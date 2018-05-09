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

 namespace Ced\Blog\Model;

 use Ced\Blog\Model\IFrontendRoute;

 class Blogcat extends \Magento\Framework\Model\AbstractModel implements IFrontendRoute
 {

    /**
     *@var ROUTE_ACTION_NAME
     */

    const ROUTE_ACTION_NAME = 'category';

    /**
     *@var ROUTE_CONTROLLER_NAME
     */

    const ROUTE_CONTROLLER_NAME = 'index';

    /**
     *@var Magento\Framework\Registry
     */

    protected $_coreRegistry;

    /**
     *@var Magento\Framework\Model\Context
     */

    protected $_context;

    /**
     *@var Magento\Framework\UrlInterface
     */

    protected $urlBuilder;

    /**
     *@var Magento\Framework\ObjectManagerInterface
     */

    protected $objectManager;

    /**
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Framework\Model\Context
     * @param Magento\Framework\UrlInterface
     * @param Magento\Framework\Registry
     * @param Magento\Framework\Model\ResourceModel\AbstractResource
     * @param Magento\Framework\Data\Collection\AbstractDb
     */

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface  $urlBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
        ) { 

        $this->objectManager = $objectManager;
        $this->_context = $context;
        $this->urlBuilder = $urlBuilder;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }

    /**
     *@var construct
     */

    protected function _construct()
    {
        $this->_init('Ced\Blog\Model\ResourceModel\Blogcat');
    }

    /**
     *@var getRouteControllerName
     */

    public function getRouteControllerName()
    {

        return self::ROUTE_CONTROLLER_NAME;

    }

    /**

     *@var getRouteActionName
     */

    public function getRouteActionName()
    {

        return self::ROUTE_ACTION_NAME;

    }

    /**
     *@var getRouteParams
     */

    public function getRouteParams($id)
    {
        return array('id' => $id);

    }

    /**
     *@var checkIdentifier
     */

    public function checkIdentifier($identifier)
    {

        return $this->_getResource()->checkIdentifier($identifier);

    }

    /**
     *@var getListUrl
     */

    public function getListUrl()
    {   

        $helper = $this->objectManager->create('Ced\Blog\Helper\Data')->getFrontendName();
        $url = $this->urlBuilder->getUrl($helper.'/'.'category'.'/'.$this->getUrlKey(), null);  
        $baseUrl = $this->urlBuilder->getUrl();
            if($baseUrl){
                $baseUrlNew = $baseUrl.''.'blog';
                $replace = $this->urlBuilder->getUrl().''.'blog/index';
                $newPostUrl = str_replace($baseUrlNew,$replace, $url);               
                return $newPostUrl;
            }
            else{
                return $url;
            }

    }

    /* added*/

    public function validate($request)
    {

        $this->_eventManager->dispatch($this->_eventPrefix . '_validate_before', $this->_getEventData());
        $result = $this->_getResource()->validate($this, $request);
        $this->_eventManager->dispatch($this->_eventPrefix . '_validate_after', $this->_getEventData());
        return $result;
    }
}