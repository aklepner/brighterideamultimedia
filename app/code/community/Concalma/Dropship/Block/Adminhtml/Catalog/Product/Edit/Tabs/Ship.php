<?php
/**
 * Concalma Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 */
class Concalma_Dropship_Block_Adminhtml_Catalog_Product_Edit_Tabs_Ship extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Reference to product objects that is being edited
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    protected $_config = null;

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->__('Shipping');
    }

    public function getTabTitle() {
        return $this->__('Shipping');
    }



    public function canShowTab() {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }

    /**
     * Render block HTML
     *
     * @return string
     */


    protected function _toHtml() {

        $id = $this->getRequest()->getParam('id');
        $warehouseInfo = $this->getWarehouseInformation();
        $number_warehouse = count($warehouseInfo);
        $option='';
        for ($i = 0; $i  < $number_warehouse; $i++) {
        	$option = $option.'<option value="'.$warehouseInfo[$i]['id'].'"  > '.$warehouseInfo[$i]['title'].'</option>' ;
        }

        try {
            //$button = $this->getLayout()->createBlock('adminhtml/widget_button')
            //    ->setClass('add')
            //    ->setType('button')
            //    ->setOnClick('window.location.href=\''.$this->getUrl('sarp_admin/product/convert', array('id'=>$id)).'\'')
            //    ->setLabel('Convert this product to subscription');
           // return $button->toHtml();
           $html = '<div class="entry-edit" >
                       <div class="entry-edit-head">
                       			<h4 class="icon-head head-edit-form fieldset-legend">Shipping</h4>
                       </div>
                   </div>';
           $html = $html.' <div class = "fieldset fieldset-wide" >
                       <span class="label" style="width: 200px"> Warehouse : </span>
                      <select id="warehouse"  style="width: 280px" name="product[warehouse]" multiple="multiple" >
                        '.$option.'
                         </select>'. $this->getWarehouseAtt()."demo";
           return $html;
        }catch(exception $e) {
            return $this->__("Sorry, but this product cannot have a warehouse");
        }
    }
    
    public function getWarehouseInformation() {
    	$query = "select * from `dropship`";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
    }
    
    public function getWarehouseAtt() {
    	$id = $this->getRequest()->getParam('id');
    	$product = Mage::getModel('catalog/product')->load($id);
    	return $product-> getResource()->getAttribute('warehouse')->getId();
    }
}
