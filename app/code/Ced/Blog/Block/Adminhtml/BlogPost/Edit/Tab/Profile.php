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

class Profile extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * @param \Magento\Store\Model\System\Store  $systemStore
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getValues()
    {
        $collection = $this->_productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', '1')
            ->addAttributeToFilter('visibility', '4');
        foreach($collection as $key => $value){
            $arr[$value->getId()] = $value->getName();
        }

        foreach ($arr as $key=>$val){
            $options[]= array('value'=>$key, 'label'=>$val);
        }
        return $options;
    }

    /**
     * Prepare form
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ced_form_post_data');
        $arr = $model->getData();
        $isElementDisabled = false;
        if(isset($arr['product_id'])) {
            $jdata = json_decode($arr['product_id']);
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Select Product')]);
        $fieldset->addField(
            'product_id',
            'multiselect',
            [
                'label' => __('Product Name'),
                'title' => __('Product Name'),
                'required' => false,
                'name' => 'product_id',
                'disabled' => $isElementDisabled,
                'values'=>$this->getValues()
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        if(isset($jdata)) {
            $model->setProductId($jdata);
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
        return __('Profile Image');

    }

    /**
     * Prepare title for tab
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
     * @param string $resourceId
     * @return bool
     */

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);

    }
}
