<?php
/**
 * Magento
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
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Model_Shipping_Shipping extends Mage_Shipping_Model_Shipping
{

	protected static $_debug;

    /**
     * Retrieve all methods for supplied shipping data
     *
     * @param Mage_Shipping_Model_Shipping_Method_Request $data
     * @return Mage_Shipping_Model_Shipping
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

        return $this->collectDropshipRates($request);
    }

    public function collectDropshipRates(Mage_Shipping_Model_Rate_Request $request)
    {
     	if (!Mage::helper('dropcommon')->isActive() || sizeof($request->getAllItems())<1) {
     		return parent::collectRates($request);
     	}

     	self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Dropcommon');

     	if (!$request->getOrig()) {
            $request
                ->setCountryId(Mage::getStoreConfig('shipping/origin/country_id', $request->getStore()))
                ->setRegionId(Mage::getStoreConfig('shipping/origin/region_id', $request->getStore()))
                ->setCity(Mage::getStoreConfig('shipping/origin/city', $request->getStore()))
                ->setPostcode(Mage::getStoreConfig('shipping/origin/postcode', $request->getStore()));
        }

    	if (self::$_debug) {
    		Mage::helper('wsalogger/log')->postDebug('dropcommon','Dropship Collecting',$request->getDropshipCollecting());
    		Mage::helper('wsalogger/log')->postDebug('dropcommon','Limit carrier',$request->getLimitCarrier());
    	}

     	// else want to restrict what's sent to the carriers
     	if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvZHJvcHNoaXAvc2hpcF9vbmNl',
        	'aG9sZGluZ3Vw','Y2FycmllcnMvZHJvcHNoaXAvc2VyaWFs')) { return parent::collectRates($request);}

        $limitCarrier = $request->getLimitCarrier();
        if (!$limitCarrier) {
            $carriers = Mage::getStoreConfig('carriers', $request->getStoreId());

            if(array_key_exists('adminshipping', $carriers)) {
                unset($carriers['adminshipping']);
            }

            foreach ($carriers as $carrierCode=>$carrierConfig) {
            	if (Mage::helper('dropcommon')->calculateDropshipRates() &&
                    (($carrierCode!='dropship' && !$request->getDropshipCollecting()) ||
            		($carrierCode=='dropship' && $request->getDropshipCollecting()) ||
                        ($carrierCode=='m2eproshipping' && $request->getDropshipCollecting()))) {
	    			continue;
				}
				$this->collectCarrierRates($carrierCode, $request);
            }
        } else {
            if (!is_array($limitCarrier)) {
                $limitCarrier = array($limitCarrier);
            }

            if(array_key_exists('adminshipping', $limitCarrier)) {
                unset($limitCarrier['adminshipping']);
            }

            foreach ($limitCarrier as $carrierCode) {
				if (Mage::helper('dropcommon')->calculateDropshipRates() &&
                    (($carrierCode!='dropship' && !$request->getDropshipCollecting()) ||
            		($carrierCode=='dropship' && $request->getDropshipCollecting()) ||
                        ($carrierCode=='m2eproshipping' && $request->getDropshipCollecting()))) {
	    			continue;
				}
                $carrierConfig = Mage::getStoreConfig('carriers/'.$carrierCode, $request->getStoreId());
                if (!$carrierConfig) {
                    continue;
                }
                $this->collectCarrierRates($carrierCode, $request);
            }
        }

        return $this;
    }

    /**
     * Gets the rates for admin shipping. They should only be calculated once. Not per warehouse.
     *
     * DROP-112
     * @param $request
     * @return $this|null
     */
    public function collectAdminShippingRates($request) {
        $this->collectCarrierRates('adminshipping', $request);

        return $this;
    }

    public function collectMergedRates($storeId, $ratesToAdd) {
    	$this->resetResult();
    	$carrier = $this->getCarrierByCode("dropship", $storeId);
    	$this->getResult()->append($carrier->createMergedRate($ratesToAdd));
      	return $this;
    }

}