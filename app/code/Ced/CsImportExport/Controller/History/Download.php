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
namespace Ced\CsImportExport\Controller\History;

use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory        
    ) {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
    }
    
    
    /**
     * Download backup action
     *
     * @return void|\Magento\Backend\App\Action
     */
    
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('filename');

        /**
 * @var \Magento\ImportExport\Helper\Report $reportHelper 
*/
        $reportHelper = $this->_objectManager->get('Magento\ImportExport\Helper\Report');

        if (!$reportHelper->importFileExists($fileName)) {
            /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/import/index');
            return $resultRedirect;
        }

        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $reportHelper->getReportSize($fileName)
        );

        /**
 * @var \Magento\Framework\Controller\Result\Raw $resultRaw 
*/
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($reportHelper->getReportOutput($fileName));
        return $resultRaw;
    }
}
