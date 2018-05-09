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


namespace Ced\RequestToQuote\Controller\Cart;

class Getrate extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customersession
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->_customersession = $customersession;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (! $this->_customersession->isLoggedIn ()) {
            $this->messageManager->addError ( __ ( 'Please login first' ) );
            $this->_redirect ( 'customer/account/login' );
            return;
        }
        try {
        $customerData = $this->_customersession->getCustomer();
        $quoteData = $this->_customersession->getData('quoteData');
        $customer = $this->_objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface')
            ->getById($customerData->getId());
        $postData = $this->getRequest()->getPostValue();


        foreach ($quoteData as $value){
            $productid = $value['product_id'];
            $product=$this->_objectManager->create('Magento\Catalog\Model\Product')->load($productid);
            $productQty = $value['quote_qty'];
            $store_id = $value['store_id'];
            $quote = $this->_objectManager->create('Magento\Quote\Model\Quote')->setStoreId($store_id);
            $quote->assignCustomer($customer);
            $params = array(
                        'product'=> $productid,
                        'qty'=> $productQty,
            );

            $quote->addProduct($product,$productQty);


        }
        /*$billingAddress = $quote->getBillingAddress()->addData(array(
            'customer_address_id' => '',
            'prefix' => '',
            'firstname' => $customerData->getFirstName(),
            'middlename' => '',
            'lastname' =>$customerData->getLastName(),
            'suffix' => '',
            'company' =>'',
            'street' => array(
                '0' => $postData['address1'],
                '1' => $postData['address2'],
            ),
            'city' => $postData['city'],
            'country_id' => $postData['country_id'],
            'region' => $postData['state'],
            'postcode' => $postData['zipcode'],
            'telephone' => $postData['telephone'],
            'fax' => '',
            'vat_id' => '',
            'save_in_address_book' => 1
        ));*/
//print_r($postData['street']);die;
        $region = '';
        $region_id = '';
        if(isset($postData['region']))
            $region = $postData['region'];
        if(isset($postData['region_id']))
            $region_id = $postData['region_id'];

        $shippingAddress = $quote->getShippingAddress()->addData(array(
            'customer_address_id' => '',
            'prefix' => '',
            'firstname' => $customerData->getFirstName(),
            'middlename' => '',
            'lastname' =>$customerData->getLastName(),
            'suffix' => '',
            'company' =>'',
            'street' => array(
                '0' => $postData['street'],
                '1' => $postData['area']
            ),
            'city' => $postData['city'],
            'country_id' => $postData['country_id'],
            'region' => $region,
            'region_id' => $region_id,
            'postcode' => $postData['zipcode'],
            'telephone' => $postData['telephone'],
            'fax' => '',
            'vat_id' => '',
            'save_in_address_book' => 1
        ));
    	$productids=array(1);
    	$websiteId = 0;
    	$store = 1;

        $currency_code = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    	$quote->setCurrency();
    	

    	$shippingAddress->setCollectShippingRates(true)
    	->collectShippingRates(true);

    	$rates = [];
    	$shop =  $quote->getShippingAddress()->getGroupedAllShippingRates();
    	foreach($shop as $_shop){
    	 foreach ($_shop as $rate) {
               $rates[] = $rate->getData();
            }
    	}

        $shippingrates = [];
    	$shipping = [];
    	foreach($rates as $shiprates){
            if($shiprates['carrier'] != 'vendor_rates' && !isset($shiprates['error_message'])){
                $shipping['code'] = $shiprates['code'];
                $shipping['carrier'] = $shiprates['carrier'];
                $shipping['method'] = $shiprates['method'];
                $shipping['price'] = $shiprates['price'];
    	        $shipping['title'] = $shiprates['carrier_title'].' '.$shiprates['method'];
            }
            else{
                continue;
            }

    	    $shippingrates[] = $shipping;
        }

        $address['firstname'] = $customerData->getFirstName();
        $address['lastname'] = $customerData->getLastName();
        $address['street'] = $postData['street'];
        $address['area'] = $postData['area'];
        $address['city'] = $postData['city'];
        $address['country_id'] = $postData['country_id'];
        $address['region'] = $region;
        $address['region_id'] = $region_id;
        $address['postcode'] = $postData['zipcode'];
        $address['telephone'] = $postData['telephone'];
        if(isset($postData['message'])){
            $address['message'] = $postData['message'];
        }
        else{
            $address['message'] = '';
        }
//print_r($address);die;
        
        $this->_customersession->setData('address',$address);

        $this->_customersession->setData('shipment',$shippingrates);
        }
        catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('requesttoquote/cart/index');
            return;
            
        }
        return $this->_redirect('*/cart/index', ['_current' => true, '_use_rewrite' => true]);
    	 
    }
    
}
