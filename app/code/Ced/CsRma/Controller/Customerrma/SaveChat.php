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

class SaveChat extends  \Ced\CsRma\Controller\Link
{
	/**
	 * @var \Magento\Framework\Data\Form\FormKey\Validator
	 */

	protected $_formKeyValidator;

	/**
	 * @var redirect
	 */

	protected $_redirect;

	/**
	 * @var \Magento\Customer\Model\Session 
	 */

	protected $customerSession;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */

	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\Controller\Result\ForwardFactory
	 */

	protected $resultForwardFactory;

	/**
	 * @var \Magento\Backend\Model\View\Result\Redirect
	 */

	protected $resultRedirectFactory;

    /**
    * @var \Ced\Rma\Model\RmachatFactory
    */

    protected $rmaChatFactory;


    /**
    * @var \Magento\Framework\Stdlib\DateTime\DateTime
    */
    protected $dateTime;

    /**
    * @var \Ced\Rma\Helper\Data
    */
    protected $rmaDataHelper;

	/**
	 * @param Magento\Framework\App\Action\Context 
	 * @param Magento\Framework\Session\Generic
	 * @param Magento\Customer\Model\Session
	 * @param Magento\Framework\Data\Form\FormKey\Validator
	 * @param Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 * @param Magento\Framework\View\Result\PageFactory
	 * @param Magento\Backend\Model\View\Result\Redirect
	 */

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsRma\Model\RmachatFactory $rmaChatFactory,
        \Ced\CsRma\Helper\Data $rmaDataHelper
	)

	{

		$this->rmaDataHelper = $rmaDataHelper;
        $this->rmaChatFactory = $rmaChatFactory;
        $this->dateTime = $dateTime;
		$this->resultPageFactory =$resultPageFactory;
		$this->_formKeyValidator = $formKeyValidator;
		$this->_redirect = $context->getRedirect();
		$this->customerSession = $customerSession;
		parent::__construct($context,$customerSession,$resultForwardFactory,$resultRedirectFactory,$resultPageFactory);

	}

	/**
	 * @param execute
	 * return redirect page
	 */

	public function execute()
	{
		if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        $chatModel = $this->rmaChatFactory->create();

        if ($this->getRequest()->getPost()) {

        	$postData = $this->getRequest()->getParams();
        	
        	$data = ['rma_request_id'=>$postData['id'],
                    'chat'=>$postData['chat'],
                    'chat_flow'=>\Ced\CsRma\Model\Request::OWNER_CUSTOMER, //for customer
                    'created_at'=>$this->dateTime->gmtDate()
                ];
        	$file='';
        	$fileIndex ='rma_file';
        	if(!empty($_FILES[$fileIndex]['name']))
        	{
	            $file = $this->rmaDataHelper->getRmaImgUpload($postData);
	           
        	}
        	$chatModel->setData($data);
            if($file){
            	$chatModel->setData('file',$file);
            }

            try {
    			$chatModel->save(); 
                $this->messageManager->addSuccess(__('Your message has been sent sucessfully.'));
              
                $url = $this->_buildUrl('*/*/view',['id'=>$chatModel->getRmaRequestId()]);
              
                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));

            } catch (\Exception $e) {
            	
                $redirectUrl = $this->_buildUrl('*/*/index');
                $this->messageManager->addException($e, __('We can\'t submit the message.'));
            }
        }          
	}

}
