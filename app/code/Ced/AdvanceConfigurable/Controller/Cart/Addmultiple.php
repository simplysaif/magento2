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
 * @package     Ced_AdvanceConfigurable
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\AdvanceConfigurable\Controller\Cart;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;

class Addmultiple extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
    
    public $_allowedResource = true;
    
    /**
 * @var Session 
*/
    protected $session;
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_resultPageFactory;
     
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->urlModel = $urlFactory;
        $this->_resultPageFactory  = $resultPageFactory;
        $this->_moduleManager = $moduleManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_cart = $cart;
        $this->_productRepository = $productRepository;
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $productRepository);
    }

    /**
     * Export shipping table rates in csv format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
      $post = $this->getRequest()->getPostValue();
      $cart = $this->_objectManager->create ('Magento\Checkout\Model\Cart');
      try{
        foreach($post['child'] as $key => $value)
        {
          if($value){
            $string = $key;

            $a = explode(',', $string);

            foreach ($a as $result) {
                $b = explode('-', $result);
                $array[$b[0]] = $b[1];
            }
             $params = array(
                  'product' => $post['product'], // This would be $product->getId()
                  'qty' => $value,
                  'super_attribute' => $array
              
              );
             $productobj = $this->_objectManager->create ('Magento\Catalog\Model\Product' )->load ($post['product']);
              if(!$productobj->getId())
              {
                $this->messageManager->addError(__('Product Does Not Exist. Contact Administrator'));
              }
              $cart->addProduct($productobj,$params);
          } 
        }
        $cart->save(); 
        if (!$cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $productobj->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
        }
      }
      catch(\Exception $e){
        $this->messageManager->addException($e, __($e->getMessage()));
      }    
    }
}
