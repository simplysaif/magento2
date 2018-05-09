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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ced\CsRma\Model\RmachatFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Ced\CsRma\Helper\Data;
use Ced\CsRma\Helper\Email;
use Ced\CsRma\Model\RequestFactory;

class Chat extends \Magento\Backend\App\Action
{
    /**
    * @var Ced\CsRma\Model\RequestFactory
    */
    protected $rmaRequestFactory;

    /**
    * @var Ced\CsRma\Helper\Data
    */
    public $rmaDataHelper;

    /**
    * @var Ced\CsRma\Model\RmachatFactory
    */
    protected $rmaChatFactory;

    /**
    * @var Magento\Framework\View\Result\PageFactory
    */
    protected $resultPageFactory;
	
    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @param Context $context
     * @param Data $rmaDataHelper
     * @param RequestFactory $rmaRequestFactory
     * @param Email $rmaEmailHelper
     * @param RmachatFactory $rmaChatFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Data $rmaDataHelper,
        RequestFactory $rmaRequestFactory,
        Email $rmaEmailHelper,
        DateTime $dateTime,
        RmachatFactory $rmaChatFactory,
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->rmaRequestFactory = $rmaRequestFactory;
        $this->rmaEmailHelper = $rmaEmailHelper;
        $this->rmaDataHelper = $rmaDataHelper;
        $this->dateTime = $dateTime;
        $this->rmaChatFactory = $rmaChatFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
     /**
     * Add order comment action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
    	
        $postData = $this->getRequest()->getParams();
        if ($id = $this->getRequest()->getParam('id')) {
            if (empty($postData['comment'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a comment.'));
            }

            $rmaChat = $this->rmaChatFactory->create();
            $comment = trim(strip_tags($postData['comment']));
            $data = ['rma_request_id'=>$id,
                    'created_at'=>$this->dateTime->gmtDate(),
                    'chat_flow'=>\Ced\CsRma\Model\Request::OWNER_ADMIN,  //when send from admin
                    'chat'=> $comment
                ]; 
            $rmaChat->setData($data);
            $files = $this->getRequest()->getFiles()->toArray();
            $file ='';
            if (!empty($files['rma_file']['name']))
            {
              $file = $this->rmaDataHelper->getRmaImgUpload($postData);
            }
            if($file){
                $rmaChat->setData('file',$file);
            }
            
            try {

                $rmaChat->save();
                $this->messageManager->addSuccess(__('Message Sent Successfully'));
                $email = $this->prepareTemplateContent($comment,$id);
                return $this->resultRedirectFactory->create()->setPath('*/*/edit',['id' => $this->getRequest()->getParam('id')]);

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];

            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot send message.')];
            }
        }
        return $this->resultRedirectFactory->create()->setPath('csrma/*/');
    }
	
    /**
     * prepare template content for email
     * @param execute
     */
    protected  function prepareTemplateContent($comment,$id)
    {
        $requestModel = $this->rmaRequestFactory->create()->load($id);
        $store = $requestModel->getStoreId();
        $customer = $requestModel->getCustomerName();
        $email = $requestModel->getCustomerEmail();
        $this->rmaEmailHelper->sendNotificationEmail($comment,$customer,$email,$store);
    }
}