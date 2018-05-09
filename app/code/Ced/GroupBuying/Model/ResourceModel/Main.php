<?php
   
namespace Ced\GroupBuying\Model\ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb; 
 class  Main  extends AbstractDb
 {
      /**
      * Define main table
      */
     protected function _construct()
     {
         $this->_init('group_main_table', 'group_id');
     }
 }