<?php
/*
Plugin Name: WP Wunderground
Plugin URI: http://www.seodenver.com/wunderground/
Description: Get accurate and beautiful weather forecasts powered by Wunderground.com for your content or your sidebar.
Version: 2.0
Author: 
Author URI:
*/
require_once("wpw-weatherdata.inc");
define('WPW_CACHETIME', 5);
add_action('wp_head', 'wp_forecast_css');


/**
 * \class wordpressWeather
 * \brief Wordpress plugin to display the weather
 *
 * This class extends the normal Wordpress Widget class into a weather widget.
 *   It can also be called using the shortcode [forecast], Here is the current
 *   list of supported parameters: source, location, measurement, caption,
 *   numdays, linkdays, datelabel, todaylabel, cache, class, highlow, iconset.
 */
class wordpressWeather extends WP_Widget {
	function __construct() {
		parent::__construct(false, $name = 'WP_Widget');
		add_shortcode('forecast', array(&$this, 'shortcode'));
		//add_action('wp_head', array(&$this, 'css'));
	}

	function do_css($wpw_obj) {
/**
 * \todo CSS should be moved into a plugin configuration option with %% codes to replace certain items.
 */
		$widthp = floor((100 - ($wpw_obj->getDays() * 2)) / $wpw_obj->getDays());
		list($width, $height) = getimagesize($wpw_obj->getIconPath() . "/clear.png"); 
		return "
.wpw-horitz {
	display:inline-block;
	vertical-align:top;
	align:center;
	margin: 0 1%;
	min-width: " . $width . "px;
	/*width: " . $widthp . "%;*/
}

.wpw-vert {
	display:block;
}
	";
	}

	function css($wpw_obj) {
		$output  = "<style type='text/css'>";
		$output .= $this->do_css($wpw_obj);
		$output .= "</style>";
		return $output;
	}

	function shortcode($atts) {
		$settings = shortcode_atts( array(
		            'source'      =>  "wunderground"
		          , 'location'    =>  "46534"
		          , 'measurement' =>  "F"
		          , 'caption'     =>  "Knox, Indiana"
		          , 'numdays'     =>  5
		          , 'linkdays'    =>  "true"
		          , 'datelabel'   =>  "%%weekday%%"
		          , 'todaylabel'  =>  "Today"
		          , 'cache'       =>  false
		          , 'class'       =>  'wordpress_weather'
		          , 'highlow'     =>  '%%high%%&deg;/%%low%%&deg;'
		          , 'iconset'     =>  "humanity"
		          ), $atts );
/*

		          , 'align'       =>  "center" // Should be handled by CSS
		          , 'width'       =>  $this->width
		          , 'table'       =>  false
		          , 'type'        =>  'table'
*/
		switch(strtolower($settings['source'])) {
			case "accuweather":
				include("wpw-backaccuweather.inc");
				$wpw_obj = new wpw_backAccuWeather();
				break;
			case "wunderground":
			default:
				include("wpw-backwunderground.inc");
				$wpw_obj = new wpw_backWunderground();
				break;
		}
		$wpw_obj->setLocation(esc_html($settings['location']));
		switch(strtolower($settings['measurement'])) {
			case "c":
				$wpw_obj->setTScale($GLOBALS['TSCALE']->CELSIUS);
				$wpw_obj->setMScale($GLOBALS['MSCALE']->METRIC);
				break;
			default:
				$wpw_obj->setTScale($GLOBALS['TSCALE']->FAHRENHEIT);
				$wpw_obj->setMScale($GLOBALS['MSCALE']->ENGLISH);
				break;
		}
		$wpw_obj->setCaption(esc_html($settings['caption']));
		$wpw_obj->setDays(absint($settings['numdays']));
		switch(strtolower($settings['linkdays'])) {
			case "true":
			case "on":
				$wpw_obj->setDaysLink(true);
				break;
			default:
				$wpw_obj->setDaysLink(false);
				break;
		}
		$wpw_obj->setClass(absint($settings['class']));
		switch(strtolower($settings['cache'])) {
			case "true":
			case "on":
				$wpw_obj->setCache(WPW_CACHETIME);
				break;
			case "false":
			case "off":
				$wpw_obj->setCache(0);
				break;
			default:
				if(absint($settings['cache']))
					$wpw_obj->setCache($settings['cache']);
				else
					$wpw_obj->setCache(WPW_CACHETIME);
				break;
		}

		$wpw_obj->getFeed();
		$output = $this->css($wpw_obj);
		$output .= $wpw_obj->displayFeed();
		return $output;
	}
}

add_action('widgets_init', create_function('', 'return register_widget("wordpressWeather");'));

