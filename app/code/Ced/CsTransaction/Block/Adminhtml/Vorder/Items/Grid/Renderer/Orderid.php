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
class Orderid extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
	{
	
		public function __construct(\Magento\Backend\Block\Context $context,\Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
		{
			$this->_objectManager=$objectManager;
			parent::__construct($context, $data);
		}
		
		public function render(\Magento\Framework\DataObject $row){
		if($row->getOrderId()!=''){ 
			 $url =  $this->getUrl("csorder/vendororder/view", array('vorder_id' => $row->getParentId()));	
			//$html='<a href="#popup" onClick="javascript:openMyPopup(\''.$url.'\')" >'.$row->getOrderIncrementId().'</a>';

			$html='<a href="#popup" onClick="window.open(
            \''.$url.'\',
            \'apiwizard,toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, ,left=100, top=100, width=1024, height=640\'
        )" >'.$row->getOrderIncrementId().'</a>';


			return $html;
			// return "<a href='". $url . "' target='_blank' >".$row->getOrderId()."</a>";		  
		  }            
		else 
    	 return '';
    }
}