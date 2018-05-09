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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Controller\Customerrma;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;

class Cancelorder extends \Ced\CsRma\Controller\Link

{
	/**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */

	public function execute()
	{
		
		$id = $this->getRequest()->getParam('order_id');
		
		try{
		     $this->_objectManager->create('Magento\Sales\Api\OrderManagementInterface')->cancel($id);
		     $this->messageManager->addSuccess('Orders Cancelled  Successfully...');
		    
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
           
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong  with this request.'));
            
        }
        return $this->_redirect('sales/order/view',['order_id'=>$id]);
       
	}

}