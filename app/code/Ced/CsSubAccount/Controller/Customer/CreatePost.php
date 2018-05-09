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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Customer; 

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
 
class CreatePost extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        Context $context, 
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        
    }

    public function execute()
    {  
        $parentVendor = $this->_customerSession->getParentVendor();
        $requestId = $this->_customerSession->getRequestId();
        $this->_customerSession->unsParentVendor();
        $this->_customerSession->unsRequestId(); 
        try{
            if(!$parentVendor || !$requestId){
                $this->messageManager->addError(__('Kindly click on the accept link from mail sent by seller.'));
                $this->_redirect('cssubaccount/customer/create/');
                return;
            }
            if (!$this->getRequest()->isPost()) {
                $this->_redirect('cssubaccount/customer/create/');
                return;
            }
            $post = $this->getRequest()->getPost();
            $password = $post['password'];
            $email = $post['email'];
            $vendor_coll = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->loadByEmail($email);
            $subvendor_coll = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($email,'email');
            $request_coll = $this->_objectManager->create('Ced\CsSubAccount\Model\AccountStatus')->getCollection()->addFieldToFilter('email',$email);
            if(!count($request_coll->getData())){ 
                $this->messageManager->addError(__('Seller has not sent any request to '.$email.' mail Id.'));
                $this->_redirect('cssubaccount/customer/create/');
                return;
            }
            if(($vendor_coll) || !empty($subvendor_coll->getData())){ 
                $this->messageManager->addError(__($email.' Mail id already exist.'));
                $this->_redirect('csmarketplace/account/login/');
                return;
            }
            $data = array();
            $data['parent_vendor'] = $parentVendor;
            $data['first_name'] = $post['firstname'];
            $data['last_name'] = $post['lastname'];
            $data['middle_name'] = $post['middlename'];
            $data['email'] = $post['email'];
            $data['role'] = 'all';
            $data['password'] = $this->_objectManager->get('Magento\Framework\Encryption\Encryptor')->encrypt($post['password']);
            $data['status'] = 0;
            $model = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->setData($data);
            $model->save();
            $this->_customerSession->setParentVendor($parentVendor);
            $this->_customerSession->setSubvendorEmail($post['email']);
            //print_r($this->_customerSession->getSubvendorEmail());die('----');

            $requestModel = $this->_objectManager->create('Ced\CsSubAccount\Model\AccountStatus')->load($requestId);
            $requestModel->setStatus(\Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_ACCEPTED);
            $requestModel->save();
        }
        catch(\Exception $e){
            $this->messageManager->addError(__($e->getMessage()));
            $this->_redirect('cssubaccount/customer/create/');
            return;
        }
        $this->messageManager->addSuccess(__('You have successfully registered for sub vendor.'));
        $this->_redirect('cssubaccount/vendor/approval');
        return;
    }
}
 
