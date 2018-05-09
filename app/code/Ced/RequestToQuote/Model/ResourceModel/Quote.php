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
	
namespace Ced\RequestToQuote\Model\ResourceModel;
	 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
	 
class Quote extends AbstractDb
{
	    /**
	     * Define main table
	     */
	protected function _construct()
	{

	        $this->_init('ced_requestquote','quote_id');

	 }
}