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
 * @package     Ced_CsDeal
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Block;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
       // parent::_construct();
		
        $this->_objectId = 'import_id';
        $this->_blockGroup = 'Ced_CsDeal';
        $this->_controller = 'deal';
        $this->setId('deal_edit');
    }

    public function getHeaderText()
    {
        return __('Import');
    }
	
	
	protected function _prepareLayout()
    {
    	$this->setTemplate('Ced_CsDeal::csdeal/edit.phtml');
        return parent::_prepareLayout();
    }
	
}