<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Blog
 * @author   	CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ced\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class massDelete extends \Magento\Backend\App\Action
{
	/**
	 * @var execute
	 */
	public function execute()
	{	
		$data = $this->getRequest()->getParams();
	    if ($data) 
	    {
			$id = $this->getRequest()->getParam('attribute_id');
			foreach($id as $val)
			{
				$model = $this->_objectManager->create('Magento\Customer\Model\Attribute')->load($val);
				$model->setId($val)->delete();	
			}
	        $this->_redirect('blog/index/index');
	        $this->messageManager->addSuccess(__('Deleted Successfully'));
	      }
	}
}