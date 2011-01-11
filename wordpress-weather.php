<?php
/*
Plugin Name: Wordpress Weather
Plugin URI: http://www.seodenver.com/wunderground/
Description: Get accurate and beautiful weather forecasts powered by Wunderground.com for your content or your sidebar.
Version: 0.2
Author: 
Author URI:
*/
require_once("wpw-weatherdata.inc");
define('WPW_CACHETIME', 5);

class wordpressWeather extends WP_Widget {
	function wordpressWeather() {
		parent::WP_Widget(false, $name = 'GcalSidebar');
		add_shortcode('wordpress-weather', array(&$this, 'shortcode'));
	}

	function shortcode($atts) {
		$settings = shortcode_atts( array(
		            'source'      =>  "base"
		          , 'location'    =>  "46534"
		          , 'displaytemp' =>  "fahrenheit"
		          , 'caption'     =>  "Knox, Indiana"
		          , 'numdays'     =>  5
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
				include("wpw-backwunderground.inc");
				$wpw_obj = new wpw_backWunderground();
				break;
			default:
				include("wpw-backbase.inc");
				$wpw_obj = new wpw_backBase();
				break;
		}
		$wpw_obj->setLocation(esc_html($settings['location']));
		switch(strtolower($settings['displaytemp'])) {
			case "celsius":
				$wpw_obj->setTScale($GLOBALS['TSCALE']->CELSIUS);
				break;
			default:
				$wpw_obj->setTScale($GLOBALS['TSCALE']->FAHRENHEIT);
				break;
		}
		$wpw_obj->setCaption(esc_html($settings['caption']));
		$wpw_obj->setDays(absint($settings['numdays']));
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
		return $wpw_obj->displayFeed();
	}
}

add_action('widgets_init', create_function('', 'return register_widget("wordpressWeather");'));

