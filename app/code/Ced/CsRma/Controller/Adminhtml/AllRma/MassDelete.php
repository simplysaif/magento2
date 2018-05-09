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
namespace Ced\CsRma\Controller\Adminhtml\AllRma;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;


class MassDelete extends \Magento\Backend\App\Action

{
	/**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */

	public function execute()
	{
		$data = $this->getRequest()->getParams();
		 if (!is_array($data['selected'])) {
            $this->messageManager->addError(__('Please select item(s).'));
        }else{
        	try{
        		foreach ($data['selected'] as $key) {
        			$this->_objectManager->create('Ced\CsRma\Model\Request')
        				->load($key)
        				->delete();
        		}
        		$this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($data['selected']))
                );
        	} catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while deleting these records.'));
            }
        }
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('csrma/*/');
        return $resultRedirect;

	}

}  

