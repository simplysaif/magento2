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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\TeamMember\Controller\Vendor;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;

class Send extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
	protected $session;
	
    public function execute()
    {    
    	if(!$this->session->getVendorId())
    		return;
    	
    	//echo $this->getRequest()->getParam('vendorid');

    	$model = $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage');
    	$model->setSender('vendor');
    	$model->setSenderEmail($this->session->getVendor()['email']);
    	$model->setReceiver('teammember');
    	$model->setReceiverEmail($this->getRequest()->getParam('memberemail'));
    	$model->setMessage($this->getRequest()->getParam('message'));
    	$model->save();
      $this->messageManager->addSuccess(__('Message Sent Successfully'));
    }
}
