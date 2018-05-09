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
namespace Ced\CsRma\Controller\Guestrma;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
    * @var  \Ced\CsRma\Helper\OrderDetail
    */
    protected $rmaOrderHelper;
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
     * @var \Magento\Backend\Model\View\Result\Redirect
     */

    protected $resultRedirectFactory;

    /**
    * @var \Ced\CsRma\Model\RequestFactory
    */

    protected $rmaRequestFactory;

   /**
    * @var \Magento\Framework\Stdlib\DateTime\DateTime
    */
    protected $dateTime;
    /**
     * @param Magento\Framework\App\Action\Context 
     * @param Magento\Framework\Session\Generic
     * @param Magento\Customer\Model\Session
     * @param Magento\Framework\Data\Form\FormKey\Validator
     * @param Magento\Framework\Stdlib\DateTime\TimezoneInterface
     * @param Magento\Framework\View\Result\PageFactory
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Backend\Model\View\Result\Redirect
     */

    public function __construct(
        \Ced\CsRma\Helper\Data $rmaDataHelper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory,
       \Ced\CsRma\Model\RequestFactory $rmaRequestFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsRma\Helper\OrderDetail $rmaOrderHelper
    )
    {
        $this->rmaDataHelper = $rmaDataHelper;
        $this->dateTime = $dateTime;
        $this->rmaOrderHelper = $rmaOrderHelper;
        $this->messageManager = $messageManager;
        $this->rmaRequestFactory = $rmaRequestFactory;
        $this->_objectManager = $context->getObjectManager();
        $this->_formKeyValidator = $formKeyValidator;
        $this->_redirect = $context->getRedirect();
        $this->customerSession = $customerSession;
        parent::__construct($context);

    }

    /**
     * @param execute
     */
    
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/form');
        }
        $data = $this->getRequest()->getPostValue();
        if ($data) {

            $rmaData = [];
            try {
                    foreach ($data['item-data']['item-id'] as $key =>$value) {
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
                         $requestModel = $this->rmaRequestFactory->create();
                         $requestModel->addData($rma_data)
                                ->setData('created_at',$this->dateTime->gmtDate())
                                ->setData('updated_at',$this->dateTime->gmtDate())
                                ->save();
                    }
                    $url = $this->_buildUrl('*/*/form', ['_secure' => true,'id'=>$requestModel->getRmaRequestId()]);
                    $this->messageManager->addSuccess(__('Your request has been submitted.'));
                    return $this->resultRedirectFactory->create()
                            ->setUrl($this->_redirect->success($url));

                } catch (Exception $e) {
                    $redirectUrl = $this->_buildUrl('csrma/guestrma/form');
                    $this->messageManager->addException($e, __('We can\'t save.'));
                }
            }          
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _buildUrl($route = '', $params = [])
    {
        /** @var \Magento\Framework\UrlInterface $urlBuilder */
        $urlBuilder = $this->urlBuilder;
        return $urlBuilder->getUrl($route, $params);
    }

    protected function getRmaItemDetail($item,$key,$vendorId)
    {
        $itemData =[];
        $itemData = [
                    'product_id'=>$item['item-id'][$key],
                    'sku'=>$item['item-sku'][$key],
                    'item_name'=>$item['item-name'][$key],
                    'price'=>$item['item-price'][$key],
                    'rma_qty'=>$item['rma-qty'][$key],
                    'vendor_id'=>$vendorId
                    ];
        return $itemData;
    }
}