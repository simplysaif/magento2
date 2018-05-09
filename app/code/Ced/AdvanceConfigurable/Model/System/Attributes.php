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
 * @package     Ced_AdvanceConfigurable
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\AdvanceConfigurable\Model\System;
use Magento\Store\Model\StoreManagerInterface;

class Attributes extends \Magento\Framework\App\Helper\AbstractHelper
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
    
    /**
     * Retrieve Option values array
     *
     * @param  boolean $defaultValues
     * @param  boolean $withEmpty
     * @return array
     */
    public function toOptionArray($defaultValues = false, $withEmpty = false,$storeId=null)
    {
        //print_r($this->_objectManager->create('Magento\Catalog\Model\Entity\Attribute')->getCollection()->addFieldToFilter('frontend_input','select')->getData());
        $filter_a = array('null' => true);
        $filter_b = array('like' => '%configurable%');
        $attributes = array();
        $attributes = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection')
                                     ->load()
                                     ->addFieldToFilter('is_global',1)
                                     ->addFieldToFilter('frontend_input','select')
                                     ->addFieldToFilter('apply_to',array($filter_a,$filter_b))
                                     ->addFieldToFilter('frontend_input_renderer',array('null' => true))
                                     ->getData();

        $options = array();
       
        foreach($attributes as $key => $value) {
                $options[] = array('value'=>$value['attribute_id'],'label'=>$value['frontend_label']);
        }
        
        return $options;
    }
   
}
