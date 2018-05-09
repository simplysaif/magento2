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
* @package     Ced_CsMultistepreg
* @author      CedCommerce Core Team <connect@cedcommerce.com >
* @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
* @license     http://cedcommerce.com/license-agreement.txt
*/


namespace Ced\CsMultistepreg\Controller\Adminhtml\Attributes;
 
use Magento\Backend\App\Action\Context;
use Magento\Framdework\View\Result\PageFactory;
class SaveSteps extends \Magento\Backend\App\Action{
	protected $resultPageFactory;
	public function __construct(
			\Magento\Framework\App\Request\Http $request,
			\Magento\Backend\App\Action\Context $context
	) {
		parent::__construct($context);
	}
	
	public function execute(){
		$postData = $this->getRequest()->getPost();
		$steps = $postData['steps']['options']['label'];
		$count = 0;
		$stepsCollection = $this->_objectManager->create('Ced\CsMultistepreg\Model\Steps')->getCollection();
		$stepsCollection->walk('delete');
		try{
			if(!$steps){
				$this->messageManager->addSuccessMessage(__('Action Performed Successfully'));
				return $this->_redirect('*/*/newStep');
			}
			foreach ($steps as $step){
				$stepsModel = $this->_objectManager->create('Ced\CsMultistepreg\Model\Steps');
				$stepsModel->setStepNumber($step['step_number']);
				$stepsModel->setStepLabel($step['step_label']);
				$stepsModel->save();
				$count++;
			}
			$this->messageManager->addSuccessMessage(__($count.' Steps Has Been Saved.'));
			return $this->_redirect('*/*/newStep');
		}catch (Exception $e){
			$this->messageManager->addSuccessMessage(__($e->getMessage()));
			return $this->_redirect('*/*/newStep');
		}
		
	}
	
	protected function _isAllowed(){
		return true;
	}
}

