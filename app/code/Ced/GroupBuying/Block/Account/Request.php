<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\GroupBuying\Block\Account;

/**
 * Sales order history block
 */
class Request extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'groupbuy/account/request.phtml';

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

    protected $last;
 

     public $_objectManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
     \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $giftCollectionFactory,
     \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $lastCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {  
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_lastCollectionFactory = $lastCollectionFactory;
        $this->_customerSession = $customerSession;
         $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Group Request'));
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {   
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->gifts) {
             $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerId);
            $this->gifts = $this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'guest_email',
                $customer['email']
            )->setOrder(
                'id',
                'asc'
            );
        }

        return $this->gifts;
    }



     public function getlastOrders($gift)
    {    
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->last) {
             $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerId);
        $this->last= $this->_lastCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'group_id',
                $gift['groupgift_id']
            );
        }
       
       return $this->last;
    }


     public function getlastoneOrders($id)
    {   
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
       
        $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerId);
        $data=$this->_objectManager->create('Ced\GroupBuying\Model\Main')->load($id); 
       
       
       return $data;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setLimit(10)
            ->setCollection(
                $this->getOrders()
            );

            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $gift
     * @return string
     */
    public function getViewUrl($gift)
    {
        return $this->getUrl('*/*/deny', ['gid' => $gift['groupgift_id']]);
    }

    /**
     * @param object $gift
     * @return string
     */
    public function getEditUrl($gift)
    {
        return $this->getUrl('*/*/approve', ['gift_id' =>$gift['groupgift_id']]);
    }

    public function getPurchaseUrl($gift)
    {      
        return $this->getUrl('*/*/view', ['gift_id' => $gift['groupgift_id']]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    public function getPaid($gift)
    {   
       
        return $this->getUrl('*/*/order', ['gift_id' =>  $gift['groupgift_id']]);
    }

    public function customized($gift)
    {   
        $group=$this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'groupgift_id',
                $gift['groupgift_id']
            );

            foreach ($group->getData() as $key => $value) {
            
            if(($value['quantity'] == 0)&& ($value['request_approval']!=1))
             return 0;
            }
            
        return 1;
    }

    public function guestuser($gift)
    {  
        $group=$this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'groupgift_id',
               $gift['groupgift_id']
            );

          
        return $group; 
    }

    /**
     * @param object $gift
     * @return string
     */
    public function getProductView($gift)
    {     
        return $this->getUrl('*/account/grpview', ['gid' => $gift['groupgift_id']]);
    }

     /**
     * @param object $gift
     * @return string
     */
    public function getGroupMessage($gift)
    {  
        $Group_Collection = $this->_objectManager->create('Ced\GroupBuying\Model\Main')->load($gift['groupgift_id']);
         
        return $Group_Collection->getGiftMsg();
    }
}
