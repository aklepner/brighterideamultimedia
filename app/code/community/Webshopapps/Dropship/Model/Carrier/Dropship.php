<?php


/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
/**
 * Webshopapps Shipping Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * Conditional Free Shipping Module - where if attribute exclude_free_shipping is set
 * will result in free shipping being disabled for checkout
 *
 * @category   Webshopapps
 * @package    Webshopapps_Dropship
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
 * @version    1.5
 */
class Webshopapps_Dropship_Model_Carrier_Dropship
    extends Webshopapps_Dropcommon_Model_Shipping_Carrier_Abstract
{


    public function collectRates(Mage_Shipping_Model_Rate_Request $request,&$warehouseResults=array(),$splitRates=false)
    {
        $splitRates = $request->getDropshipSplitRates();
        if ($splitRates && Mage::helper('dropcommon')->calculateDropshipRates()) {//DROP-98
            $warehouseResults = array();
            $results= parent::collectDropshipRates($request,$warehouseResults,$splitRates);
            $rates = $this->getSplitRates($results,$warehouseResults);
            // doesnt currently return insurance. Not raised by customers so assumed they are using merged rates.
            return $this->collectStdResult($rates);
        } else {
            return parent::collectRates($request);
        }
    }

    /**
     * Called when we want to get split rates out
     *
     * @param $results
     * @param $warehouses
     * @return string
     */
    protected function getSplitRates($results,$warehouses)
    {

        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postDebug('dropship', 'Getting Split Rates', '');

        }
        $bigRates = array();

        foreach ($results as $key => $result) {
            $rates = $result->getAllRates();
            $warehouse = $warehouses[$key];

            foreach ($rates as $rate) {
                $rate->setWarehouse($warehouse);
                $rate->setPrice($this->getHandlingFee($rate->getPrice()));
                $bigRates[] = $rate;
            }
        }
        return $bigRates;
    }


    protected function collectStdResult($ratesToAdd)
    {
        $result = Mage::getModel('shipping/rate_result');
        foreach ($ratesToAdd as $rateToAdd) {
            $result->append($rateToAdd);
        }
        return $result;
    }
}
