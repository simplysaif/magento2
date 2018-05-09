<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Vendor Fixed rate model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
namespace Ced\CsCommission\Model\Vendor\Rate;

class Miscellaneous extends \Ced\CsMarketplace\Model\Vendor\Rate\Abstractrate
{
     protected $_base_fee = 0;
     protected $_fee = 0;
     protected $_coreRegistry=0;
     protected $_scopeConfig=0;
     /**
      * Retrive miscellaneous conditions from settings
      * (vendor specific OR vendor group specific OR global)
      *
      * @param int
      * @return array
      */
       public function __construct(
     		\Magento\Framework\Model\Context $context,
     		\Magento\Framework\ObjectManagerInterface $objectInterface,
       		\Magento\Framework\Registry $registerInterface,
       		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     ) {
     	
     	$this->_objectManager = $objectInterface;
     	$this->_coreRegistry = $registerInterface;
     	$this->_scopeConfig = $scopeConfig;
     	
     } 
     protected function _getMiscellaneousConditions($vendorId,$storeId) {
     	if($this->_coreRegistry->registry('current_order_vendor'))
     		$this->_coreRegistry->unregister('current_order_vendor');
     
     	$this->_coreRegistry->register('current_order_vendor',$vendorId);
     	
     	$categoryWise = $this->_objectManager->get('\Ced\CsCommission\Helper\Category')->getUnserializedOptions();
     	$productTypes =  $this->_objectManager->get('\Ced\CsCommission\Helper\Product')->getUnserializedOptions();
     	
     	//Customize code to get sales, ship, payments & service tax 
     	$salesCalMethod = $this->_scopeConfig->getValue('ced_vpayments/general/commission_mode_sales', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
     	$salesRate = $this->_scopeConfig->getValue('ced_vpayments/general/commission_fee_sales', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
     	$shipCalMethod = $this->_scopeConfig->getValue('ced_vpayments/general/commission_mode_ship', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
     	$shipRate = $this->_scopeConfig->getValue('ced_vpayments/general/commission_fee_ship', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
     	$paymentCalMethod = $this->_scopeConfig->getValue('ced_vpayments/general/commission_mode_payments', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
     	$paymentRate = $this->_scopeConfig->getValue('ced_vpayments/general/commission_fee_paymnets', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
     	$servicetaxCalMethod = $this->_scopeConfig->getValue('ced_vpayments/general/commission_mode_servicetax', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
     	$servicetaxRate = $this->_scopeConfig->getValue('ced_vpayments/general/commission_fee_servicetax', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    
     	return array($productTypes,$categoryWise,$salesCalMethod,$salesRate,$shipCalMethod,$shipRate,$paymentCalMethod,$paymentRate,$servicetaxCalMethod,$servicetaxRate);
     }
     /**
      * Get the commission based on group
      *
      * @param float $grand_total
      * @param float $base_grand_total
      * @param string $currency
      * @param array $commissionSetting
      * @return array
      */
     public function calculateCommission($grand_total = 0, $base_grand_total = 0, $base_to_global_rate = 1, $commissionSetting = array()) {
     	try {
     		
     		$result = array();
     		$order = $this->getOrder();
     		
     		$vendorId = $this->getVendorId();
     		$vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
     		$result['base_fee'] = 0;
     		$result['fee'] = 0;
     		$salesCost = 0;
     		$shipCost = 0;
     		$paymentCost = 0;
     		$serviceTaxCost = 0;
     	
     		/*list($productTypes,$categoryWise) = $this->_getMiscellaneousConditions($vendorId);*/
     		list($productTypes,$categoryWise,$salesCalMethod,$salesRate,$shipCalMethod,$shipRate,$paymentCalMethod,$paymentRate,$servicetaxCalMethod,$servicetaxRate) = $this->_getMiscellaneousConditions($vendorId,$order->getStoreId());
     		$itemCommission = isset($commissionSetting['item_commission']) ? $commissionSetting['item_commission'] : array();
     		$customTotalPrice = 0;
     		foreach($itemCommission as $key => $itemPrice) {
     			$customTotalPrice = $customTotalPrice + $itemPrice;
     		}
     		/*
     			echo "{{";
     			echo $salesRate.'==';
     			echo $salesCalMethod;
     			echo "}}{{";
     			echo $shipRate.'==';
     			echo $shipCalMethod;
     			echo "}}{{";
     			echo $paymentRate.'==';
     			echo $paymentCalMethod;
     			echo '}}';
     			die;
     			*/
     		
     		$salesCost = $this->_objectManager->get('Ced\CsCommission\Helper\Data')->calculateFee($customTotalPrice,$salesRate,$salesCalMethod);
     		//print_r($salesCost);die;
     		$shipCost = $this->_objectManager->get('Ced\CsCommission\Helper\Data')->calculateFee($customTotalPrice,$shipRate,$shipCalMethod);
     		$paymentCost = $this->_objectManager->get('Ced\CsCommission\Helper\Data')->calculateFee($customTotalPrice,$paymentRate,$paymentCalMethod);
     		/*$serviceTaxCost = Mage::helper('cscommission')->calculateFee($customTotalPrice,$servicetaxRate,$servicetaxCalMethod);*/
     		$custom_base_fee = $salesCost + $shipCost + $paymentCost;
     		$custom_fee = $this->_objectManager->get('\Magento\Directory\Helper\Data')->currencyConvert($custom_base_fee, $order->getBaseCurrencyCode(),
     				$order->getGlobalCurrencyCode());
     		$result['base_fee'] = $custom_base_fee;
     		$result['fee'] = $custom_fee;
     		/* print_r($result);die; */
     		
     		if(count($productTypes) > 0 || count($categoryWise) > 0) {
     			
     			$item_commission = array();
    
     			foreach($order->getAllItems() as $item) {     			
     			//print_r($item->getData('item_id'));die;
     				if($item->getVendorId() && $item->getVendorId() == $vendorId) {
     					$temp_base_fee = 0;
     					$temp_fee = 0;
     					$product_temp_priority = array();
     					$category_temp_priority = array();
     					$product = $this->_objectManager->get('\Magento\Catalog\Model\Product')->load($item->getProductId());
     					
     					$productTypeId = (string)$product->getTypeId();
     					if(is_array($product->getCategoryIds())) {
     						$productCategoriesIds = (array)$product->getCategoryIds();
     					} else {
     						$productCategoriesIds = explode(',',trim((string)$product->getCategoryIds()));
     					}
     					$productCategoriesIds = (array)$productCategoriesIds;
     					$assignedProductType = array_keys($productTypes);
     					$assignedCategory = array_keys($categoryWise);
     					if (isset($productTypes[$productTypeId])) {
     						$product_temp_priority = $productTypes[$productTypeId];
     					} elseif(in_array('alltype',$assignedProductType)) {
     						$product_temp_priority = $productTypes['alltype'];
     					}
     					foreach($categoryWise as $id=>$condition) {
     						$categoryId = isset($condition['category']) && (int)$condition['category']?(int)$condition['category']:0;
     						if (!$categoryId) continue;
     						if(in_array($categoryId,$productCategoriesIds)) {
     							if(!isset($category_temp_priority['priority']) || (isset($category_temp_priority['priority']) && (int)$category_temp_priority['priority'] > (int)$condition['priority'])) {
     								$category_temp_priority = $condition;
     							}
     						}
     					}
     					/*
     						foreach($productCategoriesIds as $category) {
     						if (isset($categoryWise[$category])) {
     						if(!isset($category_temp_priority['priority']) || (isset($category_temp_priority['priority']) && (int)$category_temp_priority['priority'] > (int)$categoryWise[$categoryId]['priority'])) {
     						$category_temp_priority = $categoryWise[$categoryId];
     						}
     						}
     						}
     						*/
     					/* if($_SERVER['REMOTE_ADDR'] == '182.74.41.196') {
     					 print_r($categoryWise);
     					 echo '==';
     					 print_r($productCategoriesIds);
     					 echo '==';
     					 print_r($category_temp_priority);die;
     						} */
     					if(!isset($category_temp_priority['priority']) && isset($categoryWise['all'])) {
     						$category_temp_priority = $categoryWise['all'];
     					}
     					/* Calculation starts for fee calculation */
     					/* START */
     					$log = array();
     					$pt = isset($product_temp_priority['fee'])?$product_temp_priority['fee']:$this->_scopeConfig->getValue('ced_vpayments/general/commission_fee_default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$order->getStoreId());
     					$cw = isset($category_temp_priority['fee'])?$category_temp_priority['fee']:$this->_scopeConfig->getValue('ced_vpayments/general/commission_fee_default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$order->getStoreId());
     											
												
						$log[$order->getId()][$vendorId]['group'] = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendorId)->getGroup();
     					$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['pt']['rate'] = $pt;
     					$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['cw']['rate'] = $cw;
     					
     			
     					$pt = isset($product_temp_priority['method'])?$this->_objectManager->get('Ced\CsCommission\Helper\Data')->calculateFee($itemCommission[$item->getQuoteItemId()],$pt,$product_temp_priority['method']):$this->_objectManager->get('Ced\CsCommission\Helper\Data')->calculateFee($itemCommission[$item->getQuoteItemId()],$pt,$this->_scopeConfig->getValue('ced_vpayments/general/commission_mode_default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$order->getStoreId()));
     					$cw = isset($category_temp_priority['method'])?$this->_objectManager->get('Ced\CsCommission\Helper\Data')->calculateFee($itemCommission[$item->getQuoteItemId()],$cw,$category_temp_priority['method']):$this->_objectManager->get('Ced\CsCommission\Helper\Data')->calculateFee($itemCommission[$item->getQuoteItemId()],$cw,$this->_scopeConfig->getValue('ced_vpayments/general/commission_mode_default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$order->getStoreId()));

     					$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['pt']['fee'] = $pt;
     					$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['cw']['fee'] = $cw;
     					$cf = $this->_scopeConfig->getValue('ced_vpayments/general/commission_fn', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$order->getStoreId());
     					$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['cf']['method']['name'] = $cf;
     					switch($cf) {
     						case \Ced\CsCommission\Model\Source\Vendor\Rate\Aggregrade::TYPE_MIN :
     							$temp_base_fee = min($pt,$cw);
     							$temp_fee = $this->_objectManager->get('\Magento\Directory\Helper\Data')->currencyConvert($temp_base_fee, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
     							$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['cf']['method']['min']['base_fee'] = $temp_base_fee;
     							$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['cf']['method']['min']['fee'] = $temp_fee;
     							break;
     						case \Ced\CsCommission\Model\Source\Vendor\Rate\Aggregrade::TYPE_MAX :
     						default :
     							$temp_base_fee = max($pt,$cw);
     							$temp_fee = $this->_objectManager->get('\Magento\Directory\Helper\Data')->currencyConvert($temp_base_fee, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
     							$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['cf']['method']['max']['base_fee'] = $temp_base_fee;
     							$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['cf']['method']['max']['fee'] = $temp_fee;
     							break;
     					}
     					/* END */
     					$result['base_fee'] = $result['base_fee'] + $temp_base_fee;
     					$result['fee'] = $result['fee'] + $temp_fee;
     					$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['result'] = $result;
     					$item_commission[$item->getQuoteItemId()] = array('base_fee' => $temp_base_fee, 'fee' => $temp_fee);
     					$log[$order->getId()][$vendorId][$item->getQuoteItemId()][$item->getProductId()]['item_commission'] = $item_commission[$item->getQuoteItemId()];
     					/* Mage::log(print_r($log,true),null,'cscommission_calculation.log',true); */
     				}
     			}
    
     			$totalBaseFeeCommisionExludeServiceTax = 0;
     			$totalBaseFeeCommisionIncludeServiceTax =0;
     			$finalCommision = 0;
     			$totalBaseFeeCommisionExludeServiceTax = $result['base_fee'];
     			$serviceTaxCost = $this->_objectManager->get('Ced\CsCommission\Helper\Data')->calculateFee($totalBaseFeeCommisionExludeServiceTax,$servicetaxRate,$servicetaxCalMethod);
     			$totalBaseFeeCommisionIncludeServiceTax = $totalBaseFeeCommisionExludeServiceTax + $serviceTaxCost;
     			/* echo $totalBaseFeeCommisionIncludeServiceTax.'==';
     				echo $customTotalPrice;
     				die;
     				*/
     			$finalCommision = min($totalBaseFeeCommisionIncludeServiceTax,$customTotalPrice);
     			$result['base_fee'] = $finalCommision;
     			$result['fee'] = $this->_objectManager->get('\Magento\Directory\Helper\Data')->currencyConvert($finalCommision, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
     			$result['item_commission'] = json_encode($item_commission);
     			/* print_r($result);die; */
     			/* Mage::log(print_r($result,true),null,'cscommission_calculation.log',true); */
     		}
     		$this->_coreRegistry->unregister('current_order_vendor');
     //print_r($result);die('ghuih');
     		return $result;
     	} catch (Exception $e) {
     		echo $e->getMessage();die;
     		//Mage::log($e->getMessage(),null,'cscommission_exceptions.log');
     	}
     }
}