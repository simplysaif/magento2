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
Class PrepareProductSave implements ObserverInterface
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
	 *Notify Customer Account share Change 
	 *
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/*$groups = $observer->getEvent()->getDataObject()->getGroups();
		$customer_share=isset($groups['account_share']['fields']['scope']['value'])?$groups['account_share']['fields']['scope']['value']:Mage::getStoreConfig(\Mage\Customer\Model\Config\Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE);
		$config = new \Mage\Core\Model\Config();
		if($customer_share!=''&&$customer_share!=Mage::getStoreConfig(\Mage\Customer\Model\Config\Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE))
			$config->saveConfig(\Ced\CsMarketplace\Model\Vendor::XML_PATH_VENDOR_WEBSITE_SHARE,1); */
	
	$request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if (($items = $request->getPost('bundle_options')) && !$product->getCompositeReadonly()) {
            $product->setBundleOptionsData($items);
        }
        
        if (($selections = $request->getPost('bundle_selections')) && !$product->getCompositeReadonly()) {
            $product->setBundleSelectionsData($this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->unsetInvalidData($selections,'bundle'));
        }

        if ($product->getPriceType() == '0' && !$product->getOptionsReadonly()) {
            $product->setCanSaveCustomOptions(true);
            if ($customOptions = $product->getProductOptions()) {
                foreach (array_keys($customOptions) as $key) {
                    $customOptions[$key]['is_delete'] = 1;
                }
                $product->setProductOptions($customOptions);
            }
        }
        $product->setCanSaveBundleSelections((bool)$request->getPost('affect_bundle_product_selections') && !$product->getCompositeReadonly());
        return $this;
	}
}	
?>
