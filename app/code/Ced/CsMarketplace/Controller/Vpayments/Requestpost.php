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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Vpayments;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;


class Requestpost extends \Magento\Framework\App\Action\Action {
    public function __construct( Context $context,
        \Magento\Customer\Model\Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Ced\CsMarketplace\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime, array $data = []) 

    {
        $this->_getSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->csmarketplaceHelper=$helperData;
        $this->datetime = $datetime;
        parent::__construct ( $context, $data );
    }


    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();

        if(!$this->_getSession->getVendorId()) {

            return; 
        }
        
        $orderIds = $this->getRequest()->getParam('payment_request');

        if(strlen($orderIds) > 0) {
            $orderIds = explode(',',$orderIds);
        }

        if (!is_array($orderIds)) {
            $this->messageManager->addError(__('Please select amount(s).'));
        } else {
            if (!empty($orderIds)) {
                try {
                    $updated = 0;
                    foreach ($orderIds as $orderId) {
                        $order_increment_id=$this->_objectManager->create('Ced\CsTransaction\Model\Items')->load($orderId)->getOrderIncrementId();

                        $model = $this->_objectManager->get('Ced\CsMarketplace\Model\Vpayment\Requested')->getCollection()->addFieldToFilter('vendor_id', $this->_getSession->getVendorId())->addFieldToFilter('order_id', $order_increment_id);
                        
                        if(sizeof($model->getData())==0){
                            $amount = 0.000;

                            $pendingPayments = $this->_objectManager->get('\Ced\CsMarketplace\Model\Vorders')->getCollection()
                                                    ->addFieldToFilter('order_payment_state',array('in'=>[\Magento\Sales\Model\Order\Invoice::STATE_PAID, \Ced\CsOrder\Model\Invoice::STATE_PARTIALLY_PAID]))
                                                    ->addFieldToFilter('payment_state',array('eq'=>\Ced\CsMarketplace\Model\Vorders::STATE_OPEN))
                                                    ->addFieldToFilter('order_id',array('eq'=> $order_increment_id))
                                                    ->addFieldToFilter('vendor_id',array('eq'=> $this->_getSession->getVendorId()))
                                                    ->setOrder('created_at', 'ASC');
                                                    
                            $main_table=$this->csmarketplaceHelper->getTableKey('main_table');
                            $order_total=$this->csmarketplaceHelper->getTableKey('order_total');
                            $shop_commission_fee=$this->csmarketplaceHelper->getTableKey('shop_commission_fee');
                            $pendingPayments->getSelect()->columns(array('net_vendor_earn' => new \Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
                            
                            if(count($pendingPayments) > 0) {
                                $amount = $pendingPayments->getFirstItem()->getNetVendorEarn();
                            }
                            
                            $order_increment_id=$this->_objectManager->create('Ced\CsTransaction\Model\Items')->load($orderId)->getOrderIncrementId();
                           
                            $data = array('vendor_id'=>$this->_getSession->getVendorId(), 'order_id'=>$order_increment_id, 'amount'=>$amount, 'status'=>\Ced\CsMarketplace\Model\Vpayment\Requested::PAYMENT_STATUS_REQUESTED,'created_at'=>$this->datetime->date('Y-m-d H:i:s'));
                            $this->_objectManager->get('\Ced\CsMarketplace\Model\Vpayment\Requested')->addData($data)->save();
                            $updated++;

                        }
                    }
                    if($updated) {

                        $this->messageManager->addSuccess(__('Total of %1 amount(s) have been requested for payment.', $updated)
                        );
                        //return $resultRedirect->setPath('cstransaction/vpayments/request');
                    } else {
                         $this->messageManager->addSuccess(__('Payment(s) have been already requested for payment.')
                        );
                         //return $resultRedirect->setPath('cstransaction/vpayments/request');
                    }
                } catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        }
        
    }
    
}
