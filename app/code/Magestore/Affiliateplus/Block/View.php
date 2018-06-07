<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Shopbybrand
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Affiliateplus\Block;

/**
 * @category Magestore
 * @package  Magestore_Shopbybrand
 * @module   Shopbybrand
 * @author   Magestore Developer
 */
use Magento\Catalog\Api\CategoryRepositoryInterface;
class View extends \Magento\Catalog\Block\Product\ListProduct
{


    /**
     * @var \Magestore\Shopbybrand\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magestore\Shopbybrand\Helper\Brand
     */
    protected $_helperBrand;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_helperImage;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $_categoryHelper;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Helper\Output $helperOutput
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,

        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    )
    {
        $this->_productFactory = $productFactory;
        $this->_helperImage = $context->getImageHelper();
        $this->_objectManager = $objectManager;
        $this->_categoryHelper = $categoryHelper;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }


    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function _getAccount(){
        $this->xlog($this->getRequest()->getParams());
        $params = $this->getRequest()->getParams();
        $accountId =$params['id'] ;
        $account = null;
        if($accountId) {
            $account = $this->_objectManager->create('Magestore\Affiliateplus\Model\Account')->load($accountId);
        }
        return $account;
    }

    /**
     *	Added By Adam (31/08/2016)
     * Get list product Ids which were assigned to affiliate
     */
    public function _getListProductIdsByAccount(){
        $account = $this->_getAccount();
        $productIds = array();
        if($account && $account->getStatus() == 1){
            $collection = $this->_objectManager->create('Magestore\Affiliateplus\Model\AccountProduct')->getCollection()
                ->addFieldToFilter('account_id', $account->getId());
            foreach($collection as $item) {
                $productIds[] = $item->getProductId();
            }
        }
        return $productIds;
    }

    public function _getProductCollection()
    {
        $productIds = $this->_getListProductIdsByAccount();
        if (is_null($this->_productCollection)) {

            $collection =$this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('status',1)
                ->addFieldToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE])
                ->addFieldToFilter('entity_id', array('in'=>$productIds))
            ;

            $this->_productCollection = $collection;


//            //2 dong in dam ben duoi la bat buoc phai co
//            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
//            Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);
        }
        $this->xlog($this->_productCollection->getSize());
        return $this->_productCollection;

    }

    public function xlog($message = 'null')
    {
        $log = print_r($message, true);
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug($log);
    }


}