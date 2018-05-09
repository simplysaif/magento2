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
namespace Ced\CsMultistepreg\Block\Adminhtml\Steps\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;

class Form extends Generic //implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
	/**
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$form = $this->_formFactory->create(
				[
					'data' => [
					'id'    => 'edit_form',
					'action' => $this->getData('action'),
					'method' => 'post'
					]
				]
		);
		$form->setUseContainer(true);
		$this->setForm($form);
		
		return parent::_prepareForm();
		
		
		
		
		//return parent::_prepareForm();
		/**
		 * @var DataForm $form
		 */
		/* $form = $this->_formFactory->create(
				['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post','enctype' => 'multipart/form-data']]
		);
		$form->setUseContainer(true);
		$fieldset1 = $form->addFieldset(
				'add_step',
				array('legend'=>'Step Details')
		);
		$stepLabelField = $fieldset1->addField('options', 'text', array(
				'name'      => 'options',
				'label'     => 'options',
				'required'  => true,
		));
		$stepLabelField = $form->getElement('options'); */
		
		/* $stepLabelField = $fieldset1->addField('step_label', 'text', array(
				'name'      => 'label',
				'label'     => 'Step Label',
				'required'  => true,
		));
		$stepNumberField = $fieldset1->addField('step_number', 'text',array(
				'name' 		=> 'step_number',
				'label'     => 'Step Number',
				'required'	=> true
		)); */
		/* $stepLabelField->setRenderer(
				$this->getLayout()->createBlock(
						'\Ced\CsMultistepreg\Block\Adminhtml\Steps\Edit\Renderer\Options'
				)
		); */
		/* $this->setForm($form);
		return parent::_prepareForm(); */
	}
	
}
