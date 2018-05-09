<?php
namespace Ced\GroupBuying\Model\ResourceModel\Guest;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
class	Collection	extends	AbstractCollection
	{
	    /**
	     * Define model & resource model	     */
	    protected function _construct()
	    {
	        $this->_init(
	            'Ced\GroupBuying\Model\Guest',
	            'Ced\GroupBuying\Model\ResourceModel\Guest'
	        );
	    }
	}