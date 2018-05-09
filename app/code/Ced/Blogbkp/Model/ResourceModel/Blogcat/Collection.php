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
  * @package   Ced_Blog
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\Blog\Model\ResourceModel\Blogcat;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

  /**
    * construct
    */
  
    protected function _construct()
    {

        $this->_init('Ced\Blog\Model\Blogcat', 'Ced\Blog\Model\ResourceModel\Blogcat');

    }

}