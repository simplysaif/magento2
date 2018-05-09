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
 * @package     Ced_CsMembership
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMembership\Model;

class Status extends \Magento\Framework\DataObject
{
    const STATUS_ENABLED    = 0;
    const STATUS_DISABLED   = 1;
    const STATUS_RUNNING    = 'running';
    const STATUS_EXPIRED    = 'expired';
    
    /**
     *getOptionArray
     *return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => __('Yes'),
            self::STATUS_DISABLED   => __('No')
        );
    }
    
}
