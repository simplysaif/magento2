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
namespace Ced\CsRma\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class VendorTransaction  implements ObserverInterface
{
        
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;
    /**
    * @var Session 
    */
    protected $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
     /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $response;
    
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Ced\CsRma\Model\RequestFactory $rmaRequestFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager

    ) {
        $this->rmaRequestFactory = $rmaRequestFactory;
        $this->_request = $request;
        $this->_objectManager = $objectInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $obj = $observer->getEvent()->getVendorTrans();
        $package = $obj->getOrderArray();       
        $package['headers']['admin_fee'] = 'Admin RMA Fee';
        $package['pricing_columns'][] = 'admin_fee';
        foreach($package['values'] as $key => $values){
            //$incrementId = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id',$key);
            $rma_vorder = $this->rmaRequestFactory->craete()->getCollection()
                    ->addFieldToFilter('order_id',$key);
            $vadjustment = 0;
            foreach($rma_vorder->getData() as $val) {
                if ($val['status'] == 'Approved' && $val['resolution_requested'] == 'Refund') {
                    
                    $vadjustment += floatval($val['additional_refund']);
                    $vadjustment += floatval($val['refund_amount']);
                    $vadjustment -= floatval($val['vendor_adjustment_amount']);
                }
            }
            $fee = -1*$vadjustment;
            $package['values'][$key]['admin_fee'] = $fee;   
        }
        $obj->setOrderArray($package);
        return $obj;        
    }
}
