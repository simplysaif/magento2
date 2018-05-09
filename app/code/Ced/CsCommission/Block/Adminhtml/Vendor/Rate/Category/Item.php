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
 * @package     Ced_CsCommission
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsCommission\Block\Adminhtml\Vendor\Rate\Category;

class Item extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $this->setExtraParams('style="width: 150px;"');
        if (!$this->getOptions()) {
            $collection = $this->categoryCollectionFactory
                ->create()
                ->addAttributeToSelect(['name'])
                ->addFieldToFilter('entity_id', ['neq' => '1'])
                ->load();
            if ($collection->getSize() > 0) {
                foreach ($collection as $category) {
                    $this->addOption(
                        $category->getId(),
                        $category->getName() != '' ? addslashes($category->getName()) : 'Default Category'
                    );
                }
            }
        }
        return parent::_toHtml();
    }
}
