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
  * @package   Ced_CsImportExport
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsImportExport\Controller\Export;

use Magento\Backend\App\Action\Context;

use Magento\Framework\Controller\ResultFactory;

class GetFilter extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Get grid-filter of entity attributes action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        $data = $this->getRequest()->getParams();
      
        if ($this->getRequest()->isXmlHttpRequest() && $data) {
            try {
                /**
 * @var \Magento\Framework\View\Result\Layout $resultLayout 
*/
                $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
              
                /**
 * @var $attrFilterBlock \Magento\ImportExport\Block\Adminhtml\Export\Filter 
*/
                // $attrFilterBlock =  $resultLayout->getLayout()->createBlock('Ced\CsImportExport\Block\Export\Filter');
              
                /**
 * @var $export \Magento\ImportExport\Model\Export 
*/
                //$export = $this->_objectManager->create('Magento\ImportExport\Model\Export');
                // $export->setData($data);
                 
                // $export->filterAttributeCollection(
                  //  $attrFilterBlock->prepareCollection($export->getEntityAttributeCollection())
                // ); 
                //$this->getResponse()->setBody($this->resultPageFactory->create(true)->getLayout()->createBlock('Ced\CsImportExport\Block\Export\Filter')->toHtml());
                return $resultLayout;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $this->messageManager->addError(__('Please correct the data sent value.'));
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }
}
