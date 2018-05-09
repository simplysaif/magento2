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
 
namespace Ced\GroupBuying\Controller\Registry;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class Submit extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\Registry $registry
    ) {
        $this->_resultPageFactory  = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams('data');
        $group_partial = $this->_objectManager->create('Ced\GroupBuying\Model\Guest');
        $prod_model = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $original_product = $prod_model->load($data['original_product_id']);
        
        if($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            $customerData = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomer();
            $customer_id = $customerData->getId();
        }else{

            $this->_messageManager->addErrorMessage(__('You Should be logged in to complete this action'));
            exit ;
        }

        if(!$data['gift_receiver_email']){
            $data['gift_receiver_email'] = $customerData->getEmail();
        }
        $group_main = $this->_objectManager->create('Ced\GroupBuying\Model\Main');     
        try{    
            // Set the Group information
            
            $group_main ->setData('owner_customer_id', $customer_id)
                        ->setData('show_contribution_to_guest', $data['show_contribution_to_guest'])
                        ->setData('original_product_id', $data['original_product_id'])
                        ->setData('price', $data['price'])
                        ->setData('receiver_name', $data['receiver_name'])
                        ->setData('gift_receiver_email', $data['gift_receiver_email'])
                        ->setData('gift_msg', $data['gift_msg'])
                        ->setData('created_at',date("Y-m-d H:i:s"))
                        ->setData('status', 0)
                        ->save(); 
         $group_partial->setData('groupgift_id', $group_main->getId())
                       ->setData('guest_name',$data['receiver_name'])
                       ->setData('guest_email',$data['gift_receiver_email'])
                       ->save();                                   
        }
        catch(\Exception $ex) {
        
            $this->_messageManager->addErrorMessage(__($e->getMessage()));
            exit ;
        }

        $mailList = array();

        // Invite the Friends for the group buying
        
        foreach($data['uname'] as $key => $value){
            $mail = trim($data['email'][$key]); 
            
            if(!in_array($mail,$mailList) && $mail){
            
                $mailList[] = $mail;
                $group_guest = $this->_objectManager->create('Ced\GroupBuying\Model\Guest');
                $group_guest->setData('groupgift_id', $group_main->getId())
                            ->setData('guest_name', $value)
                            ->setData('guest_email', $data['email'][$key])
                            ->save();                       
                
                //$this->_objectManager->get('Ced\Groupgift\Helper\Data')->sendInvitationMail($group_main,$mail,$name); 
                                
            }           
        }

        // if(isset($data['notify_customer'])){
        //      //$this->_objectManager->get('Ced\Groupgift\Helper\Data')->sendNotificationMail($group_main);   
        // } 
       $this->messageManager->addSuccess(__('Your Data Submitted Successfully !'));
       return 0; 
    }
}
