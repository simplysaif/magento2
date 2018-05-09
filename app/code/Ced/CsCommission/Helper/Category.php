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
 * @package     Ced_CsCommission
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

/**
 * Core helper Category
 *
 * @category    Ced
 * @package     Ced_CsCommission
 * @author        CedCommerce Core Team <coreteam@cedcommerce.com>
 */

namespace Ced\CsCommission\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;

class Category extends AbstractHelper
{
    const CONFIG_DB_CATEGORY_USAGE_OPTIONS = 'ced_vpayments/general/commission_cw';

    const OPTION_CATEGORY_PREFIX = '';

    const OPTION_CATEGORY_PREFIX_SEPARATOR = '';

    protected $_collectionFactory;
    protected $_objectManager;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registerInterface
     * @param Serialize $serialize
     */
    public function __construct(
        Context $context,
        Registry $registerInterface,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsCommission\Model\ResourceModel\Commission\Collection $collectionFactory,
        Serialize $serialize = null
    ) {
        $this->_coreRegistry = $registerInterface;
        $this->_objectManager = $objectManager;
        $this->serialize = $serialize ?: ObjectManager::getInstance()->get(Serialize::class);
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function getCodeValue($category = 'all')
    {
        return self::OPTION_CATEGORY_PREFIX . self::OPTION_CATEGORY_PREFIX_SEPARATOR . $category;
    }

    public function getSerializedOptions($value)
    {
        $uniqueValues = [];
        if (is_array($value)) {
            $cnt = 0;
            foreach ($value as $key => $val) {
                if (!is_array($val)) {
                    continue;
                }
                if (isset($val['method']) && !in_array($val['method'], ['fixed', 'percentage'])) {
                    $val['method'] = 'fixed';
                }
                switch ($val['method']) {
                    case "fixed":
                        $val['fee'] = round($val['fee'], 2);
                        break;
                    case "percentage":
                        $val['fee'] = min((int)$val['fee'], 100);
                        break;
                }
                if (isset($val['priority']) && !is_numeric($val['priority'])) {
                    $lengthPriority = strlen($val['priority']);
                    if ($lengthPriority > 0) {
                        $val['priority'] = (int)$val['priority'];
                    } else {
                        $val['priority'] = $cnt;
                    }
                }

                if (!isset($uniqueValues[$this->getCodeValue($val['category'])])) {
                    $uniqueValues[$this->getCodeValue($val['category'])] = $val;
                } elseif (isset($uniqueValues[$this->getCodeValue($val['category'])]) &&
                    isset($uniqueValues[$this->getCodeValue($val['category'])]['priority']) &&
                    isset($val['priority'])
                    && (int)$val['priority'] < (int)$uniqueValues[$this->getCodeValue($val['category'])]['priority']) {
                    $uniqueValues[$this->getCodeValue($val['category'])] = $val;
                }
                $cnt++;
            }
        }
        if ($uniqueValues != '') {
            return $this->serialize->serialize($uniqueValues);
        } else {
            return '';
        }
    }

    public function getUnserializedOptions($vendorId = 0, $storeId = 0)
    {
        $arr = [];
        //var_dump($storeId);die;
        
        if($storeId){
        	$type = 'store';
            $collection =$this->_collectionFactory->load();
	        $collection->addFieldToFilter('type',$type);
	        $collection->addFieldToFilter('type_id',$storeId);
	        $collection->addFieldToFilter('vendor',$vendorId);
	        $collection->setOrder('priority','ASC');
	        if($collection->count()==0)
        	{
        		$storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		        $store = $storeManager->getStore(1);
		        $websiteId = $store->getWebsiteId();
		        $collection->addFieldToFilter('type','website');
		        $collection->addFieldToFilter('type_id',$websiteId);
		        $collection->addFieldToFilter('vendor',$vendorId);
		        $collection->setOrder('priority','ASC');
        		/* search for website*/
        	}

        	if($collection->count()==0)
        	{
        		/* search for default value of vendor */
        		$collection = $this->_objectManager->create('Ced\CsCommission\Model\ResourceModel\Commission\Collection')->load();
        		$collection->addFieldToFilter('type','default');
		        $collection->addFieldToFilter('type_id',0);
		        $collection->addFieldToFilter('vendor',$vendorId);
		        $collection->setOrder('priority','ASC');
        	}
        }
        else
        {
            $type = 'default';
            $collection =$this->_collectionFactory->load();
	        $collection->addFieldToFilter('type',$type);
	        $collection->addFieldToFilter('type_id',$storeId);
	        $collection->addFieldToFilter('vendor',$vendorId);
	        $collection->setOrder('priority','ASC');
        }
        
        if($collection->count()==0)
    	{
    		$collection =$this->_objectManager->create('Ced\CsCommission\Model\ResourceModel\Commission\Collection')->load();
    		$collection->addFieldToFilter('type','default');
	        $collection->addFieldToFilter('type_id',0);
	        $collection->addFieldToFilter('vendor',0);
	        $collection->setOrder('priority','ASC');
    	}
        $data = [];
        if ($collection->count()>0) {
        	$collection = $collection->toArray();
	        foreach($collection['items'] as $commission){
	            $data[$commission['category']] = $commission;
	        }
	    }
        return $data;
    }

    public function getOptions($storeId = null)
    {
        $rawOptions = $this->getUnserializedOptions($storeId);
        return $rawOptions;
        $options = [];
        if (is_array($rawOptions) && !empty($rawOptions)) {
            foreach ($rawOptions as $option) {
                $options[$option['code']] = $option;
            }
        }
        return $options;
    }

    public function getDefaultOption($storeId = null)
    {
        $options = $this->getUnserializedOptions($storeId);
        //last one set as default
        foreach ($options as $k => $val) {
            if ($val['default'] == 1) {
                return $val;
            }
        }
        return [];
    }
}
