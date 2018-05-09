<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\GroupBuying\Block\Account;

/**
 * Sales order history block
 */
class Listgift extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'groupbuy/account/listgift.phtml';

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
        array $data = []
    ) {  
        $this->_giftCollectionFactory = $giftCollectionFactory;
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
        $this->pageConfig->getTitle()->set(__('My Group Buying'));
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
            $this->gifts = $this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'owner_customer_id',
                $customerId
            )->setOrder(
                'group_id',
                'asc'
            );
        }

        return $this->gifts;
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
        return $this->getUrl('*/account/grpview', ['gid' => $gift->getId()]);
    }

    /**
     * @param object $gift
     * @return string
     */
    public function getEditUrl($gift)
    {
        return $this->getUrl('*/*/view', ['gift_id' => $gift->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
