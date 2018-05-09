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
 * @package     Ced_RequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\RequestToQuote\Block\Adminhtml\Quotes\Edit\Tab;
 
use Magento\Framework\Data\Form\Element\Label;

/**
 * Blog post edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
 
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
 
    protected $_status;
 
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Ced\RequestToQuote\Model\Quote $quote,
        \Magento\Customer\Model\Customer $customer,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->quote = $quote;
        $this->_customer = $customer;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $quote_id = $this->_requestInterface->getParam('id');
        $quoteData = $this->quote->load($quote_id);

        $quotePrice = $quoteData->getQuoteTotalPrice();
        $quoteupdPrice = $quoteData->getQuoteUpdatedPrice();
        $quoteQty = $quoteData->getQuoteTotalQty();
        $quoteupdQty = $quoteData->getQuoteUpdatedQty();

        $customerEmail = $quoteData->getCustomerEmail();
        $customerid = $quoteData->getCustomerId();
        
        $shippingMethod = $quoteData->getShipmentMethod();
        $shipamt = $quoteData->getShippingAmount();
        $customerName = $this->getName($customerid);
        $quoteData->setData('customer_name', $customerName);
        $isElementDisabled = false;
 
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
 
        $form->setHtmlIdPrefix('page_');
 
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Quote Details')]);
        
        $fieldset->addField('quote_id', 'hidden', ['name' => 'id', 'value' =>$quote_id]);

        $fieldset->addField ( 'customer_name', 'text', [ 
                'name' => 'customer_name',
                'label' => __ ( 'Customer Name' ),
                'title' => __ ( 'Customer Name' ),
                'required' => true,
                'disabled' => true 
        ] );
        
        $fieldset->addField ( 'customer_email', 'text', [ 
                'name' => 'customer_email',
                'label' => __ ( 'Customer Email' ),
                'title' => __ ( 'Customer Email' ),
                //'value' => $customerEmail,
                'required' => true,
                'disabled' => $isElementDisabled 
        ] );

        $fieldset->addField ( 'quote_total_price', 'text', [ 
                'name' => 'quote_total_price',
                'label' => __ ( 'Quoted Price' ),
                'title' => __ ( 'Quote Price' ),
                'required' => true,
                //'value' => $this->getCurrencyCode().' '.$quotePrice,
                'disabled' => true
        ] );

        $fieldset->addField ( 'quote_updated_price', 'text', [ 
                'name' => 'quote_updated_price',
                'label' => __ ( 'Update Price' ),
                'title' => __ ( 'Update Price' ),
                'required' => true,
                //'value' => $this->getCurrencyCode().' '.$quoteupdPrice,
                'disabled' => $isElementDisabled 
        ] );

        $fieldset->addField ( 'quote_total_qty', 'text', [ 
                'name' => 'quote_total_qty',
                'label' => __ ( 'Quote Quantity' ),
                'title' => __ ( 'Quote Quantity' ),
                //'value' => $quoteQty,
                'required' => true,
                'disabled' => true
        ] );

        $fieldset->addField ( 'quote_updated_qty', 'text', [ 
                'name' => 'quote_updated_qty',
                'label' => __ ( 'Update Quantity' ),
                'title' => __ ( 'Update Quantity' ),
                'required' => true,
                //'value' => $quoteupdQty,
                'disabled' => $isElementDisabled 
        ] );


        $fieldset->addField ( 'shipment_method', 'text', [
            'name' => 'shipment_method',
            'label' => __ ( 'Shipping Method' ),
            'title' => __ ( 'Shipping Method' ),
            'required' => true,
            //'value' => $shippingMethod,
            'disabled' => $isElementDisabled
        ] );

        $fieldset->addField ( 'shipping_amount', 'text', [
            'name' => 'shipping_amount',
            'label' => __ ( 'Shipping Amount' ),
            'title' => __ ( 'Shipping Amount' ),
            'required' => true,
            //'value' => $this->getCurrencyCode().' '.$shipamt,
            'disabled' => $isElementDisabled
        ] );

       $fieldset->addField ( 'status', 'select', [ 
                'name' => 'status',
                'label' => __ ( 'Status' ),
                'title' => __ ( 'Status' ),
                'required' => true,
                'values' => array (
                        '0' => 'Pending',
                        '1' => 'Approved',
                        '2' => 'Cancelled',
                        '3' => 'PO created',
                        '4' => 'Partial Po',
                        '5' => 'Ordered',
                        '6' => 'Complete'
                )
        ] );
        $form->setValues($quoteData->getData());
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
 
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Quote Information');
    }
 
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Quote Information');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
 
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getName($customer_id)
    {
        $firstName = $this->_customer->load($customer_id)->getFirstname();
        $lastName = $this->_customer->load($customer_id)->getLastname();
        $name = $firstName.' '.$lastName;

        return $name;
    }

    public function getCurrencyCode(){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $code =  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code);
        return $currency->getCurrencySymbol();
    }
}