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
 * @package     Ced_Vbadges
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Vbadges\Model\ResourceModel\Vendorbadges;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
     * @var string
     */
    protected $_idFieldName = 'id';

	public function _construct() {
		$this->_init ( 'Ced\Vbadges\Model\Vendorbadges', 'Ced\Vbadges\Model\ResourceModel\Vendorbadges' );
	}
}