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

namespace Ced\CsMarketplace\Controller\Vpayments;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class RequestFilter extends \Magento\Framework\App\Action\Action {
    public function __construct( Context $context,
        \Magento\Customer\Model\Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Ced\CsMarketplace\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime, array $data = []) 
    {
    	$this->resultPageFactory = $resultPageFactory;
        $this->_getSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->csmarketplaceHelper=$helperData;
        $this->datetime = $datetime;
        parent::__construct ( $context, $data );
    }

    public function execute()
    {

        if(!$this->_getSession->getVendorId()) return;
		$reset_filter = $this->getRequest()->getParam('reset_filter');
        
		$params = $this->getRequest()->getParams();
		
		if($reset_filter==1)
			$this->_objectManager->get('\Magento\Customer\Model\Session')->uns('payment_request_filter');
		else if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
			$this->_objectManager->get('\Magento\Customer\Model\Session')->setData('payment_request_filter',$params);
		 }
	    $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Transactions'));
        return $resultPage;
    }
}
