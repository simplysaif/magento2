<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\GroupBuying\Controller\Account;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class Order extends Action
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    protected $resultPageFactory;
    protected $customerSession;
    protected $_request;
    protected $_response;
    protected $_objectManager;
    protected $_coreRegistry = null;
    protected $_scopeConfig;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
         \Magento\Checkout\Model\Session $checkoutSession,
        \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $giftCollectionFactory,
        \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $lastCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) { 
        $this->resultPageFactory  = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession=$checkoutSession;
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_lastCollectionFactory = $lastCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Action for reorder
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
      $id=$this->getRequest()->getParam('gift_id');
      $this->_checkoutSession->setGid($id);
      $customerId = $this->_customerSession->getCustomerId();
      $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerId);
      $guest    = $this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'guest_email',
                $customer['email']
            )->addFieldToFilter(
                'groupgift_id',
                 $id
            )->getFirstItem();
      $p_qty=$guest->getData();
      $main=$this->_lastCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'group_id',
                 $id
            )->getFirstItem();
      $p_id=$main->getData();    
      $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($p_id['original_product_id']);
        /* @var $cart \Magento\Checkout\Model\Cart */
      $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        
        try {   $cart->truncate();
                $cart->addProduct ( $product, ['product'=> $p_id['original_product_id'] ,'qty'=> $p_qty['quantity'] ]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_objectManager->get('Magento\Checkout\Model\Session')->getUseNotice(true)) {
                    $this->messageManager->addNotice($e->getMessage());
                } else {
                    $this->messageManager->addError($e->getMessage());
                }
               // return $resultRedirect->setPath('*/*/history');
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
               // return $resultRedirect->setPath('checkout/index/index');
            }
        

        $cart->save();
       

     

        return $this->_redirect('checkout/cart/index');
    }
}
