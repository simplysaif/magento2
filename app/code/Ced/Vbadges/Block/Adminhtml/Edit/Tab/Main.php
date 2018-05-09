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
namespace Ced\Vbadges\Block\Adminhtml\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

	const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
	
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
		\Magento\Framework\ObjectManagerInterface $objectManager,
		array $data = []
	) {
		parent::__construct ( $context, $registry, $formFactory, $data );
		$this->_objectManager = $objectManager;
	}
	
	/**
	 * Prepare form
	 *
	 * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function _prepareForm() {
		$data = $this->_coreRegistry->registry ( 'cedbadges_form_data' );
		$isElementDisabled = false;
		
		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create ();
		
		$form->setHtmlIdPrefix ( 'page_' );
		
		$fieldset = $form->addFieldset ( 'base_fieldset', [ 
				'legend' => __ ( 'Badges' ) 
		] );
		
		if (isset ( $data )) {
			$fieldset->addField ( 'badges_id', 'hidden', [ 
					'name' => 'badges_id' 
			] );
		}
		
		$fieldset->addField ( 'badge_name', 'text', [ 
				'name' => 'badge_name',
				'label' => __ ( 'Badge Name' ),
				'title' => __ ( 'Badge Name' ),
				'required' => true,
				'disabled' => $isElementDisabled 
		] );
		$fieldset->addField ( 'badge_description', 'text', [ 
				'name' => 'badge_description',
				'label' => __ ( 'Badge Description' ),
				'title' => __ ( 'Badge Description' ),
				'required' => true,
				'disabled' => $isElementDisabled 
		] );

		$fieldset->addField ( 'badge_image', 'image', [ 
				'name' => 'badge_image',
				'label' => __ ( 'Image' ) 
		] );
		$fieldset->addField ( 'badge_status', 'select', [ 
				'name' => 'badge_status',
				'label' => __ ( 'Status' ),
				'title' => __ ( 'Status' ),
				'required' => true,
				'values' => $this->_objectManager->create('\Ced\Vbadges\Model\Badge\Source\Status')->getOptionArray()
		] );
		$fieldset->addField ( 'sales', 'text', [ 
				'name' => 'sales',
				'label' => __ ( 'No. Of Sales' ),
				'title' => __ ( 'No. Of Sales' ),
				'required' => true,
				'disabled' => $isElementDisabled 
		] );
		
		if (isset ( $data )) {
			if ($data ["badge_image"] == "") {
				$data ["badge_image"] = "default.png";
			}
			$data->setBadgeImage ( "badge/" . $data ["badge_image"] );
			$form->setValues ( $data );
		}
		$this->setForm ( $form );
		
		return parent::_prepareForm ();
	}
	
	/**
	 * Prepare label for tab
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getTabLabel() {
		return __ ( 'Badge Information' );
	}
	
	/**
	 * Prepare title for tab
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getTabTitle() {
		return __ ( 'Badge Information' );
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 */
	public function canShowTab() {
		return true;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 */
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