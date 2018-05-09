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
namespace Ced\CsRma\Controller\Vrma; 

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     *
     * @var \Magento\Framework\View\Result\Page 
    */

    protected $resultPageFactory;

    /*
    * Get the value of customer session
    */

    public function _getSession() 
    {
        return Mage::getSingleton('customer/session');
    }
    
    
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    /**
     * @var execute
     */

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Ced\CsRma\Model\Request');
        $rmaOrderHelper = $this->_objectManager->get('Ced\CsRma\Helper\OrderDetail');

        $dateTime = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        if ($data) 
        {
            if($id = $this->getRequest()->getParam('rma_request_id')) { 
                $model->load($id);
            }
            $allowCompleted = false;
            if($model->getStatus() == 'Pending'){
            	$allowCompleted = true;
            }
          
            if($model->getStatus() != "Approved" && $data['status'] !='Cancelled' &&  $model->getStatus() != "Cancelled" && $model->getStatus() != "Completed"){
	            if($data['status']=='Approved' || $allowCompleted && $data['resolution_requested']=='Refund'|| $data['resolution_requested']=='Cancel'){
	            	
	            	$creditemo = $rmaOrderHelper->generateCreditMemoForRma($id,$data);
	                 
	                if(isset($creditemo['error']) && $creditemo['error']){
	                    $this->messageManager->addError(__('Cant Save Rma Request'));
	                    return $resultRedirect->setPath('*/*/');
	                }
	            }
            }
            $model->setData('updated_at',$dateTime->gmtDate());

            try {
            	$model->setData('status',$data['status']);
                $model->save();
                $this->messageManager->addSuccess(__('Updated Successfully'));
                $this->_objectManager->get('Magento\Customer\Model\Session')->setFormData(false);
                return $resultRedirect->setPath('*/*/index');

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the page.'));

            }   
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('rma_request_id')]);
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
