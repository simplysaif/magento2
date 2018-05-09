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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Send extends \Ced\CsMarketplace\Controller\Vendor
{


    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Date filter instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    protected $_messageManager;

    protected $_transportBuilder;

    protected $_storeManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        Context $context, 
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        Registry $coreRegistry, 
        Date $dateFilter,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->_coreRegistry = $coreRegistry;
        $this->_dateFilter = $dateFilter;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $vendorId = $this->_getSession()->getVendorId();
        if(!$vendorId) {
            return;
        }
        
        if ($this->getRequest()->getPostValue()) {
                $emails = $this->getRequest()->getPost('email');
                $messages = $this->getRequest()->getPost('msg');
                $success = 0;
                $fail = 0;
                $count = count($emails);
                for($i=0; $i < count($emails); $i++ )
                {
                    $cid = $emails[$i];
                    $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->loadByEmail($cid);
                    $subVendor = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($cid,'email')->getData();
                    if($vendor || count($subVendor)){
                        $fail = $fail +1;
                        continue;
                    }
                    $savedRequests = $this->_objectManager->create('Ced\CsSubAccount\Model\AccountStatus')->getCollection()->addFieldToFilter('parent_vendor',$vendorId)
                                        ->addFieldToFilter('email',$cid)->getData();
                    if((count($savedRequests) && $savedRequests[0]['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_PENDING) || (count($savedRequests) && $savedRequests[0]['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_ACCEPTED))
                    {
                        $fail = $fail +1;
                        continue;
                    }        
                    $data = array();
                    $data['parent_vendor'] =  $vendorId;
                    $data['email'] = $cid;
                    $data['status'] = \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_PENDING;
                    $model = $this->_objectManager->create('Ced\CsSubAccount\Model\AccountStatus')->setData($data);
                    $model->save();
                    $emailTemplate = 'ced_cssubaccount_request_template';
                   // $emailTemplate = 'ced_csmarketplace_vendor_account_rejected_template';
                    $vendor_data = $this->_getSession()->getVendor();
                    $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->setWebsiteId($this->_storeManager->getWebsite()->getId());
                    $name = $customer->loadByEmail($emails[$i])->getName();
                    
                    $emailTemplateVariables = array();
                    $emailTemplateVariables['vname'] = $vendor_data['public_name'];
                    $emailTemplateVariables['vid'] = $vendorId;
                    $emailTemplateVariables['cid'] = $model->getId();
                    //$emailTemplateVariables['cname'] = $name;
                    if(isset($vendor_data['company_address']))
                      $emailTemplateVariables['company_address'] = $vendor_data['company_address'];
                    $emailTemplateVariables['message'] = $messages[$i];
                    //$emailTemplateVariables['created_at'] = $created_at;
                    //$emailTemplate->setSenderName($vendor_data['public_name']);
                    //$emailTemplate->setSenderEmail($vendor_data['email']);
                    $sender = [
                      'name' => $vendor_data['public_name'],
                      'email' => $vendor_data['email'],
                      ];
                      //print_r($emails[$i]);die;
                      $transport = $this->_transportBuilder->setTemplateIdentifier($emailTemplate)
                          ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                          ->setTemplateVars($emailTemplateVariables)
                          ->setFrom($sender)
                          ->addTo($emails[$i])
                          ->getTransport();
                      

                    //$emailTemplate->setType('html');
                    //$emailTemplate->setTemplateSubject('New Request For Sub-Vendor');

                    //print_r($emailTemplateVariables);die;
                    try
                    {
                        $transport->sendMessage();
                        //$emailTemplate->send($emails[$i], $model->getFirstName(), $emailTemplateVariables);
                        $success = $success + 1;
                    }
                    catch(Exception $e)
                    {
                        $errorMessage = $e->getMessage();
                        $this->messageManager->addError(__($errorMessage));
                        $this->_redirect('cssubaccount/customer/index/');
                        return;
                    }
                } 
                //$this->getLayout()->getBlock('head')->setTitle($this->__('Manage Customers'));
                if($fail)
                    $this->messageManager->addError(__($fail.' Request has not been sent.'));
                if($success)
                    $this->messageManager->addSuccess(__($success.' Request has been successfully sent.'));
                
                $this->_redirect('cssubaccount/customer/index/');
                return; 
                }      
      
    }

    
}
