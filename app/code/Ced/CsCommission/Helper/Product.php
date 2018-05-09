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
 * Core helper Product
 *
 * @category    Ced
 * @package     Ced_CsCommission
 * @author        CedCommerce Core Team <coreteam@cedcommerce.com>
 */

namespace Ced\CsCommission\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Serialize;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{

    const CONFIG_DB_PRODUCT_USAGE_OPTIONS = 'ced_vpayments/general/commission_pt';

    const OPTION_PRODUCT_PREFIX = '';

    const OPTION_PRODUCT_PREFIX_SEPARATOR = '';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registerInterface
     * @param Serialize $serialize
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registerInterface,
        Serialize $serialize = null
    ) {
        $this->_coreRegistry = $registerInterface;
        $this->serialize = $serialize ?: ObjectManager::getInstance()->get(Serialize::class);
        parent::__construct($context);
    }

    public function getCodeValue($types = 'all')
    {
        return self::OPTION_PRODUCT_PREFIX . self::OPTION_PRODUCT_PREFIX_SEPARATOR . $types;
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
                if (!isset($uniqueValues[$this->getCodeValue($val['types'])])) {
                    $uniqueValues[$this->getCodeValue($val['types'])] = $val;
                } elseif (isset($uniqueValues[$this->getCodeValue($val['types'])]) &&
                    isset($uniqueValues[$this->getCodeValue($val['types'])]['priority']) &&
                    isset($val['priority']) &&
                    (int)$val['priority'] < (int)$uniqueValues[$this->getCodeValue($val['types'])]['priority']
                ) {
                    $uniqueValues[$this->getCodeValue($val['types'])] = $val;
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

    public function getUnserializedOptions($vendorId = null, $storeId = null)
    {
        $arr = [];
        if ($vendorId != null) {
            $value = $this->scopeConfig->getValue(
                $vendorId . '/' . self::CONFIG_DB_PRODUCT_USAGE_OPTIONS,
                ScopeInterface::SCOPE_STORE
            );
        } else {
            $value = $this->scopeConfig->getValue(
                self::CONFIG_DB_PRODUCT_USAGE_OPTIONS,
                ScopeInterface::SCOPE_STORE
            );
        }

        if ($value == null) {
            $value = $this->scopeConfig->getValue(
                self::CONFIG_DB_PRODUCT_USAGE_OPTIONS,
                ScopeInterface::SCOPE_STORE
            );
        }
        if ($value != '') {
            $arr = $this->serialize->unserialize($value);
        }

        if (empty($arr)) {
            return [];
        }

        $sortOrder = [];
        $cnt = 1;
        foreach ($arr as $k => $val) {
            if (!is_array($val)) {
                unset($arr[$k]);
                continue;
            }
            $sortOrder[$k] = isset($val['priority']) ? $val['priority'] : $cnt++;
        }
        //sort by priority
        array_multisort($sortOrder, SORT_ASC, $arr);

        return $arr;
    }

    public function getOptions($storeId = null)
    {
        $rawOptions = $this->getUnserializedOptions($storeId);
        $options = [];
        if (!empty($rawOptions)) {
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
