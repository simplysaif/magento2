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
 * @license     http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Helper;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{

  const T_CUSTOMER_BASE_EMAIL_TEMPLATE_XML_PATH = 'cedrma_section/rma_mail_group/customer_mail_template';
   /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $_objectManager;
 
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Ced\Rma\Helper\Config
    */     
    protected $rmaConfigHelper;

    /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Ced\Rma\Helper\Config $rmaConfigHelper
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Ced\CsRma\Helper\Config $rmaConfigHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
         \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->rmaConfigHelper = $rmaConfigHelper;
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
    }

   /**
     * Send status email  to customer
     *
     * @param  string $type
     * @param  string $backUrl
     * @param  string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendStatusEmail($message,$customer,$email,$storeId)
    {
        $sender = $this->rmaConfigHelper->getDepartmentChatName();
        $this->_sendEmailTemplate(
            $message, $sender,
            array('vendor' => $customer, 'email'=>$email), $storeId
        );
        return $this;
    }

    /**
     * Send corresponding email template
     *
     * @param  string   $emailTemplate  configuration path of email template
     * @param  string   $emailSender    configuration path of email identity
     * @param  array    $templateParams
     * @param  int|null $storeId
     * @return Mage_Customer_Model_Customer
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null)
    {
        
        /*reference file vendor\magento\module-sales\Model\Order\Email\SenderBuilder.php */
        /**
        * @var $mailer Mage_Core_Model_Email_Template_Mailer 
        */
        try{
            $templateId = 'admin_email_status_template';
            $vendor = $templateParams['vendor'];
            $email = $templateParams['email'];

            $transportBuilder = $this->_objectManager->create('Magento\Framework\Mail\Template\TransportBuilder');

            $transportBuilder->addTo($email, $vendor);

            $transportBuilder->setTemplateIdentifier($templateId);

            $transportBuilder->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_ADMIN,
                'store' => \Magento\Store\Model\Store::DISTRO_STORE_ID
                ]
            );
            $transportBuilder->setTemplateVars($templateParams);
            $sender = [
            'email' => $this->scopeConfig->getValue('ced_csmarketplace/rma_general_group/dept_email'),
            'name' =>$this->scopeConfig->getValue('trans_email/ident_support/name'),
            ];

            $transportBuilder->setFrom(['name'=>'djsf','email'=>'support@tilebathkitchen.com']);
            
            $transport = $transportBuilder->getTransport();

            $transport->sendMessage();
        }
        catch(\Exception $e) {
            return;
            echo $e->getMessage();
        }
        return $this;
    }

     /**
     * Send notification to customer
     *
     * @param  string $type
     * @param  string $backUrl
     * @param  string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNotificationEmail($message,$customer,$email,$storeId)
    {
        $sender = $this->rmaConfigHelper->getDepartmentChatName();
        $this->_sendEmailTemplate(
            $message, $sender,
            array('vendor' => $customer, 'email'=>$email), $storeId
        );
        return $this;
    }
    
    public function sendNewRmaMail($data,$customerEmail,$customerName,$rmaid,$url)
    {
    	
    	$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
    	try {
    		$error = false;
    		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    		$sender = [
    		'email' => $this->scopeConfig->getValue('ced_csmarketplace/rma_general_group/dept_email'),
    		'name' =>$this->scopeConfig->getValue('trans_email/ident_support/name'),
    		];
    	  
    		$transport = $this->_objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder')
    		->setTemplateIdentifier('customer_mail_template') // this code we have mentioned in the email_templates.xml
    		->setTemplateOptions(
    				[
    				'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
    				'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
    				]
    		)
    		
    		->setTemplateVars(['rmaid' => $rmaid,'name'=>$customerName,'url'=>$url])
    		->setFrom($sender)
    		->addTo($customerEmail)
    		->getTransport();
    		$transport->sendMessage();
    		$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
    		return;
    	} catch (\Exception $e) {
    	
    		
    		//$this->_redirect('*/*/');
    		return;
    	}
    }
    
    
    public function sendTransactional($mail,$description,$Vname,$Vemail)
    {
    	$senderEmail = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('trans_email/ident_general/email');
    	$senderName = "Admin";
    	 
    	$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
    	try {
    		$error = false;
    		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    		$Vsender = [
    		'name' => $senderName,
    		'email' =>$senderEmail,
    		];
    
    		 
    		$transport = $this->_objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder')
    		->setTemplateIdentifier('send_disapatch_email_template') // this code we have mentioned in the email_templates.xml
    		->setTemplateOptions(
    				[
    				'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
    				'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
    				]
    		)
    		->setTemplateVars(['answer' => $description,'name'=>$Vname])
    		->setFrom($Vsender)
    		->addTo($Vemail)
    		->getTransport();
    		 
    		$transport->sendMessage();
    		$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
    		return;
    	} catch (\Exception $e) {
    
    		$this->messageManager->addError(
    				__('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage())
    		);
    		$this->_redirect('*/*/');
    		return;
    	}
    }
    
    
}
