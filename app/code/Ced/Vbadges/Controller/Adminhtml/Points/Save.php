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
namespace Ced\Vbadges\Controller\Adminhtml\Points;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action {
	
	/**
	 * @param Action\Context $context        	
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context
	) {
		parent::__construct ( $context );
	}
	
	/**
	 * Save action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$data = $this->getRequest ()->getPostValue ();
		$resultRedirect = $this->resultRedirectFactory->create ();
		if ($data) {
			$model = $this->_objectManager->create ( 'Ced\Vbadges\Model\Points' );
			$id = $this->getRequest ()->getParam ( 'id' );
			if ($id) {
				$model->load ( $id );
			}
				$model->setData ( 'rating', $data["rating"] );
				$model->setData ( 'points', $data ['points'] );
			
			try {
				$model->save ();
				$this->messageManager->addSuccess ( __ ( 'Rating points successfully saved.' ) );
				$this->_objectManager->get ( 'Magento\Backend\Model\Session' )->setFormData ( false );
				if ($this->getRequest ()->getParam ( 'back' )) {
					return $resultRedirect->setPath ( '*/points/edit', [ 
							'grid_record_id' => $model->getId (),
							'_current' => true 
					] );
				}
				return $resultRedirect->setPath ( 'csvendorbadges/points/view' );
			} catch ( \Magento\Framework\Exception\LocalizedException $e ) {
				$this->messageManager->addError ( $e->getMessage () );
			} catch ( \RuntimeException $e ) {
				$this->messageManager->addError ( $e->getMessage () );
			} catch ( \Exception $e ) {
				$this->messageManager->addException ( $e, __ ( 'Something went wrong while saving the data.' ) );
			}
			
			$this->_getSession ()->setFormData ( $data );
			return $resultRedirect->setPath ( '*/points/edit', [ 
					'id' => $this->getRequest ()->getParam ( 'id' ) 
			] );
		}
		return $resultRedirect->setPath ( 'csvendorbadges/points/view' );
	}
}