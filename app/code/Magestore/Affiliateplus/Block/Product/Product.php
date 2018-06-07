<?php

namespace  Magestore\Affiliateplus\Block\Product;

class Product extends \Magestore\Affiliateplus\Block\AbstractTemplate
{
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