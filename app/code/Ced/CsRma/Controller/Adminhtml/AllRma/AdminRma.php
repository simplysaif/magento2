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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class AdminRma extends \Magento\Backend\App\Action
{

	/**
     * @var \Ced\CsRma\Helper\Data
     */

    public $rmaDataHelper;
	/**
     * @var \Ced\CsRma\Model\RmaitemsFactory
     */
    protected $rmaItemFactory;

	/**
    * @var \Ced\CsRma\Model\RequestFactory
    */

    protected $rmaRequestFactory;

	/**
	 * @var \Magento\Backend\Model\View\Result\Forward
	 */
	protected $dateTime;

	/**
     * @param \Ced\CsRma\Helper\Data $rmaDataHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ced\CsRma\Model\RmaitemsFactory $rmaItemFactory
     * @param \Ced\CsRma\Model\RequestFactory $rmaRequestFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
	public function __construct(
		\Ced\CsRma\Helper\Data $rmaDataHelper,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Ced\CsRma\Model\RmaitemsFactory $rmaItemFactory,
    	\Ced\CsRma\Model\RequestFactory $rmaRequestFactory,
		\Magento\Backend\App\Action\Context $context
	) {
		$this->rmaDataHelper = $rmaDataHelper;
		$this->dateTime = $dateTime;
		$this->rmaItemFactory = $rmaItemFactory;
        $this->rmaRequestFactory = $rmaRequestFactory;
		parent::__construct($context);
	}

	/**
     * Customer rma form  in adminpanel action
     *
     * @return void|\Magento\Framework\Controller\Result\Page
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPostValue();
        $store = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        
        if($data)
        {
            try{
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
                            $rmaData[$vendorId] = $this->getAdminRequestDetails($data,$vendorId);
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
                            $rmaData['admin'] = $this->getAdminRequestDetails($data,$vendorId);
                            $rmaData['admin']['vendor_id'] = 'admin';
                            $rmaData['admin']['item'][] = $this->getRmaItemDetail($data['item-data'], $key, 'admin');
                        }
                     }
                }
                foreach ($rmaData as $rma_key => $rma_data) {
                     $requestModel = $this->rmaRequestFactory->create();
                     $requestModel->addData($rma_data)->save();
                }
                $url =$store->getStore()->getUrl('csrma/customerrma/view',['id'=>$requestModel->getRmaRequestId()]);
                
                $this->_objectManager->create('Ced\CsRma\Helper\Email')->sendNewRmaMail($data,$data['email'],$data['user-name'],$requestModel->getRmaId(),$url);
              
                    $this->messageManager->addSuccess(__('You submitted the request.')); 
                    return $resultRedirect->setPath('csrma/*/');

    			} catch (LocalizedException $e) {
                    echo $e->getMessage();
                $this->messageManager->addError($e->getMessage());

                } catch (\Exception $e) {
            	   echo $e->getMessage();
                    $this->messageManager->addException($e, __('Something went wrong while saving this request.'));
                }
        }
		$resultRedirect->setPath('csrma/*/');
        return $resultRedirect;	
	}

	 /**
     * get Admin Rma Request Detail
     *
     * @return array
     */
	protected function getAdminRequestDetails($data,$vendorId)
	{
		return $setdata = [
			'order_id'=> $data['order_id'],
            'rma_id'=>$this->rmaDataHelper->getRmaID($data['order_id'],$vendorId),
			'status'=> $data['status'],
			'resolution_requested'=> $data['resolution_requested'],
			'package_condition' => $data['package_condition'],
			'reason'=> $data['reason'],
			'customer_name'=> $data['user-name'],
			'customer_email' => $data['email'],
			'store_id' => $data['store_id'],
			'website_id' => $data['website_id'],
			'customer_id'=> $data['customer_id'],
			'external_link' =>$this->rmaDataHelper->getExternalLink(),
			'created_at'=>$this->dateTime->gmtDate(),
			'updated_at'=>$this->dateTime->gmtDate()
		];
	}

    protected function getRmaItemDetail($item,$key,$vendorId)
    {
    	
    	$itemDetails =  $this->_objectManager->create('Magento\Sales\Model\Order\Item')->load($item['order_item_id'][$key]);
    	$rowTotal = ($itemDetails->getRowTotal()/$itemDetails->getQtyOrdered())*$item['rma-qty'][$key];
    	if($itemDetails->getDiscountAmount()){
    		$perquantitydiscount = $itemDetails->getDiscountAmount()/$itemDetails->getQtyOrdered();
    	}
    	$rowTotal = $rowTotal-($perquantitydiscount*$item['rma-qty'][$key]);
        $itemData =[];
        $itemData = [
                    'product_id'=>$item['item-id'][$key],
                    'sku'=>$item['item-sku'][$key],
                    'item_name'=>$item['item-name'][$key],
                    'price'=>$item['item-price'][$key],
                    //'qty'=>$item['item-qty'][$key],
                    'rma_qty'=>$item['rma-qty'][$key],
                    'row_total'=>$rowTotal,
                    'vendor_id'=>$vendorId
                    ];
        return $itemData;
    }
}



