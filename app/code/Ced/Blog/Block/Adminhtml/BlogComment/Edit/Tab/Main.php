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
 * @category  Ced
 * @package   Ced_Blog
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\Blog\Block\Adminhtml\BlogComment\Edit\Tab;

/**
 * Blog post edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return                                        $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ced_form_data');

        $collect=$model->getData();

        $post_id=$collect['post_id'];

        $isElementDisabled = false;
        /**
         *
         *
         * @var \Magento\Framework\Data\Form $form
         */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Comment Details')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'post_title',
            'note',
            [
                'label' => __('Post'),
                'text' => '<a href="' . $this->getUrl(
                        'blog/post/edit',
                        ['id' => $post_id]
                    ) . '" onclick="this.target=\'blank\'">' . $this->escapeHtml(
                        $collect['post_title']
                    ) . '</a>'

            ]
        );
        $fieldset->addField(
            'user',
            'text',
            [
                'name' => 'user',
                'label' => __('User'),
                'title' => __('User'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email Id'),
                'title' => __('Email Id'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $arr_status=array(1=>'Approved',2=>'Disapproved',3=>'Pending');
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options'=>$arr_status
            ]
        );
        if($collect['user_type']==1) {
            $user=$fieldset->addField(
                'user_type',
                'note',
                [
                    'label' => __('Author'),
                    'text'=>'<a href="javascript:;" onclick="getUserInfo()">Customer</a>'
                ]
            );
            $user->setAfterElementHtml(
                "<script type=\"text/javascript\">
          	function getUserInfo(){
        		var user = document.getElementsByName('user')[0].value;
        		var email = document.getElementsByName('email')[0].value;
        		var reloadurl = '". $this->getUrl('blog/category/profile', array('email'=>$collect['email']))."';
        			if(user && email)
	        		 {new Ajax.Request(reloadurl,{
		    			 method: 'post',
        				parameter:{email:email},
        				onComplete: function(data) {
        					var newurl='". $this->getUrl('customer/index/edit')."'+ 'id/'+data.responseText;
    					window.open(newurl);}
	   			} 
	       			);	}		
       }</script>"
            );

        }
        elseif($collect['user_type']==2) {
            $fieldset->addField(
                'user_type',
                'note',
                [
                    'label' => __('Author'),
                    'text' => 'Guest'
                ]
            );
        } else {
            $fieldset->addField(
                'user_type',
                'note',
                [
                    'label' => __('Author'),
                    'text' => 'Adminstrator'
                ]
            );
        }
        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'style' => 'height:15em;',
                'label' => __('Comment'),
                'title' => __('Comment'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
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
        return __('Comment Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Comment Information');
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
     * @param  string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
