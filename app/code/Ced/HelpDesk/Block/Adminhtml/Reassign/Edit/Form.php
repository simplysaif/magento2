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
namespace Ced\HelpDesk\Block\Adminhtml\Reassign\Edit;
 
class Form extends  \Magento\Backend\Block\Widget\Form\Generic
{
    /*
     * @var $_wysiwygConfig
     * */
    protected $_wysiwygConfig;
    /*
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     * */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('assign_form');
        $this->setTitle(__('Assign Ticket'));
    }
    /*
     * prepare form
     * @return $this
     * */
    protected function _prepareForm()
    {
        $priority = [];
        $model = $this->_coreRegistry->registry('ced_ticket_assign');
        if ($model['id']!=null){
        	
        	$form = $this->_formFactory->create(
        			['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/*/saveassign'), 'method' => 'post']]
        			);
        	 
        	
        } else{
        	
        	$form = $this->_formFactory->create(
        			['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/*/saveassign'), 'method' => 'post']]
        			);
        	 
        }
        $agent =[];
        $agentIds = [];
        $userRoles = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $user = $objectManager->create('Magento\Backend\Model\Auth\Session')
                                     ->getUser();
        $currentUser = $user->getData('username');
        $currentUserId = $user->getData('user_id');
        $userRoles = $objectManager->create('Magento\Authorization\Model\Role')->load('Administrators','role_name')->getRoleUsers();
        if (isset($model['department']) && !empty($model['department'])) {
            $departmentModel = $objectManager->create('Ced\HelpDesk\Model\Department')->load($model['department'],'code');
            $agentIds = explode(',',$departmentModel->getAgent());
            $head = explode(',',$departmentModel->getDepartmentHead());
            $ids = array_unique(array_merge($agentIds,$head));
            if ($model['department'] == 'admin') {
                $agentModel = $objectManager->create('Ced\HelpDesk\Model\Agent')->getCollection();
                foreach ($agentModel as $value) {
                    if ($value->getUsername() != $currentUser) {
                        $agent[] = ['value'=>$value->getId().'-'.$value->getUsername(),
                                    'label'=>$value->getUsername()
                                   ];
                    }
                }
            }else{
                $agentModel = $objectManager->create('Ced\HelpDesk\Model\Agent')->getCollection()->addFieldToFilter('id',['in'=>$ids]);
                foreach ($agentModel as $value) {
                    if ($value->getUsername() != $currentUser) {
                        $agent[] = ['value'=>$value->getId().'-'.$value->getUsername(),
                                    'label'=>$value->getUsername()
                                   ];
                    }
                }
            }
        }
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Assign Ticket')]);
        if ($model['id']!=null){
            $agentData = implode('-',[$model['agent'],$model['agent_name']]);
            $fieldset->addField(
                'agent',
                'select',
                [
                    'name' => 'agent',
                    'label' => __('Agent'),
                    'title' => __('Agent'),
                    'required' => true,
                    'values' => $agent,
                    'value' =>  $agentData
                ]
            );
        }
        $headUserId = $objectManager->get('Ced\HelpDesk\Model\Agent')->load($head)->getUserId();
        if ($model['id']!=null && (in_array($currentUserId, $userRoles) || ($currentUserId == $headUserId))){
            $priorityModel = $objectManager->create('Ced\HelpDesk\Model\Priority')->getCollection();
            foreach ($priorityModel as $item) {
                $priority[] = ['label' => $item->getTitle(),'value' => $item->getCode()];
            }
            $fieldset->addField(
                'priority',
                'select',
                [
                    'name' => 'priority',
                    'label' => __('Priority'),
                    'title' => __('Priority'),
                    'required' => true,
                    'values' => $priority,
                    'value' =>  $model['priority']
                ]
            );
        }
        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $fieldset->addField(
            'reassign_description',
            'editor',
            [
                'name' => 'reassign_description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:10em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );
        if ($model['id']!=null){
            $fieldset->addField(
                'id',
                'hidden',
                [
                    'name' => 'id',
                    'value' => $model['id'],
                ]
            );
            $fieldset->addField(
                'ticket_id',
                'hidden',
                [
                    'name' => 'ticket_id',
                    'value' => $model['ticket_id'],
                ]
            );
            $fieldset->addField(
                'from',
                'hidden',
                [
                    'name' => 'from',
                    'value' => $currentUser,
                ]
            );
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}