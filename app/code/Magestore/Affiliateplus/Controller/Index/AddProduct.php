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
 * @package     Magestore_Affiliateplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Affiliateplus\Controller\Index;

/**
 * Action Index
 */
class AddProduct extends \Magestore\Affiliateplus\Controller\AbstractAction
{


    /**
     * Execute action
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $this->xlog($params);
        $response  = array();
        if ($params){
            $accountProduct = $this->_objectManager->create('Magestore\Affiliateplus\Model\AccountProduct');
            $accountProduct->setProductId($params['productid']);
            $accountProduct->setAccountId($params['accountid']);
            $accountProduct->save();
            $response['msg'] = 'success';
        }
        $this->getResponse()->setBody(json_encode($response));
    }
    public function xlog($message = 'null')
    {
        $log = print_r($message, true);
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug($log);
    }


}
