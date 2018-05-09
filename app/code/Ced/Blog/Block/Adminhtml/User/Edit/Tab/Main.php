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
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Blog\Block\Adminhtml\User\Edit\Tab;

/**
 * user  page edit form main tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_coreRegistry=null;
    const CURRENT_USER_PASSWORD_FIELD = 'current_password';
    protected $_authSession;
    protected $objectManager;
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_LocaleLists;

    /**
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Registry              $registry
     * @param \Magento\Framework\Data\FormFactory      $formFactory
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        $this->_coreRegistry     = $registry;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_authSession = $authSession;
        $this->_LocaleLists = $localeLists;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return                                        \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('permissions_user');
        $user_model = $this->objectManager->create('Ced\Blog\Model\User');
        $collections = $user_model->getCollection()->addFieldToFilter('user_id', $model->getUserId());
        foreach($collections as $coll){
            $user_model->load($coll->getCategoryId());
            break;
        }
        $data = $user_model->getData();
        $blog_cat = isset($data['blog_category']) ? explode(',', $data['blog_category']) : array();
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Blog Category')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'about',
            'textarea',
            [
                'name' => 'about',
                'style' => 'height:10em;',
                'title' => __('about'),
                'label' => __('About Author'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
        $fieldset->addField(
            'profile',
            'image',
            [
                'name' => 'profile',
                /* 'label' => $user_model->getProfile() ? __('Profile Image').' '. $user_model->getProfile() : __('Profile Image'), */
                'label'=>  __('Profile Image'),
                'title' => __('Profile Image'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'blog_category',
            'multiselect',
            [
                'name' => 'blog_category',
                'style' => 'height:10em;width:10em',
                'label' => __('Blog Category'),
                'title' => __('Blog Category'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'values' => $this->_getBlogCategoryOptions()
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }
        $model->setProfile($user_model->getProfile());

        $model->setAbout($user_model->getAbout());

        $model->setBlogCategory($user_model->getBlogCategory());

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
    protected function _getBlogCategoryOptions()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Ced\Blog\Model\Blogcat')->getCollection();
        /* ->addFieldToSelect('title');*/
        $category=array();
        foreach ($collection as $cat)
        {
            $category[] = array('label'=>$cat['title'], 'value'=>$cat['id']);
        }
        return $category;
    }

}
