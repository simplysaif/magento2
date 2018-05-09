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
 * @package     Ced_CsProductReview
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProductReview\Block;

class Rating extends \Magento\Backend\Block\Widget\Grid\Container
{
	
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'rating';
        $this->_blockGroup = 'Ced_CsProductReview';
        $this->_headerText = __('Manage Rating');
        parent::_construct();
        $this->setData('area','adminhtml');
        $this->removeButton('add');
    }
}
