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
namespace Ced\CsRma\Controller\Adminhtml\Status;

/**
 * @var Magento\Backend\App\Action
 */
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action

{
	/**
    * @var \Ced\Rma\Model\RmastatusFactory
    */

    protected $rmaStatusFactory;

	/**
	 * @var \Magento\Backend\Model\View\Result\Forward
	 */

	protected $dateTime;

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect
     */
    protected $resultRedirectFactory;
    
    /**
     * @param \Ced\Rma\Model\RmastatusFactory $rmaStatusFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */

    public function __construct(
    	\Ced\CsRma\Model\RmastatusFactory $rmaStatusFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
        )
    {
    	$this->rmaStatusFactory = $rmaStatusFactory;
        $this->resultRedirectFactory=$resultRedirectFactory;
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

	/**
	 * @var execute
	 */

	public function execute()
    {
    	$data = $this->getRequest()->getParams();
    	$resultRedirect = $this->resultRedirectFactory->create();
    	$model = $this->rmaStatusFactory->create();
        if ($data) 
        {
			if($id = $this->getRequest()->getParam('status_id')) {	
				$model->load($id);
			}
			$model->setData($data);
			try{
				$model->save();
				$this->messageManager->addSuccess(__('Updated Successfully'));
				$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					return  $resultRedirect->setPath('*/*/edit', ['id' => $model->getStatusId(), '_current' => true]);
				}
				return $resultRedirect->setPath('*/*/');
			} catch (LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
				echo $e->getMessage();
            	$this->messageManager->addException($e, __('Something went wrong while saving the page.'));
        	}	
        	$this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('status_id')]);
		}
		return $resultRedirect->setPath('*/*/');
	}
}



