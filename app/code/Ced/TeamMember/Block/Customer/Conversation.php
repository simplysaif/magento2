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
namespace Ced\TeamMember\Block\Customer;

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
        \Ced\TeamMember\Model\Session $teammemberSession,
        
        array $data = []
    ) {
        $this->session = $teammemberSession;
        $this->_objectManager = $objectInterface;
        parent::__construct($context,$data);
    }
   
  public function getCustomerConversation()
  {  
  	return $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage')->getCollection();//->addFieldToFilter('sender','teammember')->addFieldToFilter('sender_email',$this->session->getTeamMemberDataAsLoggedIn()->getEmail());
  }
  public function getVendorConversation()
  {
    return $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage')->getCollection()->addFieldToFilter('receiver','vendor')->addFieldToFilter('receiver_email',$this->getRequest->getParam('vendoremail'));
  }

  public function getSenderToken()
  {  
    $token_s=$this->session->getTeamMemberDataAsLoggedIn()->getEmail()."-".$this->getRequest()->getParam('customeremail');
    return $token_s;
  }

   public function getRecevierToken()
  {  
    $token_r = $this->getRequest()->getParam('customeremail')."-".$this->session->getTeamMemberDataAsLoggedIn()->getEmail();
    return $token_r;
  }
  
}
