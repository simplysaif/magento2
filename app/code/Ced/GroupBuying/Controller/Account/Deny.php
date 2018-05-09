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
 * @category  Ced
 * @package   Ced_GroupBuying
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\GroupBuying\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class Deny extends Action
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    protected $resultPageFactory;

    protected $_request;
    protected $_response;
    protected $_objectManager;
    protected $_coreRegistry = null;
    protected $_scopeConfig;
    
     public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $giftCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) { 
        $this->resultPageFactory  = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */

        $param=$this->getRequest()->getParam('gid');
        $customerId=$this->_customerSession->getCustomerId();
        $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerId);
        $data=$this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'guest_email',
                $customer['email']
            )->addFieldToFilter(
                'groupgift_id',
                $param
            )->getFirstItem();
        
        try{
          $data->setData('request_approval',1);
          $data->save();
        }catch(\Exception $e){echo $e->getMessage();die('new one');}
        $resultPage = $this->resultPageFactory->create();
        $this->messageManager->addNoticeMessage(__('Request Denied Successfully'));
        $this->_redirect('groupbuying/account/request');
        return $resultPage;
        
    }
}
