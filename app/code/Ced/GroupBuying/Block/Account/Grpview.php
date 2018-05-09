<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\GroupBuying\Block\Account;

/**
 * Sales order history block
 */
class Grpview extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'groupbuy/account/grpview.phtml';

    /**
     * @var \Ced\Groupgift\Model\ResourceModel\Main\CollectionFactory
     */
    protected $_giftCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $gifts;

     public $_objectManager;

     public $_scopeConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $giftCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = []
    ) {  
         $this->_giftCollectionFactory = $giftCollectionFactory;
         $this->_customerSession = $customerSession;
         $this->_objectManager = $objectManager;
         $this->_productRepository = $productRepository;
        parent::__construct($context, $data);
        $gift = $this->_objectManager->create('Ced\GroupBuying\Model\Main')->load($this->getRequest()->getParam('gid'));      
        $this->setGift($gift);
    }

    function getGuests()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerId);
        $guests = $this->_objectManager->create('Ced\GroupBuying\Model\Guest')->getCollection()
                 ->addFieldToFilter('groupgift_id',$this->getGift()->getId())->addFieldToFilter(
                'guest_email',   $customer['email'])->getFirstItem();        
        return $guests['guest_name'];
    }


    public function getPostUrl($gift)
    {
        return $this->getUrl('*/*/post', array('gift_id' => $gift->getId()));
    }

     public function getBackUrl()
    {
        return $this->getUrl('groupbuying/account/list');
    }
   

   
   public function getImages()
    {
        $customer_Id = $this->_customerSession->getCustomerId();
        $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customer_Id);
        $image = $this->_objectManager->get('Ced\GroupBuying\Model\Main')->load($this->getGift()->getId());
        $_product= $this->_objectManager->get('Magento\Catalog\Model\Product')->load($image['original_product_id']);                  
        return $_product;
    }


    public function gethelper()
    {   $this->_helper = $this->_objectManager->get('Magento\Catalog\Helper\Output');
        return $this->_helper;
    }


    public function getImageUrl()
    {   $image = $this->_objectManager->get('Ced\GroupBuying\Model\Main')->load($this->getGift()->getId()); 
        $product = $this->_productRepository->getById($image['original_product_id']);
        return $product->getUrlModel()->getUrl($product);
    }

     
     public function getAllGuest()
    {   $allgst = $this->_objectManager->create('Ced\GroupBuying\Model\Guest')->getCollection()
                 ->addFieldToFilter('groupgift_id',$this->getGift()->getId()); 
                 
        
        return $allgst;
    }


}
