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

class Post extends \Magento\Framework\App\Action\Action
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
    protected $_messageManager;
    protected $urlBuilder;

    
     public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $giftCollectionFactory
    ) { 
        $this->_resultPageFactory  = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_customerSession = $customerSession;
        $this->_giftCollectionFactory = $giftCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $gift_id = $this->getRequest()->getParam('gift_id'); 
        $postValues=$this->getRequest()->getPostValue();
        $customerId=$this->_customerSession->getCustomerId();
        $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerId);
        $gift = $this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'guest_email',
                $customer['email']
            )->addFieldToFilter(
                'groupgift_id',
                $gift_id
            )->getFirstItem();
                
       try{
          $gift->setData('quantity',$postValues['qty']);
          $gift->setData('request_approval',3);
          $gift->save();
        }catch(\Exception $e){echo $e->getMessage();die('in post value saved');}

        $this->messageManager->addSuccessMessage(__('Request Updated Successfully'));
        $this->_redirect('groupbuying/account/request');
    }    

}
