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
 * @package     Ced_QuickOrder
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\QuickOrder\Controller\Quickorder;
use \Magento\Framework\Controller\ResultFactory;
class Bulkaddtocart extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $_custmerSesion;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
    	\Magento\Framework\App\Action\Context $context,
    	\Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_custmerSesion = $session;
        parent::__construct($context);
    }
    public function execute()
    {
    	$data = $this->getRequest()->getPostValue();
    	//print_r($data);die;
    	unset($data['form_key']);
    	foreach($data as $key=>$value){
    		if(isset($value['checkbox'])){
    		 if(!isset($value['super_attribute']) && !isset($value['super_group'])){
    		$params = array(
    				'product' => $key, // This would be $product->getId()
    				'qty' => $value['qty'],	
    		);
    		}
    		if(isset($value['super_attribute'])){
    			$params = array(
    					'product' => $key, // This would be $product->getId()
    					'qty' => $value['qty'],
    			        'super_attribute'=>$value['super_attribute']
    			
    			);
    		}
    		if(isset($value['super_group'])) 
    		{
    			$params = array(
    					'product' => $key,
    					'super_group'=>$value['super_group']
    					 
    			);
    		}
    		if(isset($value['links'])){
    			
    			$params = array(
    					'product' => $key,
    					'qty'=>$value['qty'],
    					'links'=>$value['links']
    			
    			);
    		}
    		if(isset($value['bundle_option'])){
    			if(isset($value['bundled_checbkox_data'])){
    				
    			foreach($value['bundle_option'] as$key2=> $_bundle){
    				
    				if(is_array($_bundle)){
    					
    					foreach($_bundle as$key3=>$bundle){
    						
    						$bundles[$value['bundled_checbkox_data'][$key3]] =$bundle;
    						
    					}
    				}
    				$_bundles[$key2] = $bundles;
    			}
    			$params = array(
    					'product' => $key,
    					'qty'=>$value['qty'],
    					'bundle_option'=>$_bundles
    			
    			);
    			}
    			else{
	    			$params = array(
	    					'product' => $key,
	    					'qty'=>$value['qty'],
	    					'bundle_option'=>$value['bundle_option']
	    					 
	    			);
    			}
    		}
    		$cart = $this->_objectManager->create ('Magento\Checkout\Model\Cart');
    		$productobj = $this->_objectManager->create ('Magento\Catalog\Model\Product' )->load ($key);
    		if(!$productobj->getId())
    		{
    			$this->messageManager->addError(__('Product Does Not Exist.Contact Administrator'));
    			$this->_redirect('*/*/view');
    			return;
    		}
    		//$cart->truncate();
    		try{
    		$cart->addProduct($productobj,$params);
    		}catch(\Exception $e){
    			
    		    $this->messageManager->addError(__($e->getMessage()));
    		    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                 $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                 return $resultRedirect;
    		}
    		
    	}
    }
    	$cart->save();
    	$this->_redirect('checkout/cart');
    	return ;
    }
}
