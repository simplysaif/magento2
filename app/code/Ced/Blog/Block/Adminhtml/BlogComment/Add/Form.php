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

namespace Ced\Blog\Block\Adminhtml\BlogComment\Add;

/**
 * Adminhtml add product review form
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var objectManager
     */
    protected $_objectManager;

    /**
     * Review data
     *
     * @var \Magento\Review\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Core system store model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param \Magento\Review\Helper\Data             $reviewData
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Review\Helper\Data $reviewData,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_reviewData = $reviewData;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare add review form
     *
     * @return                                        void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /**
         *
         * @var \Magento\Framework\Data\Form $form
         */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('add_review_form', ['legend' => __('Comment Details')]);
        $fieldset->addField('post_title', 'note', ['label' => __('Post Title'),'name'=>'post_title' ,'text' => 'post_title']);
        $fieldset->addField(
            'user',
            'text',
            [
                'name' => 'user',
                'title' => __('User'),
                'label' => __('User'),
                'maxlength' => '50',
                'required' => true
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'title' => __('Email Id'),
                'label' => __('Email Id'),
                'maxlength' => '50',
                'required' => true,
                'class'=>'validate-email'
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
                'options'=>$arr_status
            ]
        );
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $store = $this->_objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $config_value = $store->getValue('ced_blog/general/blog_date_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);

        $fieldset->addField(
            'created_at',
            'date',
            [
                'name' => 'created_at',
                'label' => __('Created Time '),
                'date_format' => $dateFormat,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'title' => __('Comment'),
                'label' => __('Comment'),
                'required' => true
            ]
        );

        $fieldset->addField('user_type', 'hidden', ['name' => 'user_type','value'=>3]);
        $fieldset->addField('post_id', 'hidden', ['name' => 'post_id']);
        $fieldset->addField('id', 'hidden', ['name' => 'id']);
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('blog/comment/admin'));
        $this->setForm($form);
    }
}
