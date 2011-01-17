<?php

require_once("wpw-backbase.inc");

class wpw_backWunderground extends wpw_backBase {
	function __construct() {
		parent::__construct();
		$this->furl = 'http://api.wunderground.com/auto/wui/geo/ForecastXML/index.xml?query=';
	}
	function doGetFeed() {
		try {
			$xmlStr = wp_remote_fopen(trim($this->furl . urlencode($this->getLocation())));
                	$xml = new SimpleXMLElement($xmlStr);
/*
		if(is_wp_error($xmlStr) || !$xml=simplexml_load_string($xmlStr)) {
			return '<!-- WP Wunderground Error : Error reading XML file at '.$this->furl.$this->location.' -->'.$content;
		} elseif(empty($xml->simpleforecast->forecastday)) {
			return '<!-- WP Wunderground Error : Weather feed was empty from '.$this->furl.$this->location.' -->'.$content;
		}
*/
		} catch (Exception $e) {
			return '<!-- WP Wunderground Error : Error reading XML file at '.$this->furl.$this->getLocation().' -->'.$content;
		}
		$i = 0;
		foreach($xml->simpleforecast->forecastday as $day) {
			if($i < $this->getDays()) {
				$day  = simpleXMLToArray($day);
				$high = (int)$day["high"]["celsius"];
				$low  = (int)$day["low"]["celsius"];
				$wind = -1;
				$date = $day['date']['epoch'];
				switch($day['icon']) {
					case "clear":
						$condition = $GLOBALS['CONDITION']->CLEAR;
						break;
					case "partlycloudy":
						$condition = $GLOBALS['CONDITION']->PARTCLOUD;
						break;
					case "mostlycloudy":
						$condition = $GLOBALS['CONDITION']->CLOUDY;
						break;
					case "chancesnow":
						$condition = $GLOBALS['CONDITION']->SNOW;
						break;
					default:
						$condition = $GLOBALS['CONDITION']->UNKNOWN;
						break;
				}
				$this->weather[] = new wpw_weatherData($high, $condition, $date, $low, $wind);
			}
			$i++;
		}
	}
}
