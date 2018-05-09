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
namespace Ced\CsMultistepreg\Block\Adminhtml\Rewrites\CsVattribute\Edit\Tab;
use Ced\CsVendorAttribute\Block\Adminhtml\Attributes\Edit\Tab\Main as VattributeMain;
use ArgumentSequence\ParentClass;

class Main extends VattributeMain{
	
	public function _prepareForm(){
		parent::_prepareForm();
		$form = $this->getForm();
		
		$fieldset = $form->getElement('base_fieldset');
		$fieldsetMultistep = $form->addFieldset('registration_step_no_legend', array('legend'=>__('Vendor Multistep Registration Form')));
        
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stepValues = $this->toOptionArray();
        $fieldsetMultistep->addField(
            'registration_step_no', 'select', array(
            'name'      => 'registration_step_no',
            'label'     => __('Step Number'),
            'title'     => __('Step Number'),
            'values'    => $stepValues,
            'note'        => __('Step Number In Multistep Registration Form.'),
            )
        );
        
	}


    public function toOptionArray(){
        $steps = array(''=>__('Please Select'));
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stepsObject = $objectManager->create('Ced\CsMultistepreg\Model\Steps');
        $stepCollection = $stepsObject->getCollection();
        foreach($stepCollection as $step){
            $temp = array();
            $stepNumber = $step->getStepNumber();
            $stepLabel  = $step->getStepLabel();
            $temp['value'] = $stepNumber ;
            $temp['label'] = $stepLabel;
            $steps[] = $temp;
        }
        
        return $steps;
    }
}