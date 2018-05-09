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
namespace Ced\CsRma\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
    * @var \Magento\Framework\ObjectManagerInterface
    */
    protected $objectManager;

    # Return Merchandise Authorization Section

    # GENERAL SETTINGS TAB
    #
    const CSRMA_ACTIVATION_XML_PATH = 'ced_csmarketplace/rma_general_group/activation';
    #
    const CSRMA_ITEM_ACTIVATE_XML_PATH = 'ced_csmarketplace/rma_general_group/item_activate';
    #
    const CSRMA_ALLOW_GUESTS_XML_PATH = 'ced_csmarketplace/rma_general_group/guest';
    #
    const CSRMA_MIN_DAYS_XML_PATH = 'ced_csmarketplace/rma_general_group/time';
    #
    const CSRMA_ALLOW_PRINTLABEL_XML_PATH = 'ced_csmarketplace/rma_general_group/print';
    # 
    const CSRMA_FRONT_LABEL_XML_PATH = 'ced_csmarketplace/rma_general_group/rma_frontend';
    #
    const CSRMA_ALLOW_REASONS_XML_PATH = 'ced_csmarketplace/rma_general_group/reason';

    #

    # MAIL CONTACTS TAB
    #
    const CSRMA_ALLOW_EMAIL_NOTIFICATIONS_XML_PATH = 'ced_csmarketplace/rma_general_group/email_notification';
    #
    const CSRMA_DEPNAME_XML_PATH = 'ced_csmarketplace/rma_general_group/dept_name';
    #
    const CSRMA_DEPEMAIL_XML_PATH = 'ced_csmarketplace/rma_general_group/dept_email';
    #
    const CSRMA_DEPADDRESS_XML_PATH = 'ced_csmarketplace/rma_general_group/dept_address';
    #
    const CSRMA_MAILSENDER_XML_PATH = 'ced_csmarketplace/rma_general_group/sender';
    #
    const CSRMA_CUSTOMER_MAIL_TEMPLATE_XML_PATH = 'ced_csmarketplace/rma_general_group/customer_mail_template';
    #
    const CSRMA_ADMIN_MAIL_TEMPLATE_XML_PATH = 'ced_csmarketplace/rma_general_group/admin_mail_template';
    #
    const CSRMA_VENDOR_MAIL_TEMPLATE_XML_PATH  = 'ced_csmarketplace/rma_general_group/vendor_mail_template';
    #
    const CSRMA_BASE_EMAIL_CHAT_CUSTOMER_XML_PATH = 'ced_csmarketplace/rma_general_group/base_email_chat_customer';
    #
    const CSRMA_BASE_EMAIL_CHAT_ADMIN_XML_PATH   = 'ced_csmarketplace/rma_general_group/base_email_chat_admin';
    #
    const CSRMA_BASE_EMAIL_CHAT_VENDOR_XML_PATH   = 'ced_csmarketplace/rma_general_group/base_email_chat_vendor';
    #

    # CONTACTS TAB
    #
    const CSRMA_ALLOW_CHATS_XML_PATH = 'ced_csmarketplace/rma_general_group/enable_email_notification';
    # 
    const CSRMA_DEPCHAT_NAME_XML_PATH = 'ced_csmarketplace/rma_general_group/chat_sender_name';
    # 
    const CST_ADMIN_EMAIL_SENDER_XML_PATH = 'ced_csmarketplace/rma_general_group/base_email_chat_customer';
    # 
    const CST_CUST_EMAIL_SENDER_XML_PATH = 'ced_csmarketplace/rma_general_group/base_email_chat_customer';
    # 
    const COPY_METHOD_XML_PATH = 'ced_csmarketplace/rma_general_group/copy_method';

    # RMA POLICY TAB

    const CSRMA_POLICY_XML_PATH = 'ced_csmarketplace/rma_general_group/policy_description';
    #
    const CSRMA_RETADDRESS_XML_PATH = 'ced_csmarketplace/rma_general_group/return_address';

    # PROPERTIES TAB
    # 
    const CSRMA_REASONS_XML_PATH = 'ced_csmarketplace/rma_general_group/reasons';
    # 
    const CSRMA_PKGCONDITIONS_XML_PATH = 'ced_csmarketplace/rma_general_group/conditions';
    # 
    const CSRMA_RESOLUTIONS_XML_PATH = 'ced_csmarketplace/rma_general_group/resolution';

     # ORDER DETAILS  TAB
    # 
    const CSRMA_ORDER_CANCEL_XML_PATH = 'ced_csmarketplace/rma_general_group/cancel_order';
    # 
    const CSRMA_PRODUCT_SHIPPED_XML_PATH = 'ced_csmarketplace/rma_general_group/product_shipped';
    # 
    const CSRMA_PRODUCT_INVOICE_XML_PATH = 'ced_csmarketplace/rma_general_group/product_invoiced';


    # GENERAL SETTINGS TAB

   /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $salesResourceModelOrderCollectionFactory;

    /**
     * @var \Ced\Rma\Model\RequestFactory
     */
    protected $rmaRequestFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesResourceModelOrderCollectionFactory
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager, 
       \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesResourceModelOrderCollectionFactory
    ) {
        $this->objectManager = $objectManager;
        $this->salesResourceModelOrderCollectionFactory = $salesResourceModelOrderCollectionFactory;
        parent::__construct($context
        );
    }

    /**
     * enable module
     *
     * @param store value
     * @return boolean
     */

    public function enableModule($store = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CSRMA_ACTIVATION_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * get allow guest value
     *
     * @param store value
     * @return boolean
     */

    public function getAllowGuests($store = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CSRMA_ALLOW_GUESTS_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * get min days valid for rma 
     *
     * @param store value
     * @return boolean
     */

    public function getMinDaysAfter($store = null)
    {
        return intval($this->scopeConfig->getValue(self::CSRMA_MIN_DAYS_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store));
    }

    /**
     * getAllowPrintLabel
     *
     * @param store value
     * @return boolean
     */

    public function getAllowPrintLabel($store = null)
    {
        return (bool)($this->scopeConfig->getValue(self::CSRMA_ALLOW_PRINTLABEL_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store));
    }
    
    
    # MAIL CONTACTS TAB

    /**
     * getAllowSendingMail
     *
     * @param store value
     * @return boolean
     */

    public function getAllowSendingMail($store = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CSRMA_ALLOW_EMAIL_NOTIFICATIONS_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }    

    /**
     * getDepartmentDisplayName
     *
     * @param store value
     * @return boolean
     */

    public function getDepartmentDisplayName($store = null)
    {
        return $this->scopeConfig->getValue(self::CSRMA_DEPNAME_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * getDepartmentEmail
     *
     * @param store value
     * @return boolean
     */

    public function getDepartmentEmail($store = null)
    {
        return $this->scopeConfig->getValue(self::CSRMA_DEPEMAIL_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * getDepartmentDisplayAddress
     *
     * @param store value
     * @return boolean
     */

    public function getDepartmentDisplayAddress($store = null)
    {
        return $this->scopeConfig->getValue(self::CSRMA_DEPADDRESS_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }   

    /**
     * getEmailSender
     *
     * @param store value
     * @return boolean
     */

    public function getEmailSender($store = null)
    {
        return $this->scopeConfig->getValue(self::CSRMA_MAILSENDER_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    
    # CHATS TAB

    /**
     * getAllowChats
     *
     * @param store value
     * @return boolean
     */

    public function getAllowChats($store = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CSRMA_ALLOW_CHATS_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * getDepartmentChatName
     *
     * @param store value
     * @return boolean
     */

    public function getDepartmentChatName($store = null)
    {
        return $this->scopeConfig->getValue(self::CSRMA_DEPCHAT_NAME_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    # RMA POLICY TAB
    /**
     * getPolicyText
     *
     * @param store value
     * @return boolean
     */

    public  function getPolicyText($store = null)
    {
        return $this->scopeConfig->getValue(self::CSRMA_POLICY_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * getDepartmentEmail
     *
     * @param store value
     * @return boolean
     */

    public function isProductShippedEnabled($store = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CSRMA_PRODUCT_SHIPPED_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    public function isOrderCanceledEnabled($store = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CSRMA_ORDER_CANCEL_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /*
     * Retrieve if RMA Online Refund is enabled
     *
     * @return bool
     */
    public function isProductInvoicedEnabled($store = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CSRMA_PRODUCT_INVOICE_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * isRmaPermitted
     *
     * @param order
     * @return boolean
     */

    public function isRmaPermitted($order)
    {
        if ($order->getState() == 'complete') {
            $orderInvoices = $order->getInvoiceCollection();
            $lastInvoiceTime = 0;
            foreach ($orderInvoices as $orderInvoice) {
                $orderInvoiceTime = strtotime($orderInvoice->getCreatedAt());
                if ($orderInvoiceTime > $lastInvoiceTime) {
                    $lastInvoiceTime = $orderInvoiceTime;
                }
            }
            $timeStr = '-' . self::getMinDaysAfter() . ' day';
            if ($lastInvoiceTime && $lastInvoiceTime >= strtotime($timeStr, time())) {
                return true;
            }
        }
        return false;
    }

    /*public function getRmaLabel()
    {
        if ($this->request->getParam('order_id')) {
            $order_selected = $this->request->getParam('order_id');
            $_ordersPlaced = $this->salesResourceModelOrderCollectionFactory->create()
                    ->addFieldToFilter('state', array('in' => array('complete')))
                    ->addFieldToFilter('entity_id', $order_selected);
            $_ordersPlaced->getSelect()
                    ->where('updated_at > DATE_SUB(NOW(), INTERVAL ? DAY)', $this->rmaConfigHelper->getMinDaysAfter());
            $_ordersPlaced->load();
            if (!empty($_ordersPlaced->getData())) {
                $_selectedOrderId = $_ordersPlaced->getData();
                if ($this->rmaOrderdetailsHelper->isValidOrder(isset($_selectedOrderId[0]['increment_id'])? $_selectedOrderId[0]['increment_id']:null))
                    return $this->rmaConfigHelper->getFrontLabel();
            }
        }
        return ;
    }*/
    
    /*
     * check if Order selected previously have an RMA
     *
     * @return bool
     */
    /*public function checkRmaForOrder($orderId)
    {
        $isOrder = $this->rmaRequestFactory->create()->load($orderId, 'order_id')->getData();
        if (empty($isOrder))
        {
            return false;
        }
        else
        {
            return true;
        }
        return false;
    }*/

    # Customize Returns Section

    /**
     * getAvailableReasons
     *
     * @param store
     * @return boolean
     */
      
    public  function getAvailableReasons($store = null)
    {
        $availableReason = [];
        $configValue = $this->scopeConfig->getValue(self::CSRMA_REASONS_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        if ($configValue) {
            $values = @unserialize($configValue);
            if (is_array($values)) {
                foreach($values as $key => $value) {
                    $availableReason[$value['reasons']] = $value['reasons'];
                }
            } 
        }
        asort($availableReason);
        return $availableReason;
    }
    # Customize Returns Section

    /**
     * getPackageCondition
     *
     * @param store
     * @return array of package condition
     */

    public  function getPackageCondition($store = null)
    {
        $packageCondition = [];
        $config_value = $this->scopeConfig->getValue(self::CSRMA_PKGCONDITIONS_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        if ($config_value) {
            $pk_values = @unserialize($config_value);
            if (is_array($pk_values)) {
                foreach($pk_values as $pkkey => $pk_value) {
                    $packageCondition[$pk_value['conditions']] = $pk_value['conditions'];
                }
            } 
        }
        asort($packageCondition);
        return $packageCondition;
    }

    /**
     * getPackageCondition
     *
     * @param store
     * @return array of resolution
     */

    public function getResolution($store = null)
    {
        $resolution = [];
        $config_value = $this->scopeConfig->getValue(self::CSRMA_RESOLUTIONS_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        if ($config_value) {
            $resolution_values = @unserialize($config_value);
            if (is_array($resolution_values)) {
                foreach($resolution_values as $resolutionkey => $resolution_value) {
                    $resolution[$resolution_value['resolution']] = $resolution_value['resolution'];
                }
            } 
        }
        asort($resolution);
        return $resolution;
    }

    public function getAvailableStatus()
    {
        $status = [];
        $statusModel =  $this->objectManager->create('Ced\CsRma\Model\Rmastatus')
                ->getCollection()
                ->setOrder('sortOrder','DESC');
        foreach($statusModel as $value) {
            $status[$value->getStatus()] = $value->getStatus();
        }
        return $status;
    }

    public function OrderFilterStatus()
    {
        $filter = [];
        $filter[] = 'Complete';
        if ($this->isOrderCanceledEnabled()) {
            //$filter[] = 'Pending';
            $filter[] = 'Processing';
        } elseif ($this->isProductShippedEnabled() || $this->isProductInvoicedEnabled()) {
            $filter[] = 'Processing';
        }
        return $filter;
    }

    public function ProductFilterOutStatus()
    {
        $ps_filter = [];
        $ps_filter[] = 'Refunded';
        $ps_filter[] = 'Canceled';
        if (!$this->isOrderCanceledEnabled()) {
            $ps_filter[] = 'Ordered';
        }
        if (!$this->isProductShippedEnabled()) {
                $ps_filter[] = 'Shipped';
        }
        if (!$this->isProductInvoicedEnabled()) {
            $ps_filter[] = 'Invoiced';
        }
        return $ps_filter;
    }
    
    public function transferTovendor(){
    	return (bool)$this->scopeConfig->getValue("ced_csmarketplace/rma_general_group/transfertovendor");
    }
}
