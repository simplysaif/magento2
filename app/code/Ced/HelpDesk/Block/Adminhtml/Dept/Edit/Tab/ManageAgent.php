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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Block\Adminhtml\Dept\Edit\Tab;

class ManageAgent extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /*
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     *
     * */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /*
     * prepare form
     * @return $this
     * */

    protected function _prepareForm()
    {   
        $agent = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $agentData = $objectManager->create('Ced\HelpDesk\Model\Agent')->getCollection();
        $model = $this->_coreRegistry->registry('ced_department');
        $form = $this->_formFactory->create(); 
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Manage Agent')]);
        foreach ($agentData as $agentvalue) {
           $agent[] = [
                    'label'=>$agentvalue->getUsername(),
                    'value'=>$agentvalue->getId()
                    ];
        }
        $fieldset->addField(
            		'agent',
            		'multiselect',
            		[
            			'name' => 'agent[]',
            			'label' => __('Agent'),
            			'title' => __('Agent'),
                        'required' => true,
                        'values' => $agent
            		]
        		);
         $fieldset->addField(
                    'department_head',
                    'select',
                    [
                        'name' => 'department_head',
                        'label' => __('Head of Department'),
                        'title' => __('Head of Department'),
                        'required' => true,
                        'values' => $agent
                    ]
                );
        
        
        if ($model = $this->_coreRegistry->registry('ced_department')) {
        	$fieldset->addField('id', 'hidden', ['name' => 'id']);
        	$form->setValues($model);
        }
        
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
 
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Image');
    }
 
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Image');
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
 
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}