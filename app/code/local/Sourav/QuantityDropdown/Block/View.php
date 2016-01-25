<?php
/**
  
  @category    Sourav
  @package     Sourav_QuantityDropdown
  
 */

class Sourav_QuantityDropdown_Block_View extends Mage_Catalog_Block_Product_View
{
   
          function hasSimpleQuantity() {
			$product = $this->getProduct();
		   $enableForProduct=$product->getResource()->getAttribute('dream_quantity_enable')->getFrontend()->getValue($product);
            
			
			$hasValue=intval($product->getDreamQuantityOption())  ; 			
			if($enableForProduct=="Yes" && $hasValue>0){
			     return true;
			}else{
		       	  return false;
			}
			
       }
          
       
         function getQuantityOptions()
		 {
			 $product = $this->getProduct();
 		   
			 $QuantityOptions=$product->getResource()->getAttribute('dream_quantity_option')->getFrontend()->getValue($product);	 
              $QuantityOptions = rtrim($QuantityOptions, ',');			
			return $optionsArray = explode(",",$QuantityOptions);
         }	   

}