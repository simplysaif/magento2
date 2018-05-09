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
  * @license   http://cedcommerce.com/license-agreement.txt
  */

 namespace Ced\Blog\Block\Adminhtml\BlogPost\Edit;

 use Magento\Backend\Block\Template\Context;
 use Magento\Backend\Model\Auth\Session;
 use Magento\Framework\Json\EncoderInterface;

 
/**
 * Admin page left menu
 */

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @return void
     */

    protected function _construct()
    {
        parent::_construct();
        $this->setId('attribute_id');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Post Information'));
    }

    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
        ) 
    {

        parent::__construct($context, $jsonEncoder, $authSession, $data);
        $this->_objectManager = $objectManager;
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'SEO', array(
                'label'     => __('SEO information'),
                'title'     => __('SEO information'),
                'content'   => $this->getLayout()->createBlock('Ced\Blog\Block\Adminhtml\BlogPost\Edit\Tab\Settings')->toHtml(),
                )
            );
        $this->addTab(
           'Profile', array(
            'label'     => __('Related Products'),
            'url' => $this->getUrl('blog/post/relatedproduct', ['_current' => true]),
            'class' => 'ajax'
            )
           );

        return parent::_beforeToHtml();
    }



    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
