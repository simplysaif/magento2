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

class Chat extends \Ced\CsMarketplace\Controller\Vendor
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
    public function execute()
    {
        $postData = $this->getRequest()->getParams();

        $resultRedirect = $this->resultRedirectFactory->create();

        $dateTime = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        if ($id = $this->getRequest()->getParam('id')) {
            if (empty($postData['chat'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please enter message.'));
            }

            $rmaChat = $this->_objectManager->create('Ced\CsRma\Model\Rmachat');
            $file='';
            $fileIndex ='rma_file';
            if(!empty($_FILES[$fileIndex]['name']))
            {
              $file = $this->_objectManager->create('Ced\CsRma\Helper\Data')->getRmaImgUpload($postData);
            }
            $comment = trim(strip_tags($postData['chat']));
            $data = ['rma_request_id'=>$id,
                    'created_at'=>$dateTime->gmtDate(),
                    'chat_flow'=>\Ced\CsRma\Model\Request::OWNER_VENDOR,  //when send form vendor
                    'chat'=> $comment,
                    'vendor_id'=>$postData['vendor_id']
                ]; 
            $rmaChat->setData($data);
            if($file){
                $rmaChat->setData('file',$file);
            }
            
            try {
                $rmaChat->save();
                $this->messageManager->addSuccess(__('Message Sent Successfully'));
                $email = $this->prepareTemplateContent($comment,$id);
                return $resultRedirect->setPath('*/*/edit',['rma_id' => $this->getRequest()->getParam('id')]);

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];

            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot send message.')];
            }
        }
        return $resultRedirect->setPath('*/*/edit',['rma_id' => $this->getRequest()->getParam('id')]);
    }

    /**
     * prepare template content for email
     * @param execute
     */
    protected  function prepareTemplateContent($comment,$id)
    {

        $requestModel = $this->_objectManager->create('Ced\CsRma\Model\Request')->load($id);
        $store = $requestModel->getStoreId();
        $customer = $requestModel->getCustomerName();
        $email = $requestModel->getCustomerEmail();
        $this->_objectManager->create('Ced\CsRma\Helper\Email')->sendNotificationEmail($comment,$customer,$email,$store);

        
    }
}
