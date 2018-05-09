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
 * @package   Ced_RequestToQuote
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\RequestToQuote\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_objectManager = null;
    protected $_inlineTranslation;

    protected $_transportBuilder;
    protected $_storeManager;
    protected $_customerSession;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Ced\RequestToQuote\Model\Mailattachement $transportBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_customerSession = $customerSession;
        $this->date = $date;
        $this->_transportBuilder = $transportBuilder;
        parent::__construct($context);
    }


    public function isEnable() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $configvalue = $objectManager->get ( '\Magento\Framework\App\Config\ScopeConfigInterface' );
        $value = $configvalue->getValue ( 'requesttoquote_configuration/active/enable' );
        return $value;
    }

    public function sendPoCreatedMail($customer_id, $quote_id, $po_id, $link, $po_qty, $po_price, $cancel,$filepath)
    {   $support=$this->_objectManager->get ( '\Magento\Framework\App\Config\ScopeConfigInterface' )->getValue('trans_email/ident_support/email');
        $modeldata = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customer_id);
        $email = $modeldata->getEmail();
        $emailvariables['customername'] = $modeldata->getFirstname();
        $emailvariables['po_id'] = $po_id;
        $emailvariables['quote_id'] = $quote_id;
        $emailvariables['link'] = $link;
        $emailvariables['po_qty'] = $po_qty;
        $emailvariables['po_price'] = $po_price;
        $emailvariables['cancel_link'] = $cancel;
        $emailvariables['subject'] = "A new PO has been created";

        $this->_template = 'ced_requesttoquote_customer_po_creation';
        $this->_inlineTranslation->suspend();


        $senderInfo = [
            'name' => 'SUPPORT',
            'email' => $support,
        ];

        try {

            $this->_transportBuilder->setTemplateIdentifier('ced_requesttoquote_customer_po_creation')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailvariables)
                ->setFrom($senderInfo)
                ->addTo($email, $emailvariables['customername'])
                ->addAttachment(file_get_contents($filepath));
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();


        }catch (\Exception $e) {
            return __( 'Unable to send mail.' );
        }
        $this->_inlineTranslation->resume();
        return __( 'Mail has been sent.' );
    }

    public function sendQuoteCreatedMail($customer_id, $customer_mail, $quote_id, $link)
    {   $support=$this->_objectManager->get ( '\Magento\Framework\App\Config\ScopeConfigInterface' )->getValue('trans_email/ident_support/email');
        $emailvariables['customername'] = $modeldata->getFirstname();
        $emailvariables['quote_id'] = $quote_id;
        $emailvariables['link'] = $link;
        $emailvariables['subject'] = "A new Quote has been created by you.";

        $this->_template = 'ced_requesttoquote_customer_quote_creation';
        $this->_inlineTranslation->suspend();


        $senderInfo = [
            'name' => 'SUPPORT',
            'email' => $support,
        ];

        try {

            $this->_transportBuilder->setTemplateIdentifier('ced_requesttoquote_customer_quote_creation')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailvariables)
                ->setFrom($senderInfo)
                ->addTo($customer_mail, $emailvariables['customername']);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();


        }catch (\Exception $e) {
            return __( 'Unable to send mail.' );
        }
        $this->_inlineTranslation->resume();
        return __( 'Mail has been sent.' );
    }


    public function generatePromoCode()
    {
        $length=6;
        $rndId = md5(uniqid(rand(),1));
        $rndId = strip_tags(stripslashes($rndId));
        $rndId = str_replace(array(".", "$"),"",$rndId);
        $rndId = strrev(str_replace("/","",$rndId));
        if (!is_null($rndId)){
            return strtoupper(substr($rndId, 0, $length));
        }
        return strtoupper($rndId);
    }


    //api requirement
    public function getCustomerSummary($customer_id){
        $amount=0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $AmountsModel = $objectManager->create ( 'Ced\ReferralSystem\Model\Transaction' )->getCollection ()->addFieldtoFilter ('customer_id', $customer_id)->getData();
        $referral_count=sizeof($AmountsModel);
        foreach($AmountsModel as $key=>$value){
            $amount+=$value['earned_amount'];
        }

        $data=json_encode(['success'=>"true", 'amount_earned'=>$amount, 'referral_count'=>$referral_count]);
        return $data;
    }

    public function sendEmail($template, $tempate_variables=array(), $reciever_email){
        $senderName = $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $name = $this->_objectManager->create('Magento\Customer\Model\Customer')->setWebsiteId(1)->loadByEmail($reciever_email)->getName();
        try{
          $sender = [
                      'name' => $senderName,
                      'email' => $senderEmail,
                      ];
                     // print_r($template);
                     // print_r($sender);
                     // print_r($reciever_email);
            $tempate_variables['name'] = $name;
            //print_r($tempate_variables);die;
          $transport = $this->_transportBuilder->setTemplateIdentifier($template)
              ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
              ->setTemplateVars($tempate_variables)
              ->setFrom($sender)
              ->addTo($reciever_email)
              ->getTransport();
          $transport->sendMessage();

      }
      catch(\Exception $e)
      {
        print_r($e->getMessage());
      }
    }
}