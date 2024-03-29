<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_Customermembership
  * @author       CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
namespace Ced\TeamMember\Block\Account;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\Data\CustomerInterface;
/**
 * Customer address edit block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit extends \Magento\Framework\View\Element\Template
{
    
    protected $_session;

    public $_objectManager;
   protected $_filtercollection;
   protected $_requestCollection;
   
    
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Ced\TeamMember\Model\Session $teammemberSession,
        
        array $data = []
    ) {
        $this->_session = $teammemberSession;
        $this->_objectManager = $objectInterface;
        parent::__construct($context,$data);
    }
   
  public function getTeamMember()
  {
  	return $this->_objectManager->create('Ced\TeamMember\Model\TeamMemberData')->load($this->getRequest()->getParam('teammemberid'),'teammember_id');
  }
  
  public function getSaveUrl(){
  	
  	return $this->getUrl('*/*/save',array('id'=>$this->getRequest()->getParam('teammemberid')));
  }
  
  public function getMediaDirectory()
  {
  	$mediaDirectory  = $this->_objectManager->get('Magento\Framework\Filesystem')
  	->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
  	$path = $mediaDirectory->getAbsolutePath('teammember/'.$this->getRequest()->getParam('teammemberid').'/');
  	return $path;
  }
  
 
  public Function getImageSrc()
    {
    	$url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'teammember/'.$this->getRequest()->getParam('teammemberid').'/';
        return $url;
    }
}
