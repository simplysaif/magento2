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
  * @package     Ced_Barcode
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */ 
namespace Ced\Barcode\Model;

class DescriptionOption implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function toOptionArray()
	{
		return [

		['value' => 'sku', 'label' => __('Sku')],
		['value' => 'name', 'label' => __('Name')],
		['value' => 'price', 'label' => __('Price')],
		['value' => 'image', 'label' => __('Image')],
		['value' => 'qty_increment', 'label' => __('Qty Increment')],
		

		];
	}
}