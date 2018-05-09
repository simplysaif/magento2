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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Controller\Customerrma;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;

class Cancel extends \Ced\CsRma\Controller\Link

{
	/**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */

	public function execute()
	{
		$response = new DataObject();
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		
		$id = $this->getRequest()->getParam('rma_request_id');
		try{
			$model = $this->_objectManager->create('Ced\CsRma\Model\Request');
			$model->load($id)->setData('status','Cancelled')->save();

			$this->messageManager->addSuccess(__('The request status has been changed.'));
			$response->setError(0);
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $response->setError(1);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong  with this request.'));
            $response->setError(1);
        }
       	$resultJson->setData($response->toArray());
        return $resultJson;
	}

}