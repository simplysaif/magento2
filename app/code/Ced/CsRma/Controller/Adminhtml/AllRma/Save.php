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
namespace Ced\CsRma\Controller\Adminhtml\AllRma;

/**
 * @var Magento\Backend\App\Action
 */
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action

{
	/**
     * @var \Ced\CsRma\Model\RmaitemsFactory
     */
    protected $rmaItemFactory;

	/**
    * @var \Ced\CsRma\Model\RequestFactory
    */

    protected $rmaRequestFactory;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */

	protected $dateTime;

	/**
	 * @var \Ced\CsRma\Helper\OrderDetail
	 */
	protected $rmaOrderHelper;
	/**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory
     * @param \Ced\Rma\Model\RmaitemsFactory $rmaItemFactory
     * @param \Ced\Rma\Model\RequestFactory $rmaRequestFactory
     * @param \Magento\Backend\App\Action\Context $context
     */ 

	public function __construct(
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Ced\CsRma\Model\RmaitemsFactory $rmaItemFactory,
    	\Ced\CsRma\Model\RequestFactory $rmaRequestFactory,
    	\Ced\CsRma\Helper\OrderDetail $rmaOrderHelper,
		Context $context
	) {
		parent::__construct($context);
		$this->rmaOrderHelper = $rmaOrderHelper;
		$this->dateTime = $dateTime;
		$this->rmaItemFactory = $rmaItemFactory;
        $this->rmaRequestFactory = $rmaRequestFactory;
		
	}

	/**
	 * @var execute
	 */

	public function execute()
    {
    	$data = $this->getRequest()->getParams();
    	$additionalRefund ="";
    	if(isset($data['additional_refund'])){
    		$additionalRefund = $data['additional_refund'];
    	}
        

    	$resultRedirect = $this->resultRedirectFactory->create();
    	$model = $this->rmaRequestFactory->create();
        if ($data) 
        {
			if($id = $this->getRequest()->getParam('rma_request_id')) {	
				$model->load($id);
			}
			$allowCompleted = false;
            if($model->getStatus() =='Pending'){
                $allowCompleted = true;                 	
            }  
			
            if($model->getStatus() != "Approved" && $data['status'] != "Cancelled" && $model->getStatus() != "Cancelled" && $model->getStatus() != "Completed"){
				if($data['status']=='Approved' || $allowCompleted && $data['resolution_requested']=='Refund'|| $data['resolution_requested']=='Cancel'){
					$creditemo = $this->rmaOrderHelper->generateCreditMemoForRma($id,$data);
			
					if(isset($creditemo['error']) && $creditemo['error']){
	                    $this->messageManager->addError(__($creditemo['error']));
	                    return $resultRedirect->setPath('*/*/');
	                }
					   
						$model->setData('updated_at',$this->dateTime->gmtDate());
	
					if(isset($data['additional_refund'])){
						$model->setData('additional_refund',$additionalRefund);
					}
					if(isset($data['vendor_adjustment_amount'])){
						$model->setData('vendor_adjustment_amount',$data['vendor_adjustment_amount']);
					}
	            }
            }
           
			if(isset($data['is_transfered'])) {
				$model->setData('is_transfered',$data['is_transfered']);
			}

			try {
				$model->setData('status',$data['status']);
				$model->save();
				$this->messageManager->addSuccess(__('Updated Successfully'));
				$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					return  $resultRedirect->setPath('*/*/edit', ['id' => $model->getRmaRequestId(), '_current' => true]);
				}
				return $resultRedirect->setPath('*/*/');
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
            	$this->messageManager->addException($e, __('Something went wrong while saving the page.'));
        	}	
        	
        	$this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('rma_request_id')]);
		}
		return $resultRedirect->setPath('*/*/');
	}
}



