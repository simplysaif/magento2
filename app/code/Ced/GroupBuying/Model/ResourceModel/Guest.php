<?php
   
namespace Ced\GroupBuying\Model\ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb; 
 class  Guest  extends AbstractDb
 {
      /**
      * Define main table
      */
     protected function _construct()
     {
         $this->_init('guest_information', 'id');
     }
 }