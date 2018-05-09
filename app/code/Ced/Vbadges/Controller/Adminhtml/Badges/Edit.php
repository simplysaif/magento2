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
 * @package     Ced_Vbadges
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Vbadges\Controller\Adminhtml\Badges;

class Edit extends \Magento\Backend\App\Action {
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;
	
	/**
	 *
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;
	
	/**
	 *
	 * @param Action\Context $context        	
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory        	
	 * @param \Magento\Framework\Registry $registry        	
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
		\Magento\Framework\Registry $registry
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		parent::__construct ( $context );
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 */
	protected function _isAllowed() {
		return true;
	}
	
	protected function _initAction() {
		$resultPage = $this->resultPageFactory->create ();
		return $resultPage;
	}
	
	public function execute() {
		$id = $this->getRequest ()->getParam ( "id" );
		$model = $this->_objectManager->create ( 'Ced\Vbadges\Model\Badges' );
		if ($id) {
			$model->load( $id );
		}
		$this->_coreRegistry->register ( 'cedbadges_form_data', $model );
		$resultPage = $this->_initAction ();
		$resultPage->addBreadcrumb ( $id ? __ ( 'Edit Badges' ) : __ ( 'New Badge' ), $id ? __ ( 'Edit Badges' ) : __ ( 'New Badge' ) );
		$resultPage->getConfig ()->getTitle ()->prepend ( $model->getId () ? $model->getBadgeName () : __ ( 'Add Badge' ) );
		return $resultPage;
	}
}