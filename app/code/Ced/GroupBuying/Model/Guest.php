<?php
   namespace Ced\GroupBuying\Model;
   
  use Magento\Framework\Model\AbstractModel;
   
  class Guest extends AbstractModel
  {
      /**
      * Define resource model

      */
      protected function _construct()
     {
         $this->_init('Ced\GroupBuying\Model\ResourceModel\Guest');
     }
 }