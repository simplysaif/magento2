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
 * @package     Ced_CsMultiSeller
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultiSeller\Model\System\Config\Source;
class Type extends \Ced\CsMarketplace\Model\System\Config\Source\AbstractBlock
{ 
	
	/**
     * Retrieve Option values array
     *
	 * @param boolean $defaultValues
	 * @param boolean $withEmpty
     * @return array
     */
    public function toOptionArray($defaultValues = false, $withEmpty = false)
    {
		$options = array();
		$allowedTypes = array('simple');
		
		foreach($this->_objectManager->get('Magento\Catalog\Model\Product\Type')->getOptionArray() as $value=>$label) {
				if(!in_array($value,$allowedTypes)) 
					continue;
				$options[] = array('value'=>$value,'label'=>$label);
		}
		if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
		return $options;
    }

}