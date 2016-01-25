<?php


/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
require_once 'Mage/Checkout/controllers/OnepageController.php';

class Webshopapps_Dropship_AjaxController extends Mage_Checkout_OnepageController
{
    protected $_address;

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError() //|| $this->getOnepage()->getQuote()->getIsMultiShipping()
        ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))
        ) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    public function getTotalShippingPriceAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isGet()) {
            $totalShippingPrice = $this->getRequest()->getParam('total_shipping');
            $excl = $this->getShippingPrice($totalShippingPrice, Mage::helper('tax')->displayShippingPriceIncludingTax());
            $incl = $this->getShippingPrice($totalShippingPrice, true);
            if (Mage::helper('tax')->displayShippingBothPrices() && $excl != $incl) {
                $resultSet = $excl . $this->__(' Incl. Tax ') . $incl;
            } else {
                $resultSet = $excl;
            }
            $this->getResponse()->setBody($resultSet);
        }
    }

    protected function getShippingPrice($price, $flag)
    {
        return $this->getOnepage()->getQuote()->getStore()->convertPrice(Mage::helper('tax')->getShippingPrice($price, $flag, $this->getAddress()), true);
    }

    protected function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getOnepage()->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }
}