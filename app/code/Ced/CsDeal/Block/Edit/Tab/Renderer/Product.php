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
 * @package     Ced_CsDeal
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Block\Edit\Tab\Renderer;

class Product extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_vproduct;
    public $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Ced\CsMarketplace\Model\Vproducts $vproduct,
    	\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_vproduct = $vproduct;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
    	$_product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($row->getProductId());
		if($_product && $_product->getId()){
						$html=$_product->getName();
						return $html;
		}
		
	}
}