<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Blog
 * @author   	CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
     
namespace Ced\Blog\Block\Adminhtml;

class Blog extends \Magento\Backend\Block\Widget\Container
{
	/**
	 * @var string
	 */
	protected $_template = 'grid/view.phtml';

	/**
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param array $data
	 */
	public function __construct(
			\Magento\Backend\Block\Widget\Context $context,
			array $data = []
	) {
		parent::__construct($context, $data);
		$this->_getAddButtonOptions();
	}

	/**
	 * Prepare button and gridCreate Grid , edit/add grid row and installer in Magento2
	 *
	 * @return \Magento\Catalog\Block\Adminhtml\Product
	 */
	protected function _prepareLayout()
	{
		$this->setChild(
				'grid',
				$this->getLayout()->createBlock('Ced\Blog\Block\Adminhtml\Blog\Grid', 'grid.view.grid')
		);
		
		return parent::_prepareLayout();
	}

	/**
	 * @return array
	 */
	protected function _getAddButtonOptions()
	{
		 $splitButtonOptions = [
		'label' => __('Add New Attribute'),
		'class' => 'primary',
		'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
				];
		$this->buttonList->add('add', $splitButtonOptions);
	}

	/**
	 * @param string $type
	 * @return string
	 */
	protected function _getCreateUrl()
	{
		return $this->getUrl(
				'blog/*/new'
		);
	}

	/**
	 * Render grid
	 *
	 * @return string
	 */
	public function getGridHtml()
	{
		return $this->getChildHtml('grid');
	}
}