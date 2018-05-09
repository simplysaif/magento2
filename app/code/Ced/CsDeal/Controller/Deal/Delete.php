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

class Delete extends \Ced\CsMarketplace\Controller\Vendor
{
 
    public function execute()
    {
    	$vendorId=$this->_getSession()->getVendorId();
    	$dealIds = $this->getRequest()->getParam('deal_id');
    	try {
					if($dealIds) {
						$model=$this->_objectManager->get('Ced\CsDeal\Model\Deal')->load($dealIds);
						$this->_eventManager->dispatch('controller_action_predispatch_ced_csdeal_delete', array('deal' => $model,));
						$model->delete();
						$model->save();
						$this->messageManager->addSuccessMessage(__('Deal Deleted Successfully.'));
					}
					
				} catch (Exception $e) {
					$this->messageManager->addError($e->getMessage());
				}
		$this->_redirect('*/*/listi');

	}
    
}
