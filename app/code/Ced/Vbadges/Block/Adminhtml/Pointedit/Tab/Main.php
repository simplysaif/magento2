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
namespace Ced\Vbadges\Block\Adminhtml\Pointedit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {
	
	/**
	 *
	 * @param \Magento\Backend\Block\Template\Context $context        	
	 * @param \Magento\Framework\Registry $registry        	
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param array $data        	
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		array $data = []
	) {
		parent::__construct ( $context, $registry, $formFactory, $data );
	}
	
	/**
	 * Prepare form
	 *
	 * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function _prepareForm() {
		$data = $this->_coreRegistry->registry ( 'cedpoints_form_data' );
		$isElementDisabled = false;
		
		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create ();
		
		$form->setHtmlIdPrefix ( 'page_' );
		
		$fieldset = $form->addFieldset ( 'base_fieldset', [ 
				'legend' => __ ( 'Review Points' ) 
		] );
		
		if (isset ( $data )) {
			$fieldset->addField ( 'id', 'hidden', [ 
					'name' => 'id' 
			] );
		}
		
		$fieldset->addField ( 'rating', 'select', [ 
					'name' => 'rating',
					'label' => __ ( 'Rating' ),
					'title' => __ ( 'Rating' ),
					'options' => [
										'0' => __ ( 'Please Select Option' ),
										'1' => __ ( '1 OUT OF 5' ),
										'2' => __ ( '2 OUT OF 5' ),
										'3' => __ ( '3 OUT OF 5' ),
										'4' => __ ( '4 OUT OF 5' ),
										'5' => __ ( '5 OUT OF 5' ) 
								]
			]
		);

		$fieldset->addField ( 'points', 'text', [ 
				'name' => 'points',
				'label' => __ ( 'Points' ),
				'title' => __ ( 'Points' ),
				'required' => true,
				'disabled' => $isElementDisabled 
			]
		);
		
		if (isset ( $data )) {
			$form->setValues ( $data );
		}
		$this->setForm ( $form );
		
		return parent::_prepareForm ();
	}
	
	public function getTabLabel() {
		return __ ( 'Rating Points Information' );
	}
	
	public function getTabTitle() {
		return __ ( 'Rating Points Information' );
	}
	
	public function canShowTab() {
		return true;
	}
	
	public function isHidden() {
		return false;
	}
	
	/**
	 * Check permission for passed action
	 *
	 * @param string $resourceId        	
	 * @return bool
	 */
	protected function _isAllowedAction($resourceId) {
		return $this->_authorization->isAllowed ( $resourceId );
	}
}