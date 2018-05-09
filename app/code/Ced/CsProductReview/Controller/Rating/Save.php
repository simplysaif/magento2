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
 * @package   Ced_CsProductReview
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\CsProductReview\Controller\Rating;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
use Magento\Framework\Controller\ResultFactory;
class Save extends \Ced\CsMarketplace\Controller\Vendor
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $coreRegistry = null;
	
	/**
	 * @param Action\Context $context
	 * @param Builder $productBuilder
	 * @param Initialization\StockDataFilter $stockFilter
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			Session $customerSession,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			UrlFactory $urlFactory,
			\Magento\Framework\Module\Manager $moduleManager,
			\Magento\Framework\Registry $coreRegistry
	) {
		parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
		$this->coreRegistry = $coreRegistry;
	}
	
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
    	$reultpage = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    	//print_r($this->getRequest()->getPostValue());die;
    	if($this->getRequest()->isPost()){
    		try{
    			
    			$stores = $this->getRequest()->getParam('stores');
    			$ratingModel =$this->_objectManager->create('Ced\CsProductReview\Model\Review')->getCollection()->addFieldToFilter('rating_id',$this->getRequest()->getParam('id'))->addFieldToFilter('vendor_id',$this->_getSession()->getVendorId());
    			foreach ($ratingModel as $rmodel){
    				$rmodel->delete();
    			}
    			$vendorId = $this->_getSession()->getVendorId();
    			$ratingId = $this->getRequest()->getParam('id');
    			foreach ($stores as $key=>$value){
    				$model = $this->_objectManager->create('Ced\CsProductReview\Model\Review');
    				$model->setVendorId($vendorId);
    				$model->setStoreId($value);
    				$model->setRatingId($this->getRequest()->getParam('id'));
    				$model->save();
    			}
    	
    		} catch(\Exception $e){
    			$this->messageManager->addError(__('Something Went Wrong While Saving Rating'));
    			return $reultpage->setPath('productreview/rating/index');
    		}
    	}
    	$this->messageManager->addSuccess(__('The Rating has been saved.'));
    	return $reultpage->setPath('productreview/rating/index');
    	
    }
   
}