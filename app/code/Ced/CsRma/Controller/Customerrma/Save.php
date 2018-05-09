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

class Save extends  \Ced\CsRma\Controller\Link
{
	/**
	 * @var \Magento\Framework\Data\Form\FormKey\Validator
	 */

	protected $_formKeyValidator;

	/**
	 * @var redirect
	 */

	protected $_redirect;
	/**
	 * @var \Magento\Customer\Model\Session 
	 */

	protected $customerSession;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */

	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\Controller\Result\ForwardFactory
	 */

	protected $resultForwardFactory;

	/**
	 * @var \Magento\Backend\Model\View\Result\Redirect
	 */

	protected $resultRedirectFactory;

    /**
    * @var \Ced\Rma\Model\RequestFactory
    */
    protected $rmaRequestFactory;

    /**
    * @var  \Ced\Rma\Helper\OrderDetail
    */
    protected $rmaOrderHelper;
    /**
    * @var \Magento\Framework\Stdlib\DateTime\DateTime
    */
    protected $dateTime;
    public $url;
	/**
	 * @param Magento\Framework\App\Action\Context 
	 * @param Magento\Customer\Model\Session $customerSession
	 * @param Magento\Framework\Data\Form\FormKey\Validator
	 * @param Magento\Framework\Controller\Result\ForwardFactory 
	 * @param Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 * @param Magento\Framework\View\Result\PageFactory
	 * @param Ced\CsRma\Model\RequestFactory
	 * @param Magento\Backend\Model\View\Result\Redirect
	 * @param Ced\CsRma\Helper\OrderDetail
	 */

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
       	\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ced\CsRma\Model\RequestFactory $rmaRequestFactory,
        \Ced\CsRma\Helper\OrderDetail $rmaOrderHelper
	)

	{
        $this->dateTime = $dateTime;
        $this->rmaOrderHelper = $rmaOrderHelper;
        $this->rmaRequestFactory = $rmaRequestFactory;
		$this->resultPageFactory =$resultPageFactory;
		$this->_formKeyValidator = $formKeyValidator;
		$this->_redirect = $context->getRedirect();
		$this->_url = $context->getUrl();
		$this->customerSession = $customerSession;
		parent::__construct(
            $context,
            $customerSession,
            $resultForwardFactory,
            $resultRedirectFactory,
            $resultPageFactory
        );

	}

	/**
	 * @param execute
	 * return redirect page
	 */

	public function execute()
	{
	    
		
		if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        
        $data = $this->getRequest()->getParams();
    
        $customerId =$this->_objectManager->get('Magento\Customer\Model\Session')->getCustomer()->getId();
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $customerEmail = $customer->getEmail();
        $customerName =   $customer->getName();
        if ($data['item-data']['item-id']) {
        	try {

        		/*make an array to save data*/
            	$rmaData = [];

            	foreach ($data['item-data']['item-id'] as $key =>$value) {
                    if($data['item-data']['rma-qty'][$key]==0){
                        continue;
                    }

                   	$vendorId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                        ->getVendorIdByProduct($value);
                    if($vendorId){
                        if(isset($rmaData[$vendorId]))
                        {
                            $rmaData[$vendorId]['item'][] = $this->getRmaItemDetail($data['item-data'], $key, $vendorId);
                        }
                        else
                        {
                            $rmaData[$vendorId] = $this->rmaOrderHelper->getRequestDetailsOrder($data,$vendorId);
                            $rmaData[$vendorId]['vendor_id'] = $vendorId;
                            $rmaData[$vendorId]['item'][] = $this->getRmaItemDetail($data['item-data'], $key, $vendorId);
                        }
                    } else {
                        if(isset($rmaData['admin']))
                        {
                            $rmaData['admin']['item'][] = $this->getRmaItemDetail($data['item-data'], $key, 'admin');
                        }
                        else
                        {
                            $vendorId = '0';
                            $rmaData['admin'] = $this->rmaOrderHelper->getRequestDetailsOrder($data,$vendorId);
                            $rmaData['admin']['vendor_id'] = 'admin';
                            $rmaData['admin']['item'][] = $this->getRmaItemDetail($data['item-data'], $key, 'admin');
                        }
                     }
                }
                
                foreach ($rmaData as $rma_key => $rma_data) {
                	   /*create factory for rma request and rma shipping */
                     $requestModel = $this->rmaRequestFactory->create();
                     $requestModel->addData($rma_data)
                     		->setData('created_at',$this->dateTime->gmtDate())
                    		->setData('updated_at',$this->dateTime->gmtDate())
                     		->save();
                }
               
                $url =  $this->_url->getUrl('csrma/customerrma/view',['id'=>$requestModel->getRmaRequestId()]);
                $this->_objectManager->create('Ced\CsRma\Helper\Email')->sendNewRmaMail($data,$customerEmail,$customerName,$requestModel->getRmaId(),$url);
               
                $this->messageManager->addSuccess(__('Your request has been submitted.'));
                $url = $this->_buildUrl('*/*/index', ['_secure' => true]);
                return $this->resultRedirectFactory->create()
                    ->setUrl($this->_redirect->success($url));
            } catch (\Exception $e){
                $redirectUrl = $this->_buildUrl('*/*/index');
                $this->messageManager->addException($e, __('We can\'t save the address.'));
            }
        }
        $redirectUrl = $this->_buildUrl('*/*/index');          
	}

	protected function getRmaItemDetail($item,$key,$vendorId)
    {
    	
    	$itemDetails =  $this->_objectManager->create('Magento\Sales\Model\Order\Item')->load($item['order_item_id'][$key]);
    	$rowTotal = ($itemDetails->getRowTotal()/$itemDetails->getQtyOrdered())*$item['rma-qty'][$key];
    	if($itemDetails->getDiscountAmount()){
    		$perquantitydiscount = $itemDetails->getDiscountAmount()/$itemDetails->getQtyOrdered();
    	}
    	$rowTotal = $rowTotal-($perquantitydiscount*$item['rma-qty'][$key]);
        $itemData =[
                    'product_id'=>$item['item-id'][$key],
                    'sku'=>$item['item-sku'][$key],
                    'item_name'=>$item['item-name'][$key],
                    'price'=>$item['item-price'][$key],
                    'rma_qty'=>$item['rma-qty'][$key],
                    'row_total'=>$rowTotal,
                    'vendor_id'=>$vendorId
                ];
        return $itemData;
    }
}

