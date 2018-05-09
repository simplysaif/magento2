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
 * @category    Ced
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Controller\Deal;
use Magento\Customer\Model\Session;
use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\UrlFactory;

class Expire extends \Magento\Framework\App\Action\Action
{
    
	protected $_scopeConfig;
	protected $_storeManager;
	protected $_httpRequest;
	protected $resultPageFactory;
    
    public function execute() {
		
		$post_data=$this->getRequest()->getPost('product_id');
		$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($post_data);
	    try{
	        $product->setSpecialPrice(null);
            $product->getResource()->saveAttribute($product, 'special_price');
            $product->save();
            //$this->_objectManager->get('Psr\Log\LoggerInterface')->debug("deal expired");
	        $deal = $this->_objectManager->create('Ced\CsDeal\Model\Deal')->load($post_data, 'product_id');
	        $deal->delete();
	        }catch(\Exception $e){
	        	$this->messageManager->addError(__($e->getMessage()));
	        } 
    }
}