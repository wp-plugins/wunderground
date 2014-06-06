=== Weather Forecast - WP Wunderground ===
Tags: weather, weather.com, wunderground, weatherbug, forecast, widget, shortcode, Yahoo weather, Yahoo! Weather, wp-weather, wp weather, local weather, weather man, weather widget, cool weather, accuweather, get weather, wordpress weather
Requires at least: 2.8
Tested up to: 3.9.1
Stable tag: trunk
Contributors: katzwebdesign, katzwebservices
License: GPLv2 or later
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=WP%20Wunderground%20for%20WordPress&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8

Get accurate and beautiful weather forecasts powered by Wunderground.com for your content or your sidebar.

== Description ==

> ### We're getting ready to release the next version of the plugin!
> 
> Brand-new templates, translation support, current conditions & more!
>
> __We need testers.__ Please [download the new version](https://github.com/katzwebservices/Wunderground/archive/master.zip), try it out, and if you find issues, [report the issues](https://widget.uservoice.com/omnibox/Csq4WQZTBPGtRqSZXIsNA?mode=contact&locale=en&forum_id=254985&contact_us=true&accent_color=007DBF&embed_type=popover&trigger_method=custom_trigger&menu=true&screenshot_enabled=false&contact_enabled=true&feedback_enabled=false&smartvote=false).
> 
> &rarr; __[Read more about the new version](https://github.com/katzwebservices/Wunderground#readme)__ &larr;

<h3>Wunderground is the best WordPress weather site.</h3>

Wunderground.com has the most accurate and in-depth weather information. They're also not evil corporate giants, and are weather geeks, which is nice. 

This plugin uses the Wunderground API to gather its accurate forecasts.

<h3>If you want a great-looking weather forecast, use this plugin.</h3>
__This is the best-looking weather forecast plugin for WordPress.__ It looks great on many different templates out of the box.

The WP Wunderground plugin uses [10 great-looking icon sets](http://www.wunderground.com/weather/api/d/docs?d=resources/icon-sets) from Wunderground.com.

Check out the Screenshots tab for pictures.

### Using the WP Wunderground Weather Plugin

The plugin can be configured in two ways: 

1. Configure the plugin in the admin's WP Wunderground settings page, then add `[forecast]` to your content where you want the forecast to appear.
2. Go crazy with the `[forecast]` shortcode.

<h4>Using the `[forecast]` Shortcode</h4>
If you're a maniac for shortcodes, and you want all control all the time, this is a good way to use it.

`[forecast location="Tokyo, Japan" caption="Weather for Tokyo" measurement='F' todaylabel="Today" datelabel="date('m/d/Y')" highlow='%%high%%&deg;/%%low%%&deg;' numdays="3" iconset="Cartoon" class="css_table_class"]`

<strong>The shortcode supports the following settings:</strong>

* `location="Tokyo, Japan"` - Use any city/state combo or US/Canada ZIP code
* `caption="Weather for Tokyo"` - Add a caption to your table (it's like a title) 
* `measurement='F'` - Choose Fahrenheit or Celsius by using "F" or "C"
* `datelabel="date('m/d/Y')"` - Format the way the days display ("9/30/2012" in this example)
* `todaylabel="Today"` - Format how today's date appears ("Today" in this example)
* `highlow='%%high%%&deg;/%%low%%&deg;'` - Format how the highs & low temps display ("85&deg;/35&deg;" in this example)
* `numdays=3` - Change the number of days displayed in the forecast. Up to 6 day forecast.
* `iconset="Cartoon"` - Choose your iconset from one of 10 great options
* `class="css_table_class"` - Change the CSS class of the generated forecast table

<h4>Learn more on the <a href="http://www.seodenver.com/wunderground/">official plugin page</a></h4>

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
1. Activate the plugin
1. Go to the plugin settings page (under Settings > WP Wunderground)
1. Configure the settings on the page. (Instructions for some setting configurations are on the box to the right)
1. Click Save Changes.
1. When editing posts, use the `[forecast]` "shortcode" as described on this plugin's Description tab

== Screenshots ==

1. Embedded in content in the twentyten theme
1. Embedded in content in the twentyten theme, alternate settings
1. In the sidebar of the Motion theme
1. In the sidebar of the ChocoTheme theme
1. In the sidebar of the Piano Black theme
1. In the sidebar of the twentyten theme
1. Plugin configuration page

== Frequently Asked Questions == 

= How do I use my own icons? =

If you want to use your own icons, you would add a filter to the bottom of your theme's <code>functions.php</code> file:

<h3>Version 1.2+</h3>
<pre>
add_filter('wp_wunderground_forecast_icon', 'use_custom_wunderground_icons');

function use_custom_wunderground_icons($content=null) { 
	
	$myIconFolder = 'http://www.example.com/images/';
	
	$myFileType = '.gif';
	
	$content = preg_replace('/http\:\/\/icons\-ecast\.wxug\.com\/i\/c\/[a-z]\/(.*?)\.gif/ism', $myIconFolder.'$1'.$myFileType, $content);
	
	return $content;
}
</pre>

<h3>Version 1.1</h3>
<pre>
add_filter('wp_wunderground_forecast', 'use_custom_wunderground_icons');

function use_custom_wunderground_icons($content=null) { 
	
	$myIconFolder = 'http://www.example.com/images/';
	
	$myFileType = '.gif';
	
	$content = preg_replace('/http\:\/\/icons\-ecast\.wxug\.com\/i\/c\/[a-z]\/(.*?)\.gif/ism', $myIconFolder.'$1'.$myFileType, $content);
	
	return $content;
}
</pre>

= I am unable to activate the plugin. = 
This plugin requires PHP5, and Version 1.0 does not check for PHP compatibility. In future versions, it will show a more meaningful error and also support PHP 4.

For now, however, options are to upgrade the version of PHP (contact your web host) or to not use the plugin.

= I want to modify the forecast output. How do I do that? =

<pre>
function replace_weather($content) {
	$content = str_replace('Rain', 'RAYNNNNN!', $content);
	$content = str_replace('Snow', 'SNNNOOOO!', $content);
	return $content;
}
add_filter('wp_wunderground_forecast', 'replace_weather');
</pre>

= What is the plugin license? =

* This plugin is released under a GPL license.

= Do I need a Wunderground account? =
Nope, no account needed.

= This plugin slows down my site. =
Version 1.1 of the plugin added forecast caching, so the forecast is only re-loaded every 6 hours.

For previous versions, it is recommended to use a caching plugin (such as WP Super Cache) with this plugin; that way the forecast isn't re-loaded every page load.

== Changelog ==

= 1.2.5.1 = 
* Quick fix for icon issues: the icon images were broken.

= 1.2.5 = 
* Fixed issue where checkbox state wasn't being reflected in settings (as mentioned <a href="http://wordpress.org/support/topic/plugin-weather-forecast-wp-wunderground-not-saving-checkbox-settings" rel="nofollow">in this support thread</a>)

= 1.2.4 = 
* Removed error generation when XML file cannot be read (Error on line 427, <a href="http://wordpress.org/support/topic/506565" rel="nofollow">as reported</a>). Now, it just outputs an HTML comment error message.

= 1.2.3 =
* Fixed bug where Degree Measurement select drop-down would not show saved state as Celsius, even though it was working properly. (thanks <a href="http://www.OwlConcept.com">Robson</a>)
* Added proper HTML escaping for High/Low formatting and temperature output

= 1.2.2 =
* Added GoDaddy compatibility by switching from `simplexml_load_file` to `wp_remote_fopen` and `simplexml_load_string` (<a href="http://wordpress.org/support/topic/490946">thanks, rjune</a>)

= 1.2.1 =
* Fixed issue with "Give thanks" link

= 1.2 =
* Improved data storage, fixing issues users were having with Celsius / Fahrenheit settings and setting the number of columns in a table
* A new `cache` option has been added to the shortcode. Add `cache=0` or `cache=` to the shortcode to disable storing forecasts. <strong>Not recommended;</strong> will dramatically slow down site.	
	* If you want to refresh the results, you can add `?cache=false` to your URL and the forecast will be updated.
* Added "width" option to shortcode to define table width. `100%` is the default. Use `width=0` or `width=` to disable hard-coding width in table.
* Changed the default high/low setting to add the degree symbol.
* Removed code whitespace when storing table for added speed
* Added CSS classes to forecast columns based on weather conditions. This will allow you to make "Partly Cloudy" columns gray, "Sunny" blue, etc.
* Added three new filters:
	* `wp_wunderground_forecast_cache` - How long results are cached for. Default: 6 hours.
	* `wp_wunderground_forecast_icon`
	* `wp_wunderground_forecast_conditions`
	* `wp_wunderground_forecast_temp`
* Rounded column width to two digits. Instead of `16.66666667%`, it's now `16.67%`

= 1.1 =
* Added data storage - the plugin will now store forecast tables for 6 hours. This should speed up the time it takes to load the forecasts.
* Added a check for PHP5 and `simplexml_load_file`, which are required for the plugin.

= 1.0 =
* Initial launch

== Upgrade Notice ==

= 1.2.5.1 = 
* Quick fix for icon issues: the icon images were broken.

= 1.2.5 = 
* Fixed issue where checkbox state wasn't being reflected in settings (as mentioned <a href="http://wordpress.org/support/topic/plugin-weather-forecast-wp-wunderground-not-saving-checkbox-settings" rel="nofollow">in this support thread</a>)

= 1.2.4 =
* Removed error generation when XML file cannot be read (Error on line 427, <a href="http://wordpress.org/support/topic/506565" rel="nofollow">as reported</a>). Now, it just outputs an HTML comment error message.

= 1.2.3 =
* Fixed bug where Degree Measurement select drop-down would not show saved state as Celsius, even though it was working properly. (thanks <a href="http://www.OwlConcept.com" rel="nofollow">Robson</a>)
* Added proper HTML escaping for High/Low formatting and temperature output

= 1.2.2 =
* Added GoDaddy compatibility by switching from `simplexml_load_file` to `wp_remote_fopen` and `simplexml_load_string` (<a href="http://wordpress.org/support/topic/490946">thanks, rjune</a>)

= 1.2.1 =
* Fixed issue with "Give thanks" link

= 1.2 =
* Improved data storage, fixing issues users were having with Celsius / Fahrenheit settings and setting the number of columns in a table
* Many other updates & improvements - cheek the changelog.

= 1.1 = 
* Added data storage - the plugin will now store forecast tables for 6 hours. This should speed up the time it takes to load the forecasts.
* Added a check for PHP5 and `simplexml_load_file`, whichare required for the plugin. Users will no longer get `Parse error: syntax error, unexpected '{' in {your-site}/wunderground.php on line 412` error

= 1.0 = 
* Blastoff!