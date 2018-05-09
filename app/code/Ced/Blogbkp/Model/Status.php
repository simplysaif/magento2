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
  * @category  Ced
  * @package   Ced_Blog
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\Blog\Model;

class Status implements \Magento\Framework\Option\ArrayInterface
{

    /**#@+
     * Status values
     */

    const STATUS_APPROVED = 1;

    const STATUS_DISAPPROVED = 2;

    const STATUS_PENDING = 3;

    /**
     * Retrieve status options array.
     * @return array
     */

    public function toOptionArray()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }

    /**
     * Retrieve option array
     * @return string[]
     */

    public static function getOptionArray()
    {
        return [self::STATUS_APPROVED => __('Approved'), self::STATUS_DISAPPROVED => __('Disapproved'),self::STATUS_PENDING => __('Pending')];

    }

    /**
     * Retrieve option array with empty value
     * @return string[]
     */

    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }

    /**
     * Retrieve option text by option value
     * @param string $optionId
     * @return string
     */

    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}

