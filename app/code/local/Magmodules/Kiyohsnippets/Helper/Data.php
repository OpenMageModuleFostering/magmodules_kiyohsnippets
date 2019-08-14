<?php 
/**
 * Magmodules.eu - http://www.magmodules.eu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magmodules.eu so we can send you a copy immediately.
 *
 * @category    Magmodules
 * @package     Magmodules_Kiyohsnippets
 * @author      Magmodules <info@magmodules.eu)
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Magmodules_Kiyohsnippets_Helper_Data extends Mage_Core_Helper_Abstract {

	function getSnapshopRequest() 
	{
		$kiyoh_shop_id = Mage::getStoreConfig('kiyohsnippets/api/shop_id');
		$api_url = Mage::getStoreConfig('kiyohsnippets/api/api_url');
		$data_type = Mage::getStoreConfig('kiyohsnippets/api/data_type');
		$text_suffix = Mage::getStoreConfig('kiyohsnippets/api/kiyoh_suffix');

		$filename = 'https://' . $api_url . '/widgetfeed.php?company=';
	
		if(!$kiyoh_shop_id) {
			return false;
		}

		$xml = $this->getXml($filename . $kiyoh_shop_id, 1); 

		if(!$xml) {
			return false;
		}

		$xml_data = $xml->channel->description;

		$qty = explode(' ', $xml_data);
		$qty = array_pop($qty);
		
		if(empty($qty)) {
			return false;		
		}
	
		if(empty($data_type)) {
			$max = '10';
			$data = explode(',', $xml_data);
			$data = explode(' ', $data[0]);
			$data = array_pop($data);
			$percentage = ($data * 10);
		} else {
			$data = '';
			$max = '100';
			if(preg_match("/[0-9]+%/", $xml_data, $matches)) {
				$data = substr($matches[0], 0, -1);
			}		
			$percentage = $data;
			$data = $data . '%';
		}
		
		$snippets = array();	
		$snippets['qty'] = $qty;
		$snippets['avg'] = $data;
		$snippets['max'] = $max;
		$snippets['percentage'] = $percentage;
		$snippets['data_type'] = $data_type;
		$snippets['text_suffix'] = $text_suffix;

		return $snippets;
	}
	
	function getKiyohLink() 
	{			
		$show_link = Mage::getStoreConfig('kiyohsnippets/api/show_link');

		if(!$show_link) {
			return false;
		}

		$kiyoh_link = Mage::getStoreConfig('kiyohsnippets/api/kiyoh_link');
		return $kiyoh_link;
	}

	function getKiyohStars($rating) 
	{	
		$show_stars = Mage::getStoreConfig('kiyohsnippets/api/show_stars');
		if(!$show_stars) {
			return false;
		}

		$html  = '<div class="rating-box">';
		$html .= '	<div class="rating" style="width:' . $rating . '%"></div>';
		$html .= '</div>';

		return $html;
	}

	public function getXml($url, $timeout = 0) 
	{
		$ch = curl_init($url);
		curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => (int) $timeout));
		if($xml = curl_exec($ch)){
			return simplexml_load_string($xml);
		} else {
			return false;
		}
	}	
	
}