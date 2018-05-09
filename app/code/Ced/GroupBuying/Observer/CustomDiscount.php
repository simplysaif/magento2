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
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\GroupBuying\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

Class CustomDiscount implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    private $messageManager;
    protected $customerSession;
    
    public function __construct(
      \Magento\Framework\ObjectManagerInterface $objectManager,
      \Magento\Customer\Model\Session $customerSession,
      \Magento\Framework\App\Request\Http $request,
      \Magento\Checkout\Model\Session $checkoutSession,
      \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $giftCollectionFactory
      
    ) {
        $this->request = $request;
        $this->_objectManager = $objectManager;
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->customerSession = $customerSession;
        $this->_checkoutSession=$checkoutSession;
    }
    /**
     *Product Assignment Tab
     */
    
    public function getDiscountPrice($total,$quote)
    {
    	return $this;
      $productids = array();
      $productid ='';
      $membershipdata = $this->_objectManager->create('Ced\CustomerMembership\Model\Membership')->getCollection();
       
      foreach($membershipdata as $_membershipdata)
      {
        $productids[] = $_membershipdata->getProductId();
      }
      // print_r($productids);
      foreach($quote->getAllItems() as $items)
      {
        if(in_array($items->getProductId(),$productids))
        {
          $productid = $items->getProductId();
          break;
        }
      }
      try{
      if($this->_objectManager->create('Magento\Customer\Model\Session')->isLoggedIn()){
    
        if($total>0)
        {
          $customerid = $this->_objectManager->create('Magento\Customer\Model\Session')->getCustomerId();
          $subscribedplan = $this->_objectManager->create('Ced\CustomerMembership\Model\Subscription')->getCollection()->addFieldToFilter('customer_id',$customerid)->addFieldToFilter('status','running');
          if(count($subscribedplan->getData())>1)
          {
            $discountamount = ($subscribedplan->getLastItem()->getOrderDiscount()*$total)/100;
            return $discountamount;
          }
          else{
            $data =  $subscribedplan->getData();
            $discountamount = ($data[0]['order_discount']*$total)/100;
            return $discountamount;
          }
    
        }
      }
    }catch(\Exception $e){
      echo $e->getMessage();die('exception error');
    }
   }
   
   public function execute(\Magento\Framework\Event\Observer $observer)
   {
    
    $id=$this->_checkoutSession->getGid();
    $customerid = $this->_objectManager->create('Magento\Customer\Model\Session')->getCustomerId();
    $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerid);
    $guest_data=$this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'groupgift_id',
                 $id
            );
   $qty=0;         
   foreach ($guest_data->getData() as $key => $value) {
     $qty+=$value['quantity'];
   }
    $main=$this->_objectManager->get('Ced\GroupBuying\Model\Main')->load($id);
    $product=$this->_objectManager->get('Magento\Catalog\Model\Product')->load($main['original_product_id']);

     $realprice=0;
    foreach($product->getTierPrice() as $port)
             {
                 
                     if($qty>=$port['price_qty'])
                     {
                        
                        $realprice=$port['price'];
                        
                     }

             }
    if($realprice!=0) 
    $dis=(1-($realprice/$product->getPrice()))*100;
    else
    $dis=0;  
    $loggedin = $this->_objectManager->create('Magento\Customer\Model\Session')->isLoggedIn();
    // $subscribedplan = $this->_objectManager->create('Ced\CustomerMembership\Model\Subscription')->getCollection()->addFieldToFilter('customer_id',$customerid)->addFieldToFilter('status','running');
    if($loggedin):
    try{
    $quote=$observer->getEvent()->getQuote();
    $quoteid=$quote->getId();
    $total=$quote->getBaseSubtotal();
    //$discountAmount = $this->getDiscountPrice($total,$quote);
    //$discountAmount=10;
    if($quoteid) {
        
         /* $quote->setSubtotal(0);
        $quote->setBaseSubtotal(0);
        
        $quote->setSubtotalWithDiscount(0);
        $quote->setBaseSubtotalWithDiscount(0);
        
        $quote->setGrandTotal(0);
        $quote->setBaseGrandTotal(0); */
   
   
        $canAddItems = $quote->isVirtual()? ('billing') : ('shipping');
        
        foreach ($quote->getAllAddresses() as $address) {
   
           $address->setBaseSubtotal(0);
          
          //$address->setGrandTotal(0);
          //$address->setBaseGrandTotal(0); 
          //die('apply discount3');
          //echo "hello";
          //echo $address->getSubtotal().'  ';

          $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
          $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());
          $quote->setSubtotalWithDiscount(
              (float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
          );

          $quote->setBaseSubtotalWithDiscount(
              (float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
          );
   
          $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
          $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
   
          $quote ->save();
          //echo $quote->getBaseSubtotal();
          //$discountAmount = $this->getDiscountPrice($quote->getBaseSubtotal(),$quote);
          $discountAmount=($dis*$quote->getBaseSubtotal())/100;
          if($discountAmount>0) {
          $quote->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
          ->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
          ->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
          ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
          ->save();
            
          if($address->getAddressType()==$canAddItems) {
            
            
            //echo $address->setDiscountAmount; exit;
            $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount()-$discountAmount);
            $address->setGrandTotal((float) $address->getGrandTotal()-$discountAmount);
            $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount()-$discountAmount);
            $address->setBaseGrandTotal((float) $address->getBaseGrandTotal()-$discountAmount);
            if($address->getDiscountDescription()){
              $address->setDiscountAmount(-($address->getDiscountAmount()-$discountAmount));
              $address->setDiscountDescription($address->getDiscountDescription().', Custom Discount');
              $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount()-$discountAmount));
            }else {
              $address->setDiscountAmount(-($discountAmount));
              $address->setDiscountDescription('Group Discount');
              $address->setBaseDiscountAmount(-($discountAmount));
            }
            $address->save();
          }//end: if
         
        } //end: foreach

        foreach($quote->getAllItems() as $item){
          //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
          if($total>0){
          $rat=$item->getPriceInclTax()/$total;
          $ratdisc=$discountAmount*$rat;
          $item->setDiscountAmount($discountAmount/(count($quote->getAllItems())));
          $item->setBaseDiscountAmount($discountAmount/(count($quote->getAllItems())))->save();
          }
        }
   
   
      }
      //die('hello');
   
    }
   }catch(\Exception $e){
      echo $e->getMessage();die('exception');
    }
    endif;
   }
   
   
}    

