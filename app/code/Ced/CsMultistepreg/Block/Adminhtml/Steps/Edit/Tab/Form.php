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
namespace Ced\CsMultistepreg\Block\Adminhtml\Steps\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;


class Form extends Generic implements TabInterface
{
	
	/**
	 * @param Context $context
	 * @param Registry $registry
	 * @param FormFactory $formFactory
	 * @param Config $wysiwygConfig
	 * @param Status $newsStatus
	 * @param array $data
	 */
	public function __construct(
			Context $context,
			Registry $registry,
			FormFactory $formFactory,
			Config $wysiwygConfig,
			array $data = []
	) {
		parent::__construct($context, $registry, $formFactory, $data);
	}

	/**
	 * Prepare form fields
	 *
	 * @return \Magento\Backend\Block\Widget\Form
	 */
	protected function _prepareForm()
	{

		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create();
		$form->setHtmlIdPrefix('steps_');
		$form->setFieldNameSuffix('steps');
		
		$fieldset1 = $form->addFieldset(
            'add_step',
            array('legend'=>'Step Details')
        );
        $optionsField = $fieldset1->addField('options', 'text', array(
            'name'      => 'options',
            'label'     => 'Options',
            'required'  => false,
        ));
        $optionsField = $form->getElement('options');
     	$optionsField->setRenderer(
            $this->getLayout()->createBlock(
                'Ced\CsMultistepreg\Block\Adminhtml\Steps\Edit\Renderer\Options'
            )
        );
		
		
		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return 'Step Details';
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return 'Step Details';
	}

	/**
	 * {@inheritdoc}
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isHidden()
	{
		return false;
	}
}