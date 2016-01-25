<?php 
class Sourav_QuantityDropdown_Block_Adminhtml_System_Config_Form_Fieldset_About extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $html .= $this->_getAboutHtml();
        $html .= $this->_getFooterHtml($element);
        return $html;
    }


    /**
     * Get html of about
     *
     * @return string
     */
    protected function _getAboutHtml()
    {	 
        
	$block = $this->getLayout()->createBlock('core/template');
	
	$block->setTemplate('sourav_quantitydropdown/about.phtml');
	
	return $block->toHtml();
    
    }
}
