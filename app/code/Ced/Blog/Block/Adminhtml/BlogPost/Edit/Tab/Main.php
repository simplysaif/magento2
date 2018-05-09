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
 * @package     Ced_Blog
 * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Blog\Block\Adminhtml\BlogPost\Edit\Tab;

/**
 * Blog post edit form main tab
 */

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */

    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\System\Store
     */

    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */

    protected $_wysiwygConfig;

    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_objectManager = $objectInterface;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ced_form_post_data');
        $id = $this->getRequest()->getParam('post_id');
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);

        $isElementDisabled = false;
        $register = $model->getData();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);
        $fieldset->addField(
            'id',
            'hidden',
            [
                'name' => 'id',
                'label' => __('Attribute Id'),
                'title' => __('Id'),
                'required' => false,
                'disabled' => false
            ]
        );
        $collection = $this->_objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection');
        foreach($collection->getData() as $val)
        {
            if($val['attribute_code']=='ip_address')
            {
                $entity_type_id = $val['entity_type_id'];
            }
        }
        $collection = $this->_objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')->setEntityTypeFilter($entity_type_id);
        foreach($collection->getData() as $val)
        {
            if($val['note']==1)
            {
                if($val['attribute_code']=='title')
                {
                    $fieldset->addField(
                        $val['attribute_code'],
                        'text',
                        [
                            'name' => $val['attribute_code'],
                            'label' => __($val['frontend_label']),
                            'title' => __($val['attribute_code']),
                            'required' => true,
                            'disabled' => $isElementDisabled
                        ]
                        ,'^');
                }
                if($val['attribute_code']=='post_text')
                {
                    $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
                    $fieldset->addField(
                        $val['attribute_code'],
                        'textarea',
                        [
                            'name' => $val['attribute_code'],
                            'label' => __($val['frontend_label']),
                            'title' => __($val['attribute_code']),
                            'required' => true,
                            'disabled' => $isElementDisabled,
                            'config' => $wysiwygConfig
                        ],'^'
                    );
                }
                if($val['attribute_code']=='featured_image')
                {
                    if(isset($register['featured_image']))
                    {
                        if($register['featured_image'] == 'ced/blog/post/thumbnail.jpg')
                            $fieldset->addField(
                                $val['attribute_code'],
                                'file',
                                [
                                    'name' => $val['attribute_code'],
                                    'label' => __($val['frontend_label']),
                                    'title' => __($val['attribute_code']),
                                    'required' => true,
                                    'disabled' => $isElementDisabled
                                ]
                            );
                        else
                        {
                            $fieldset->addField(
                                $val['attribute_code'],
                                'image',
                                [
                                    'name' => $val['attribute_code'],
                                    'label' => __($val['frontend_label']),
                                    'title' => __($val['attribute_code']),
                                    'required' => true,
                                    'disabled' => $isElementDisabled
                                ]
                            );
                        }
                    }
                    else {
                        $fieldset->addField(
                            $val['attribute_code'],
                            'file',
                            [
                                'name' => $val['attribute_code'],
                                'label' => __($val['frontend_label']),
                                'title' => __($val['attribute_code']),
                                'required' => true,
                                'disabled' => $isElementDisabled
                            ]
                        );
                    }
                }
                if($val['attribute_code']=='url')
                {
                    $fieldset->addField(
                        $val['attribute_code'],
                        'text',
                        [
                            'name' => $val['attribute_code'],
                            'label' => __($val['frontend_label']),
                            'title' => __($val['attribute_code']),
                            'required' => true,
                            'disabled' => $isElementDisabled
                        ]
                    );
                }
                if($val['attribute_code']=='status')
                {
                    $arr = array('publish'=>'publish','unpublish'=>'unpublish','draft'=>'draft');
                    $fieldset->addField(
                        'blog_status',
                        'select',
                        [
                            'name' => 'blog_status',
                            'label' => __($val['frontend_label']),
                            'title' => __('Post Status'),

                            'required' => true,

                            'disabled' => $isElementDisabled,

                            'values'=>  $arr
                        ]
                    );
                }

                if($val['attribute_code']=='publish_date')
                {
                    $dateFormat = $this->_localeDate->getDateFormat(
                        \IntlDateFormatter::SHORT
                    );
                    $store = $this->_objectManager->create( '\Magento\Framework\App\Config\ScopeConfigInterface');
                    $config_value = $store->getValue('ced_blog/general/blog_date_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,0);
                    $fieldset->addField(
                        $val['attribute_code'],
                        'date',
                        [
                            'name' => 'created_at',
                            'label' => __($val['frontend_label']),
                            'date_format' => $dateFormat,
                            'disabled' => $isElementDisabled,
                            'class' => 'validate-date validate-date-range date-range-custom_theme-from'
                        ]
                    );
                }
            }
            if($val['note']==1)
                if($val['backend_type']=='text')
                {
                    $fieldset->addField(
                        $val['attribute_code'],
                        'text',
                        [
                            'name' => $val['attribute_code'],
                            'label' => __($val['frontend_label']),
                            'title' => __($val['attribute_code']),
                            'required' => false,
                            'disabled' => $isElementDisabled
                        ]
                    );
                }

            if($val['note']==1)
                if($val['backend_type']=='date')
                {
                    $fieldset->addField(
                        $val['attribute_code'],
                        'date',
                        [
                            'name' => $val['attribute_code'],
                            'label' => __($val['frontend_label']),
                            'date_format' => $dateFormat,
                            'disabled' => $isElementDisabled,
                            'class' => 'validate-date validate-date-range date-range-custom_theme-from'
                        ]
                    );
                }
            if($val['note']==1)
                if($val['backend_type']=='Dropdown')
                {
                    $collection = $this->_objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection');
                    foreach($collection->getData() as $val)
                    {
                        if($val['attribute_code']=='ip_address')
                        {
                            $entity_type_id= $val['entity_type_id'];
                        }
                    }
                    $collection = $this->_objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')->setEntityTypeFilter($entity_type_id);
                    foreach($collection->getData() as $data)
                    {
                        if($data['attribute_code']==$val['attribute_code'])
                        {
                            $values =json_decode($val['frontend_class']);
                        }
                    }
                    if(isset($values))
                        foreach($values as $val1)
                        {
                            $ar[$val1] = $val1;
                        }

                    $fieldset->addField(
                        $val['attribute_code'],
                        'select',
                        [
                            'name' => $val['attribute_code'],
                            'label' => __($val['frontend_label']),
                            'disabled' => $isElementDisabled,
                            'values'=> $ar
                        ]
                    );
                }
        }
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        $user = $currentUser->getData();

        /*$usercollection = $this->_objectManager->create('Ced\Blog\Model\User')->getCollection();*/
        $usercollection = $this->_objectManager->create('Ced\Blog\Model\User')->load($user['user_id'],'user_id');
        $val = $usercollection->getData();
        $cat1 = array();
        if(!empty($val))
        {
            if(!empty($val['blog_category']))
            {
                $userCategoryId = explode(',',$val['blog_category']);
                $userCategory = $this->_objectManager->create('Ced\Blog\Model\Blogcat')
                    ->getCollection()->addFieldToFilter('id',array('in'=>$userCategoryId));
                $userCategoryData = $userCategory->getData();
                foreach($userCategoryData as $value)
                {
                    $cat1[] = array('label'=>$value['title'], 'value'=>$value['id']);
                }
            }

        }
        /*codes for validating category*/
        if (empty($cat1)){
            $cat_collection = $this->_objectManager->create('Ced\Blog\Model\Blogcat')->getCollection();
            foreach($cat_collection->getData() as $val){
                $cat1[] = array('label'=>$val['title'], 'value'=>$val['id']);
            }
        }


        if($model->getId())
        {
            $category=json_decode($model->getBlogCategory());
            if(!empty($category)){
                $model->setData('blog_category',implode(",",$category));
            }
        }
        if(!empty($cat1))
        {
            $fieldset->addField(
                'blog_category',
                'multiselect',
                [
                    'name' => 'blog_category[]',
                    'label'=>__('Category'),
                    'title' => __('Category'),
                    'style' => 'height:15em;width:20em',
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'values'=>  $cat1,


                ]
            );
        }

        $fieldset->addField(
            'tags',
            'text',
            [
                'name' => 'tags',
                'label' => __('Tags'),
                'title' => __('tags'),
                'required' => true,
                'note' => __('Enter tags seperated by commas (,)'),
                'disabled' => $isElementDisabled
            ]
        );
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $store=$this->_objectManager->create( '\Magento\Framework\App\Config\ScopeConfigInterface');
        $config_value=$store->getValue('ced_blog/general/blog_date_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,0);
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }



        $form->setValues($model);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     * @return \Magento\Framework\Phrase
     */

    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Prepare title for tab
     * @return \Magento\Framework\Phrase
     */

    public function getTabTitle()
    {

        return __('Post info');
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
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {

        return $this->_authorization->isAllowed($resourceId);
    }
}