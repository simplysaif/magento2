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
class Edit extends \Ced\CsMarketplace\Controller\Vendor
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
    	if(!$this->_objectManager->create('Ced\CsProductReview\Helper\Data')->IsEnable()){
    		 $resultPage = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    		return $resultPage->setPath('csmarketplace/vendor/index');
    	}
        $this->initEnityId();
        /** @var \Magento\Review\Model\Rating $ratingModel */
        $ratingModel = $this->_objectManager->create('Magento\Review\Model\Rating');
        if ($this->getRequest()->getParam('id')) {
            $ratingModel->load($this->getRequest()->getParam('id'));
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        
        $resultPage->getConfig()->getTitle()->prepend(__('Ratings'));
        $resultPage->getConfig()->getTitle()->prepend(
            $ratingModel->getId() ? $ratingModel->getRatingCode() : __('New Rating')
        );
        
       $ratingModel =$this->_objectManager->create('Ced\CsProductReview\Model\Review')->getCollection()->addFieldToFilter('rating_id',$this->getRequest()->getParam('id'))->addFieldToFilter('vendor_id',$this->_getSession()->getVendorId());
      $storeId =[];
      foreach($ratingModel as $_rating){
      	$storeId[] = $_rating->getStoreId();
      }
      $this->coreRegistry->register('store_id',$storeId);
        /* $resultPage->addContent($resultPage->getLayout()->createBlock('Ced\CsProductReview\Block\Rating\Edit'))
            ->addLeft($resultPage->getLayout()->createBlock('Ced\CsProductReview\Block\Rating\Edit\Tabs')); */
        return $resultPage;
    }
    /**
     *
     * @see \Magento\Review\Controller\Adminhtml\Rating::initEnityId()
     */
    protected function initEnityId()
    {
    	$this->coreRegistry->register(
    			'entityId',
    			$this->_objectManager->create('Magento\Review\Model\Rating\Entity')->getIdByCode('product')
    	);
    }
}