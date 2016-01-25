<?php
/**
  @category    Sourav
  @package     Sourav_QuantityDropdown
  
 */

class Sourav_QuantityDropdown_Helper_Data extends Mage_Core_Helper_Abstract
{
     /**
	 Path to store config if front-end output is enabled
     *
     * @var string
     */
    const XML_PATH_ENABLED            = 'quantityoptionconfig/productquantity_group/productquantity_enable';

 
       public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }  
 
	
                
}