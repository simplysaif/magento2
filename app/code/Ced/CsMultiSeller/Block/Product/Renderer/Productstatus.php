<?php 
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsMultiSeller
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */
 namespace Ced\CsMultiSeller\Block\Product\Renderer;
class Productstatus extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
	protected $_vproduct;
	protected $_storeManager;
	
	/**
	 * @param \Magento\Backend\Block\Context $context
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
			\Magento\Backend\Block\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			array $data = []
	) {
		$this->_objectManager = $objectManager;
		$this->_storeManager = $storeManager;
		parent::__construct($context, $data);
	}
	
	/**
	 * @return Status
	 */
	public function render(\Magento\Framework\DataObject $row) {
		$vOptionArray = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorOptionArray();
		if($row->getCheckStatus()==\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS)
			return $vOptionArray[$row->getCheckStatus().$row->getStatus()];
		else 
			return $vOptionArray[$row->getCheckStatus()];
	}

}
?>