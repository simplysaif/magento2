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
namespace Ced\CsRma\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
    * @var \Ced\CsRma\Model\RequestFactory
    */
    protected $rmaRequestFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
    * @var \Ced\CsRma\Model\RmastatusFactory
    */
    public $rmaStatusFactory;

    /**
    * @var \Magento\Sales\Model\OrderFactory 
    */
    protected $salesOrderFactory;

    /**
    * @var  \Magento\Store\Model\StoreManagerInterface
    */
    protected $storeManager;

    /**
    * @var \Magento\Framework\ObjectManagerInterface
    */
    protected $objectManager;
    
    /**
    * @var \Magento\Framework\Stdlib\DateTime\DateTime
    */
    protected $dateTime;

     /**
    * @var Magento\Sales\Model\OrderFactory
    */
    protected $orderAddressRepository;

    /**
     * @param \Magento\Sales\Model\OrderFactory $salesOrderFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context $context
     * @param  \Ced\CsRma\Model\RmastatusFactory $rmaStatusFactory
     */

    public function __construct(
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Ced\CsRma\Model\RequestFactory $rmaRequestFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\OrderFactory $orderFactory, 
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository,
        \Ced\CsRma\Model\RmastatusFactory $rmaStatusFactory

    ) {
        $this->rmaRequestFactory = $rmaRequestFactory;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderFactory = $orderFactory;
        $this->rmaStatusFactory = $rmaStatusFactory;
        $this->dateTime = $dateTime;
        $this->salesOrderFactory = $salesOrderFactory;
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get format for address
     *
     * @param \Magento\Customer\Model\Address\Config 
     * @return address
     */

    public function format($address, $type)
    {
        $formatType = $this->objectManager->get('\Magento\Customer\Model\Address\Config')->getFormatByCode($type);
        if (!$formatType || !$formatType->getRenderer()) {
            return null;
        }
        return $formatType->getRenderer()->renderArray($address->getData());
    }
    
    /**
     * Get store value
     *
     * @param $storeId
     * @return store name
     */

    public function getStoreValue($storeId)
    {
        $store = $this->storeManager->getStore($storeId);
        $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), 
                    $store->getName()
                ];
        return implode('<br/>', $name);
    }

    /**
     * Get external link
     *
     * @return string
     */

    public function getExternalLink()
    {
        return strtoupper(uniqid(dechex(rand())));
    }

    /**
     * Get img uplaod
     * @param $data
     * @return string
     */
    public function getRmaImgUpload($data)
    {
    	
        $flag ='';
       
            $mediaDirectory = $this->objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $destinationFolder = 'ced/csrma/chat';
            $path = $mediaDirectory->getAbsolutePath($destinationFolder);
            $uploader = $this->objectManager->create('Magento\MediaStorage\Model\File\Uploader', 
                        array('fileId' => "rma_file"));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','pdf','zip')); 
            $uploader->setAllowRenameFiles(false);                 
            $uploader->setFilesDispersion(false);
            $extension = $uploader->getFileExtension();
            $fileName = time().'.'.$extension;
            $uploader->save($path, $fileName);
            $flag = $uploader->getUploadedFileName();
       
        return $flag;
    }

    /**
     * Get rmaid
     * @param $orderid
     * @return string
     */

    public  function getRmaID($orderId,$vendorId)
    {
        $rmaId = '';
        $order = $this->salesOrderFactory->create()->loadByIncrementId($orderId);
        if($order->getCustomerGroupId()){
            $rmaId = $order->getCustomerGroupId();  
            $rmaId .= substr($order->getCustomerFirstname(), 0, 1);
            $rmaId .= substr($order->getCustomerLastname(), 0, 1);
            $rmaId .= substr($order->getIncrementId(), -4, -1);
            $rmaId .= substr($this->dateTime->timestamp(time()), -4);
            $rmaId .= $vendorId;
            $rmaId .= $order->getEntityId();
        } else {
            $rmaId.= substr($order->getBillingAddress()->getFirstname(), 0,1);
            $rmaId.= substr($order->getBillingAddress()->getLastname(), 0,1);
            $rmaId.= substr($order->getIncrementId(), -4, -1);
            $rmaId.= substr($this->dateTime->timestamp(time()), -4);
            $rmaId .= $vendorId;
            $rmaId.= $order->getEntityId();
        }
        return $rmaId;
    }

    /**
     * Get rmaimage
     * @param $sku
     * @return string
     */

    public function getRmaImage($sku)
    {
        $product =  $this->objectManager->create('Magento\Catalog\Model\Product')
                    ->loadByAttribute('sku',$sku);   
        $imageHelper  =  $this->objectManager->get('\Magento\Catalog\Helper\Image');
        $image_url = $imageHelper->init($product, 'product_page_image_small')
            ->setImageFile($product->getFile())
            ->getUrl();
        return $image_url;
    }

    /*get status table message*/

    public function getStatusData($status)
    {
        $rmaStatus = $this->rmaStatusFactory->create()
            ->getCollection()
            ->addFieldToFilter('status',$status)
            ->getData();
        $notification ='';
        foreach ($rmaStatus as $key => $value) {
            $notification = $value['notification'];
        }
        return $notification;
    }

    /**
     * Generate Unique Approval Code on basis of respective RMA
     *
     * @return string
     */
    public function getApprovalCode($firstname,$order)
    {
        $approvalCode = [];
        $approvalCode['firstLetter'] = substr(strtolower($firstname), 0, 1);
        $approvalCode['position'] = strcspn(strtolower($firstname), "aeiou");
        $approvalCode['rma'] = substr(strtolower($order), 0, 1).substr(strtolower($order), -1, -2);
        $finaleApprovalCode = md5(implode($approvalCode));
        return $finaleApprovalCode;
    }

    /**
     * Return rma request order collection.
     *
     * @return array
     */
    public function getOrderCollection($incrementId)
    {
        return $this->orderFactory->create()->loadByIncrementId($incrementId);
    }

    public function getAddress($order)
    {
        $address ='';
        if($order->getCustomerIsGuest() == 1) {
            $address = $this->format($order->getBillingAddress(), 'html');
        } else {
            $billingAddressId = $order->getBillingAddressId();
            if($billingAddressId) {
                $billingAddress = $this->orderAddressRepository->get($billingAddressId);
                $address = $this->format($billingAddress,'html');
            }
        }
        return $address;
    }

    public function getShippingAddress($order)
    {
        $address ='';
        if($order->getCustomerIsGuest() == 1) {
            $address = $this->format($order->getShippingAddress(), 'html');
        } else {
            $shippingAddressId = $order->getShippingAddressId();
            if($shippingAddressId) {
                $shippingAddress = $this->orderAddressRepository->get($shippingAddressId);
                $address = $this->format($shippingAddress,'html');
            }
        }
        return $address;
    }

    /**
     * check other rma for the order id.
     *
     * @return string
     */
    public function checkOtherRma($orderId)
    {
        $model = $this->rmaRequestFactory->create()->getCollection()
            ->addFieldToFilter('order_id',$orderId)
            ->addFieldToFilter('status', array('nin' => array('Pending','Approved','Completed')));
        if(count($model->getData())>0)
            return true;
        else
            return false;
    }

    public function getPrice($price)
    {
        return $this->objectManager->get('Magento\Sales\Model\Order')->formatBasePrice($price);
    }
}