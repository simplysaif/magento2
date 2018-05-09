<?php
    
    namespace Ced\GroupBuying\Observer;
 
    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\App\RequestInterface;
 
    class PlaceOrder implements ObserverInterface
    {    


         
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
     protected $_registry = null;

    /**
     * @param \Magento\Quote\Model\ResourceModel\Quote $quote
     */
   public function __construct (        
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
      \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $giftCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession

    ) {   
        $this->_objectManager=$objectManager;
         $this->_logger = $logger;
         $this->_giftCollectionFactory = $giftCollectionFactory;
         $this->_checkoutSession=$checkoutSession;
        $this->_registry = $registry;
    }
 

   public function execute(\Magento\Framework\Event\Observer $observer)
    {
       return $this;
       if($this->_checkoutSession->getGid())
           {
            $id=$this->_checkoutSession->getGid();
           $customerid = $this->_objectManager->create('Magento\Customer\Model\Session')->getCustomerId();
           $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerid);
           $guest_data=$this->_giftCollectionFactory->create()->addFieldToSelect(
                    '*'
                )->addFieldToFilter(
                    'groupgift_id',
                     $id
                )->addFieldToFilter(
                    'guest_email',
                     $customer['email']
                )->getFirstItem();
                try{
                      $guest_data->setData('request_approval',5);
                      $guest_data->setData('pay_status',1);
                      $guest_data->save();
                   }catch(\Exception $e){echo $e->getMessage();}
           $this->_checkoutSession->unsGid();         
           }            


      }

 
}


    

       
        