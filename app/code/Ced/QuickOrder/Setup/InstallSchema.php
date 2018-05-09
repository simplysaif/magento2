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
 * @package     Ced_QuickOrder
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\QuickOrder\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
	
	/**
	 * {@inheritdoc}
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();	
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$mediaurl= $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$state = $objectManager->get('\Magento\Framework\App\State');
		$state->setAreaCode('frontend');
		$websiteId = $storeManager->getWebsite()->getWebsiteId();
	
		$store = $storeManager->getStore();
		$storeId = $store->getStoreId();

		$rootNodeId = $store->getRootCategoryId();

		$rootCat = $objectManager->get('Magento\Catalog\Model\Category');
		$cat_info = $rootCat->load($rootNodeId);
		
		$categorys=array('B2b Quick Order');
		foreach($categorys as $cat)
		{
			$name=ucfirst($cat);
			$url=strtolower($cat);
			$cleanurl = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags($url))))));
			$categoryFactory=$objectManager->get('\Magento\Catalog\Model\CategoryFactory');
			$categoryTmp = $categoryFactory->create();
			$categoryTmp->setName($name);
			$categoryTmp->setIsActive(true);
			$categoryTmp->setUrlKey($cleanurl);
			$categoryTmp->setData('description', 'description');
			$categoryTmp->setParentId($rootCat->getId());
			$mediaAttribute = array ('image', 'small_image', 'thumbnail');
			$categoryTmp->setImage('/m2.png', $mediaAttribute, true, false);
			$categoryTmp->setStoreId($storeId);
			$categoryTmp->setPath($rootCat->getPath());
			$categoryTmp->save();
		
		}
		
	}

}