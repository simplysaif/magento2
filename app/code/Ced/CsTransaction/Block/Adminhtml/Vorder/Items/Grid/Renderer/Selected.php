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
 * @package     Ced_CsTransaction
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsTransaction\Block\Adminhtml\Vorder\Items\Grid\Renderer;
class Selected extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
	{
	
		public function __construct(\Magento\Backend\Block\Context $context,\Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
		{
			$this->_objectManager=$objectManager;
			parent::__construct($context, $data);
		}
		
		public function render(\Magento\Framework\DataObject $row){
		if($row->getOrderId()!=''){ 
			$params = $this->getRequest()->getParams();
			$orderIds=$params['order_ids'];
			$html='<input type="checkbox" class="csmarketplace_relation_id checkbox" value="'.$orderIds.'" name="in_orders">';
			return $html;
			// return "<a href='". $url . "' target='_blank' >".$row->getOrderId()."</a>";		  
		  }            
		else 
    	 return '';
     }
}