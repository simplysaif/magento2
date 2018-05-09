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

namespace Ced\Blog\Block\Adminhtml\BlogPost\Edit\Tab;

/**
 * Blog post edit form main tab
 */

class Settings extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * @param \Magento\Framework\Registry  $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
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
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ced_form_post_data');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('SEO INFORMATION')]);
        $fieldset->addField(
            'meta_title',
            'text',
            [
                'name' => 'meta_title',
                'label' => __('Meta title'),
                'title' => __('store'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Meta Keywords'),
                'title' => __('keywords'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldset->addField(
            'meta_content',
            'textarea',
            [
                'name' => 'meta_content',
                'label' => __('Meta Description'),
                'title' => __('Sort Order'),
                'note' => __('meta description limit should not be more than 255 '),
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
     * @return \Magento\Framework\Phrase
     */

    public function getTabLabel()
    {
        return __('Meta Description');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */

    public function getTabTitle()
    {
        return __('Description');
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
