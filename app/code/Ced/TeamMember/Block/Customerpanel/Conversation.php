<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_Customermembership
  * @author       CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
namespace Ced\TeamMember\Block\Customerpanel;

/**
 * Customer address edit block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Conversation extends \Magento\Framework\View\Element\Template
{
    
    public $session;

    public $_objectManager;
   protected $_filtercollection;
   protected $_requestCollection;
   
    
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Customer\Model\Session $customerSession,
        
        array $data = []
    ) {
        $this->session = $customerSession;
        $this->_objectManager = $objectInterface;
        parent::__construct($context,$data);
    }
   
  public function getConversation()
  {   

  	return $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage')->getCollection();
  }

  public function getMemberConversation()
  {
    return $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage')->getCollection()->addFieldToFilter('receiver','customer')->addFieldToFilter('receiver_email',$this->getRequest->getParam('memberemail'));
  }


  public function getSenderToken()
  {  
    $token_s=$this->session->getCustomer()->getEmail()."-".$this->getRequest()->getParam('memberemail');
    return $token_s;
  }

   public function getRecevierToken()
  {  
    $token_r = $this->getRequest()->getParam('memberemail')."-".$this->session->getCustomer()->getEmail();
    return $token_r;
  }

  
}
