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
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultistepreg\Controller\Multistep;
use \Magento\Framework\App\Request\Http;
use Magento\Backend\App\Action\Context;
use \Magento\Customer\Model\Session;
class Save extends  \Magento\Framework\App\Action\Action{
	private $request;
	private $_session;
	public function __construct(Context $context,Http $request,Session $session){
		parent::__construct($context);
		$this->request = $request;
		$this->_session = $session;
	}
	
	public function execute(){
		$vendorId = $this->getRequest()->getParam('vendor_id');
		if($vendorId){
			$vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
			$vendor->addData($this->getRequest()->getParam('vendor'));
			try{
				$vendor->save();
				$eventManager = $this->_objectManager->get('Magento\Framework\Event\ManagerInterface');
				$eventManager->dispatch('vendor_multistepregistration_complete_after',array('vendor'=>$vendor));
				return $this->_redirect('csmarketplace/account/approval');
			}catch (Exception $e){
				
			}
		}
	}
}