<?php
require_once("wpw-weatherdata.inc");
if(!function_exists('plugin_basename')) {
	function plugin_basename($option) {
		return('/wordpress-weather');
	}
}
if(!function_exists('get_option')) {
	function get_option($option) {
		return('');
	}
}

if(!function_exists('simpleXMLToArray')) {
	function simpleXMLToArray($xml, 
	                    $flattenValues=true,
	                    $flattenAttributes = true,
	                    $flattenChildren=true,
	                    $valueKey='@value',
	                    $attributesKey='@attributes',
	                    $childrenKey='@children'){

	$return = array();
	if(!($xml instanceof SimpleXMLElement)){return $return;}
	$name = $xml->getName();
	$_value = trim((string)$xml);
	if(strlen($_value)==0){$_value = null;};

	if($_value!==null){
	    if(!$flattenValues){$return[$valueKey] = $_value;}
	    else{$return = $_value;}
	}

	$children = array();
	$first = true;
	foreach($xml->children() as $elementName => $child){
	    $value = simpleXMLToArray($child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey);
	    if(isset($children[$elementName])){
		if($first){
		    $temp = $children[$elementName];
		    unset($children[$elementName]);
		    $children[$elementName][] = $temp;
		    $first=false;
		}
		$children[$elementName][] = $value;
	    }
	    else{
		$children[$elementName] = $value;
	    }
	}
	if(count($children)>0){
	    if(!$flattenChildren){$return[$childrenKey] = $children;}
	    else{$return = array_merge($return,$children);}
	}

	$attributes = array();
	foreach($xml->attributes() as $name=>$value){
	    $attributes[$name] = trim($value);
	}
	if(count($attributes)>0){
	    if(!$flattenAttributes){$return[$attributesKey] = $attributes;}
	    else{$return = array_merge($return, $attributes);}
	}
	
	return $return;
	}
}

if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

class wpw_backBase {
	protected $icon_sets   = '';
	protected $furl        = '';
	protected $iurl        = '';
	protected $location    = '';
	protected $icons       = '';
	protected $displayTemp = '';
	protected $displayMeas = '';
	protected $displayDir  = '';
	protected $DisplayAlign= '';
	protected $highlow     = '';
	protected $caption     = '';
	protected $numdays     = '';
	protected $dayslink     = '';
	protected $datelabel   = '';
	protected $todaylabel  = '';
	protected $showlink    = '';
	protected $cache       = true;
	protected $width       = '';
	protected $weather     = '';
	protected $icon_url    = '';
	protected $timezone    = '';

	function __construct() {
		global $TSCALE,$MSCALE,$ORIENT,$ALIGN,$STATES;
		$this->icon_sets    = Array('humanity'); // Any US Zip Code
		$this->furl         = '';           // URL to pull the feed
		$this->iurl         = '';           // URL where the icons are

		// Frontend variables and defaults
		$this->location      = '46534';      // Any US Zip Code
		$this->icons         = 'humanity';   // Icon set named by icon_sets
		$this->setTimezone("America/Chicago");
		date_default_timezone_set($this->getTimezone());
		$this->setTScale($GLOBALS['TSCALE']->FAHRENHEIT); 
		$this->displayMeas   = $GLOBALS['MSCALE']->ENGLISH;
		$this->displayDir    = $GLOBALS['ORIENT']->HORIZ;
		$this->displayAlign  = $GLOBALS['ALIGN']->CENTER;
		$this->highlow = '%%high%%&deg;/%%low%%&deg;';
		$this->setCaption('Knox, Indiana');
		$this->setDays(5);
		$this->setDaysLink(true);
		$this->setDateLabel('%%weekday%%');
		$this->setTodayLabel('Today');
		$this->setClass('wordpress_weather');
		$this->showlink = '';
		$this->cache = true;
		$this->width = '100%';
		$this->icon_url  = sprintf("%s/plugins/%sicons" , WP_CONTENT_URL , str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
		$this->icon_path = sprintf("%s/plugins/%sicons" , WP_CONTENT_DIR , str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
		$this->weather = Array();
		
		// PHP5 only
		if(!version_compare(PHP_VERSION, '5.0.0', '>=')) {
			add_action('admin_notices', 'wpw_phpError');
			function wpw_phpError() {
				$out = '<div class="error" id="messages"><p>';
				$out .= 'WordPress Weather plugin requires PHP5. Please ask your hosting company to upgrade your server to PHP5. It should be free.';
				$out .= '</p></div>';
				echo $out;
			}
			return;
		}
		
		// Some hosts don't support it...
		if(!function_exists('simplexml_load_string')) {
			add_action('admin_notices', 'wpw_xmlError');
			function wpw_xmlError() {
				$out = '<div class="error" id="messages"><p>';
				$out .= 'The WP Wunderground plugin requires the PHP function <code>simplexml_load_string()</code>. Your server has this disabled. Please ask your hosting company to enable <code>simplexml_load_string</code>.';
				$out .= '</p></div>';
				echo $out;
			}
			return;
		}
		if(!function_exists('json_decode')) {
			add_action('admin_notices', 'wpw_jsonError');
			function wpw_jsonError() {
				$out = '<div class="error" id="messages"><p>';
				$out .= 'The WP Wunderground plugin requires the PHP function <code>json_decode()</code>. Your server has this disabled. Please ask your hosting company to enable <code>json_decode</code>.';
				$out .= '</p></div>';
				echo $out;
			}
			return;
		}
	}
    
	function getIcons() {
		return $this->icons;
	}
	function setIcons($icons) {
		if(in_array($icons, $this->icon_sets))
			$this->icons = $icons;
		return $this->icons;
	}
	function getIconPath() {
		if(in_array($this->icons, $this->icon_sets))
			return $this->icon_path . "/" . $this->icons;
		else
			return $this->icon_path . "/humanity";
	}
	function getIconUrl() {
		if(in_array($this->icons, $this->icon_sets))
			return $this->icon_url . "/" . $this->icons;
		else
			return $this->icon_url . "/humanity";
	}
	function doGetFeed() {
		for($iter = 0 ; $iter < $this->numdays ; $iter++) {
			$this->weather[] = new wpw_weatherData(100, $GLOBALS['CONDITION']->SUNNY, time() + ($iter * 24 * 60 * 60));
		}
	}
	function getFeed() {
		if($this->cacheRead()) {
			$this->doGetFeed();
			if($this->getCache())
				$this->cacheUpdate();
		}
	}
	function cacheRead() {
		$wud = wp_upload_dir();
		$dir = $wud['basedir'] . "/wordpress_weather";
		$fname = $dir . "/" . $this->getLocation() . ".json";
		if(! is_dir($dir) || ! is_file($fname))
			return true;

		$cacheStr = file_get_contents($fname);
		$cacheData = json_decode($cacheStr);

		if(($cacheData->serial + ($this->getCache() * 60)) < time()) {
			return true;
		}

		$this->weather = Array();
		foreach($cacheData->data as $data) {
			$newData = new wpw_weatherData($data->high, $data->condition, $data->date, $data->low, $data->windspeed);
			$newData->setPrecip($data->precip);
			$this->weather[] = $newData;
		}
		return false;
		
	}
	function cacheUpdate() {
		$wud = wp_upload_dir();
		$dir = $wud['basedir'] . "/wordpress_weather";
		if(! is_dir($dir))
			mkdir($dir, 755, true);
		$fname = $dir . "/" . $this->getLocation() . ".json";
		$fp = fopen($fname, 'w');
		$cacheData = Array('serial' => time(), 'data' => $this->weather);
		fwrite($fp, json_encode($cacheData));
		fclose($fp);
	}
	function displayFeed() {
		if($this->getOrient() == $GLOBALS['ORIENT']->PORTRAIT) {
			$css="display:block;";
			$class = "wpw-portrait";
		}
		else {
			$css="display:inline-block;";
			$class = "wpw-landscape";
		}
		$output = "<div class='" . $this->getClass() . "'><h2>" . $this->getCaption() . "</h2><ul class='forecast'>";
		$dayCount = 1;
		foreach($this->weather as $data) {
			if($this->getDaysLink()) {
				$linkStart = "<a target='_blank' href='" . $this->getForecastLink($dayCount) . "'>";
				$linkEnd = "</a>";
			}
			if(date('Ymd') == date('Ymd', $data->getDate())) {
				$label = $this->getTodayLabel();
			}
			else {
				$label = $this->getDateLabel($data->getDate());
			}
			$temp = str_replace('%%high%%', $data->getHigh($this->getTScale()), $this->highlow);
			$temp = str_replace('%%low%%', $data->getLow($this->getTScale()), $temp);				

			$output .= "<li class='" . $class . "'>"
			        .  $linkStart
			        .  "<h4>" .  $label ."</h4>"
			        .  "<div class='img'>" . "<img src='" . $this->getIconUrl() . '/' . $data->getConditionSlug() . ".png'></div>"
			        .  "<div class='text'>" . $data->getPrecip($this->getMScale()) . " " . $data->getConditionText() . "</div>"
			        .  "<div class='temp'>" . $temp . "</div>"
			        .  $linkEnd
			        .  "</li>";
			$dayCount++;
		}
		$output .= "</div>";
		return $output;
	}
	function getForecastLink($day) {
		return("");
	}
	function getTodayLabel() {
		return $this->todaylabel;
	}
	function setTodayLabel($tlabel) {
		$this->todaylabel = $tlabel;
		return $this->todaylabel;
	}
	function getDateLabel($date) {
                // First we do these easy date replacements
                $label = str_replace('%%weekday%%', date("l", $date), $this->datelabel);
/*
                $label = str_replace('%%day%%', $day, $label);
                $label = str_replace('%%month%%', $month, $label);
                $label = str_replace('%%year%%', $year, $label);
*/
		return $label;
	}
	function setDateLabel($tlabel) {
		$this->datelabel = $tlabel;
		return $this->datelabel;
	}
	function getOrient() {
		return $this->displayDir;
	}
	function setOrient($orient) {
		$this->displayDir = $orient;
		return $this->displayDir;
	}
	function getTScale() {
		return $this->displayTemp;
	}
	function setTScale($tscale) {
		$this->displayTemp = $tscale;
		return $this->displayTemp;
	}
	function getMScale() {
		return $this->displayMeas;
	}
	function setMScale($mscale) {
		$this->displayMeas = $mscale;
		return $this->displayMeas;
	}
	function getTimezone() {
		return($this->timezone);
	}
	function setTimezone($timezone) {
		$this->timezone = $timezone;
		return($this->timezone);
	}
	function getLocation() {
		return($this->location);
	}
	function setLocation($location) {
		$this->location = $location;
		return($this->location);
	}
	function getDays() {
		return($this->numdays);
	}
	function setDays($days) {
		$this->numdays = $days;
		return($this->numdays);
	}
	function getDaysLink() {
		return($this->dayslink);
	}
	function setDaysLink($dayslink) {
		$this->dayslink = $dayslink;
		return($this->dayslink);
	}
	function getCache() {
		return($this->cache);
	}
	function setCache($cache) {
		$this->cache = $cache;
		return($this->cache);
	}
	function getCaption() {
		return($this->caption);
	}
	function setCaption($caption) {
		$this->caption = $caption;
		return($this->caption);
	}
	function getClass() {
		return($this->class);
	}
	function setClass($class) {
		$this->class = $class;
		return($this->class);
	}
}
