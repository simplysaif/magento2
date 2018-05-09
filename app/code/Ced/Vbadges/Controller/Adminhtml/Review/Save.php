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
namespace Ced\Vbadges\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action {
	
	public function __construct(
		\Magento\Backend\App\Action\Context $context, 
		\Magento\Framework\Filesystem $media, 
		\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory, 
		\Magento\Framework\Stdlib\DateTime\DateTime $datetime, 
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	) {
		parent::__construct ( $context );
		$this->_filesystem = $media;
		$this->resultPageFactory = $resultPageFactory;
		$this->timezone = $timezone;
		$this->datetime = $datetime;
		$this->_fileUploaderFactory = $fileUploaderFactory;
	}
	
	/**
	 * Save action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$data = $this->getRequest ()->getPostValue ();
		$deletecheck = $this->getRequest ()->getPostValue ( "badge_image" );
		$resultRedirect = $this->resultRedirectFactory->create ();
		if ($data) {
			$model = $this->_objectManager->create ( 'Ced\Vbadges\Model\Review' );
			$date = $this->datetime->date ( 'Y-m-d' );
			$id = $this->getRequest ()->getParam ( 'badges_id' );
			if ($id) {
				$model->load ( $id );
			}
			// upload file begins
			try {
				$uploader = $this->_fileUploaderFactory->create ( [ 
						'fileId' => 'badge_image' 
				] );
				
				$uploader->setAllowedExtensions ( [ 
						'jpg',
						'jpeg',
						'gif',
						'png' 
				] );
				
				$uploader->setAllowRenameFiles ( false );
				
				$uploader->setFilesDispersion ( false );
				
				$path = $this->_filesystem->getDirectoryRead ( "media" )->

				getAbsolutePath ( 'badge' );
				$result = $uploader->save ( $path );
			} catch ( \Exception $e ) {
				$result ["file"] = "";
				if (isset ( $deletecheck ['delete'] ) && $deletecheck ['delete'] == 1) {
					$model->setData ( 'badge_image', $result ["file"] );
				}
			}
			if ($result) {
				$model->setData ( 'badge_name', $data ["badge_name"] );
				if ($result ["file"] != "") {
					$model->setData ( 'badge_image', $result ["file"] );
				}
				$model->setData ( 'created_at', $date );
				$model->setData ( 'badge_status', $data ['badge_status'] );
				$model->setData ( 'updated_at', $date );
				$model->setData ( 'points', $data ['points'] );
			}
			try {
				$model->save ();
				$this->messageManager->addSuccess ( __ ( 'Badge successfully saved.' ) );
				$this->_objectManager->get ( 'Magento\Backend\Model\Session' )->setFormData ( false );
				if ($this->getRequest ()->getParam ( 'back' )) {
					return $resultRedirect->setPath ( '*/review/edit', [ 
							'grid_record_id' => $model->getId (),
							'_current' => true 
					] );
				}
				return $resultRedirect->setPath ( 'csvendorbadges/review/badgesview' );
			} catch ( \Magento\Framework\Exception\LocalizedException $e ) {
				$this->messageManager->addError ( $e->getMessage () );
			} catch ( \RuntimeException $e ) {
				$this->messageManager->addError ( $e->getMessage () );
			} catch ( \Exception $e ) {
				$this->messageManager->addException ( $e, __ ( 'Something went wrong while saving the data.' ) );
			}
			
			$this->_getSession ()->setFormData ( $data );
			return $resultRedirect->setPath ( '*/review/edit', [ 
					'id' => $this->getRequest ()->getParam ( 'id' ) 
			] );
		}
		return $resultRedirect->setPath ( 'csvendorbadges/review/badgesview' );
	}
}