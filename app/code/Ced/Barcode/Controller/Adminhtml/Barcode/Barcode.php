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
  * @package     Ced_Barcode
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\Barcode\Controller\Adminhtml\Barcode;
use Magento\Customer\Model\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Filesystem\DirectoryList;


class Barcode extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
   
 
    protected $_coreRegistry;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
		FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
		\Magento\Framework\Registry $registry,
    	\Magento\Ui\Component\MassAction\Filter $filter
    ) {
    	
	    $this->_fileFactory = $fileFactory;
        $this->resultForwardFactory = $resultForwardFactory;
		$this->_coreRegistry = $registry;
		$this->filter = $filter;
        parent::__construct($context);
    }
	
    /**
     * Index action
     *
     * @return pdf of Barcode
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
    	if(isset($params['id']))
    	{
    		
    		$data=[];
    		$data[] = $params['id'];
    		$pdf = $this->_objectManager->get(
    				'Ced\Barcode\Model\Barcode'
    		)->getPdf($data);
    	}
    	else{
    		$collection = $this->filter->getCollection($this->_objectManager->create(
    				'Magento\Catalog\Model\Product')->getCollection());
    		$ids = $collection->getAllIds();
    		$pdf = $this->_objectManager->get(
    				'Ced\Barcode\Model\Barcode'
    		)->getPdf($ids);
    	
    	}
    
    	
    	$date = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');
    	return $this->_fileFactory->create(
    			'barcode' . $date . '.pdf',
    			$pdf->render(),
    			DirectoryList::VAR_DIR,
    			'application/pdf'
    	);
    	
    
        
    }
}