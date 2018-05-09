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
 
namespace Ced\CsProductReview\Controller\Review;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\RatingFactory;
class MassDelete extends \Ced\CsMarketplace\Controller\Vendor
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $coreRegistry = null;
	
	/**
	 * Review model factory
	 *
	 * @var \Magento\Review\Model\ReviewFactory
	 */
	protected $reviewFactory;
	
	/**
	 * Rating model factory
	 *
	 * @var \Magento\Review\Model\RatingFactory
	 */
	protected $ratingFactory;
	
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
			ReviewFactory $reviewFactory,
			RatingFactory $ratingFactory,
			\Magento\Framework\Registry $coreRegistry
	) {
		parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
		$this->coreRegistry = $coreRegistry;
		$this->reviewFactory = $reviewFactory;
		$this->ratingFactory = $ratingFactory;
	}
	
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
	public function execute()
    {
        $reviewsIds = $this->getRequest()->getParam('reviews');
      
        if (!$reviewsIds) {
            $this->messageManager->addError(__('Please select review(s).'));
        } else {
        	$reviewsIds = explode(',',$reviewsIds);
        	$this->coreRegistry->register('isSecureArea', true);
            try {
                foreach ($reviewsIds as $reviewId) {
                    $model = $this->reviewFactory->create()->load($reviewId);
                    $model->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($reviewsIds))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
            	
                $this->messageManager->addException($e, __('Something went wrong while deleting these records.'));
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('productreview/*/' . $this->getRequest()->getParam('ret', 'index'));
        return $resultRedirect;
    }
    
}