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
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultistepreg\Block\Adminhtml\Rewrites\CsVattribute;
use Ced\CsVendorAttribute\Block\Adminhtml\Attributes as VattributeGridContainer;

class Attributes extends  VattributeGridContainer{
	
	public function __construct(
			\Magento\Backend\Block\Widget\Context $context,
			array $data = []
	) {
		parent::__construct($context, $data);
		$this->getAddStepButtonOptions();
	}
	/* 
	/**
	 * Prepare button and gridCreate Grid , edit/add grid row and installer in Magento2
	 *
	 * @return \Magento\Catalog\Block\Adminhtml\Product
	 */
	
	public function getAddStepButtonOptions(){
	
		$addStepButtonOptions = [
		'label' => __('Add Registration Step'),
		'class' => 'primary',
		'onclick' => "setLocation('" . $this->_getCreateStepUrl() . "')"
				];
		$this->buttonList->add('registrationstep', $addStepButtonOptions);
	}
	
	/**
	 *
	 *
	 * @param string $type
	 * @return string
	 */
	protected function _getCreateStepUrl()
	{
		return $this->getUrl(
				'csmultistep/*/newStep'
		);
	}
	
}