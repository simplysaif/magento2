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
 * @package     Ced_RequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\RequestToQuote\Model\System\Config\Customer;
use Magento\Store\Model\StoreManagerInterface;

class Groups extends \Magento\Framework\App\Helper\AbstractHelper
{


    protected $_context;
    protected $_storeManger;
    protected $_producttype;
    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\Type $producttype,
        StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectInterface
    ) {
        $this->storeManager = $storeManager;
        $this->_context = $context;
        $this->_producttype = $producttype;
        $this->_objectManager = $objectInterface;
    }


    public function toOptionArray($defaultValues = false, $withEmpty = false,$storeId=null)
    {
        $filter_a = array('null' => true);
        $filter_b = array('like' => '%configurable%');
        $attributes = array();
        $attributes = $this->_objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection')->toOptionArray();

        $options = array();

        foreach($attributes as $key => $value) {
	if($value['value']!=0){
            $options[] = array('value' => $value['value'], 'label' => $value['label']);
            }
        }
        return $options;
    }

}
