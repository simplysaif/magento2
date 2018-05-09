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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
Class DuplicateProduct implements ObserverInterface
{
	
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;
	
	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
		
        $this->_objectManager = $objectManager;
    }
	
	 /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
		$invoice = $observer->getDataObject();
		$order = $invoice->getOrder();
		//$this->_objectManager('Ced\CsMarketplace\Helper\Data')->logProcessedData($order->getData('increment_id'), Ced_CsMarketplace_Helper_Data::SALES_ORDER_PAYMENT_STATE_CHANGED);
		if ($order->getBaseTotalDue() == 0) {
			$vorders = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')
							->getCollection()
							->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));
			if (count($vorders) > 0) {
				foreach ($vorders as $vorder) {
					try{
						$vorder->setOrderPaymentState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
						$vorder->save();
						//Mage::helper('csmarketplace')->logProcessedData($vorder->getData(), Ced_CsMarketplace_Helper_Data::VORDER_PAYMENT_STATE_CHANGED);

						}
						catch(Exception $e){
							echo "exception: ".$e->getMessage();die;
						//Mage::helper('csmarketplace')->logException($e);
						}
				}
			}					 
		}
		return $this;		
    }		
}

?>
