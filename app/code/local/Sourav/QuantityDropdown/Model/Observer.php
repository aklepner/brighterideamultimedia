<?php
/**
  @category    Sourav
  @package     Sourav_QuantityDropdown
  
 */

class Sourav_QuantityDropdown_Model_Observer 
{
    public function catalogProductLoadAfter(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
    }
}