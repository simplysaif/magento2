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
class Productid extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

	/**
	 * @return Product Id
	 */
	public function render(\Magento\Framework\DataObject $row) {
		return '<a href="'.$this->getUrl('*/*/edit', array(
				'store'=>$this->getRequest()->getParam('store',0),
				'id'=>$row->getId())).'" title="'.$row->getId().'">'.$row->getId().'</a>';
	}

}
?>