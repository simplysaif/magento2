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

namespace Magestore\Affiliateplus\Block\Substore\Catalog;

/**
 * @category Magestore
 * @package  Magestore_Shopbybrand
 * @module   Shopbybrand
 * @author   Magestore Developer
 */
class ProductView extends \Magestore\Affiliateplus\Block\AbstractTemplate
{

    protected $collectionFactory;
//    /**
//     * Contruct
//     */
//    protected function _construct(
//        \Magestore\Affiliateplus\Model\ResourceModel\AccountProduct\CollectionFactory $collectionFactory
//    ){
//        $this->collectionFactory = $collectionFactory;
//        parent::_construct();
//
//    }

    /**
     * get Account
     *
     * @return mixed
     */
    public function getAccount(){
        return $this->_sessionModel->getAccount();
    }


    /**
     * get ShowSubstore
     *
     * @return mixed
     */
    public function getShowSubstore(){
        $isShow = $this->_objectManager->create('Magestore\Affiliateplus\Helper\SubStore')->isShowSubstore();
        return $isShow;
    }

    public function getProduct(){
        $params = $this->getRequest()->getParams();
        $productId = $params['id'];
        $productModel = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
        return $productModel;

    }
    public function checkExistAccountProduct($accountId,$productId){
        $collection  = $this->_objectManager->create('\Magestore\Affiliateplus\Model\AccountProduct')->getCollection()
            ->addFieldToFilter('product_id',$productId)
            ->addFieldToFilter('account_id',$accountId);
        if($collection->getSize()){
            return true;
        }else{
            return false;
        }

    }
    public function xlog($message = 'null')
    {
        $log = print_r($message, true);
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug($log);
    }


}