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
		add_filter('plugin_action_links', array(&$this, 'settings_link'), 10, 2 );
		add_shortcode('forecast', array(&$this, 'shortcode'));
                add_action('admin_menu', array(&$this, 'admin'));
	        add_action('admin_init', array(&$this, 'settings_init') );
		$this->options = get_option('wp_wunderground', array());
	
		foreach($this->options as $key=> $value) {
			$this->{$key} = $value;
		}



		//add_ action('wp_head', array(&$this, 'css'));
	}

	function settings_init() {
		register_setting( 'wp_wunderground_options', 'wp_wunderground', array(&$this, 'sanitize_settings') );
	}

	function sanitize_settings($input) {
		return $input;
	}


	function admin() {
		add_options_page('WP Wunderground', 'WP Wunderground', 'administrator', 'wp_wunderground', array(&$this, 'admin_page'));
	}
	function admin_page() {
	?>
        <div class="wrap">
        <h2>WP Wunderground: Weather Forecasts for WordPress</h2>
        <h3>Settings only compatible with Weather Underground data Source</h3>
        <div class="postbox-container" style="width:65%;">
            <div class="metabox-holder">
                <div class="meta-box-sortables">
                    <form action="options.php" method="post">
		<?php
		wp_nonce_field('update-options');
		settings_fields('wp_wunderground_options');
		$rows[] = array('id' => 'wp_wunderground_location'
		          , 'label' => __('Location', 'wp_wunderground')
		          , 'content' => "<input type='text' name='wp_wunderground[location]' id='wp_wunderground_location' value='".esc_attr__($this->location)."' size='40' style='width:95%!important;' /><br /><small>The location can be entered as: ZIP code (US or Canadian); city state; city, state; city; state; country; airport code (3-letter or 4-letter); lat, long.</small>"
		          , 'desc' => 'The location for the forcast.'
		    );
		$rows[] = array('id' => 'wp_wunderground_numdays'
	                  , 'label' => __('Days of Forecast', 'wp_wunderground')
	                  , 'desc' => 'How many days would you like to display in the forecast? Supports up to 6.'
		          , 'content' => $this->buildDays()
		);

		$rows[] = array(
			'id' => 'wp_wunderground_measurement',
		        'label' => __('Degree (&deg;) Measurement', 'wp_wunderground'),
		        'desc' => 'Are you metric or U.S., baby?',
			'content' => $this->buildMeasurement()
		);

		$rows[] = array(
		        'id' => 'wp_wunderground_caption',
		        'label' => __('Forecast Caption', 'wp_wunderground'),
		        'content' => "<input type='text' name='wp_wunderground[caption]' id='wp_wunderground_caption' value='".esc_attr__($this->caption)."' size='40' style='width:95%!important;' />",
		        'desc' => 'This will display above the forecast. Think of it like a forecast title.'
		    );

		$rows[] = array(
		        'id' => 'wp_wunderground_datelabel',
		        'label' => __('"All Dates" Label', 'wp_wunderground'),
		        'content' => "<input type='text' name='wp_wunderground[datelabel]' id='wp_wunderground_datelabel' value='".esc_attr__($this->datelabel)."' size='40' style='width:95%!important;' />",
		        'desc' => 'How all dates appear by default. See instructions in the "Date Formatting" section of the box on the right &rarr;'
		    );

		$rows[] = array(
		        'id' => 'wp_wunderground_todaylabel',
		        'label' => __('"Today\'s Date" Label', 'wp_wunderground'),
		        'content' => "<input type='text' name='wp_wunderground[todaylabel]' id='wp_wunderground_todaylabel' value='".esc_attr__($this->todaylabel)."' size='40' style='width:95%!important;' />",
		        'desc' => 'How today\'s date appears (overrides All Dates format). See instructions in the "Date Formatting" section of the box on the right &rarr;'
		);

		$rows[] = array(
		        'id' => 'wp_wunderground_highlow',
		        'label' => __('"High/Low" Formatting', 'wp_wunderground'),
		        'desc' => 'See instructions in the "Highs &amp; Lows Formatting" section of the box on the right &rarr;',
		        'content' => "<input type='text' name='wp_wunderground[highlow]' id='wp_wunderground_highlow' value='".htmlspecialchars($this->highlow)."' size='40' style='width:95%!important;' />"
		);


		$rows[] = array(
			'id' => 'wp_wunderground_icon_set',
		        'label' => __('Icon Set', 'wp_wunderground'),
		        'desc' => 'How do you want your weather icons to look?',
		                'content' => $this->buildIconSet()
		);

		$checked = (empty($this->showlink) || $this->showlink == 'yes') ? ' checked="checked"' : '';
		$rows[] = array(
		        'id' => 'wp_wunderground_cache',
		        'label' => __('Use Cache', 'wp_wunderground'),
		        'desc' => 'Cache the results to prevent fetching the forecast on each page load. <strong>Highly encouraged.</strong>',
		        'content' => "<p><label for='wp_wunderground_cache'><input type='hidden' name='wp_wunderground[cache]' value='no' /><input type='checkbox' name='wp_wunderground[cache]' value='yes' id='wp_wunderground_cache' $checked /> Cache forecast results</label></p>"
		);

		$checked = (empty($this->showlink) || $this->showlink == 'yes') ? ' checked="checked"' : '';

		$rows[] = array(
		        'id' => 'wp_wunderground_showlink',
		        'label' => __('Give Thanks', 'wp_wunderground'),
		        'desc' => 'Checking the box tells the world you use this free plugin by adding a link to your footer. If you don\'t like it, you can turn it off, so please enable.',
		        'content' => "<p><label for='wp_wunderground_showlink'><input type='hidden' name='wp_wunderground[showlink]' value='no' /><input type='checkbox' name='wp_wunderground[showlink]' value='yes' id='wp_wunderground_showlink' $checked /> Help show the love.</label></p>"
		);

		$this->postbox('wp_wundergroundsettings',__('Store Settings', 'wp_wunderground'), $this->form_table($rows), false);

		?>
                        <input type="hidden" name="page_options" value="<?php foreach($rows as $row) { $output .= $row['id'].','; } echo substr($output, 0, -1);?>" />
                        <input type="hidden" name="action" value="update" />
                        <p class="submit">
                        <input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes', 'wp_wunderground') ?>" />
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div class="postbox-container" style="width:34%;">
            <div class="metabox-holder">
                <div class="meta-box-sortables">
                <?php $this->postbox('wp_wundergroundhelp',__('Configuring This Plugin', 'wp_wunderground'), $this->configuration(), true);  ?>
                </div>
            </div>
        </div>
    </div>
    <?php
	}

    function buildDays() {
        $c = ' selected="selected"';
        $output = '<select id="wp_wunderground_numdays" name="wp_wunderground[numdays]">';
                $output .= '    <option value="1"'; if($this->numdays == '1') { $output .= $c; } $output .= '>1 Day</option>';
                $output .= '    <option value="2"'; if($this->numdays == '2') { $output .= $c; } $output .= '>2 Days</option>';
                $output .= '    <option value="3"'; if($this->numdays == '3') { $output .= $c; } $output .= '>3 Days</option>';
                $output .= '    <option value="4"'; if($this->numdays == '4') { $output .= $c; } $output .= '>4 Days</option>';
                $output .= '    <option value="5"'; if($this->numdays == '5') { $output .= $c; } $output .= '>5 Days</option>';
                $output .= '    <option value="6"'; if($this->numdays == '6') { $output .= $c; } $output .= '>6 Days</option>';
                $output .= '</select>';
                $output .= '<label for="wp_wunderground_numdays" style="padding-left:10px;"># of Days in Forecast:</label>';
                return $output;
        }

    function buildMeasurement() {
        $c = ' selected="selected"';
        $output = '<select id="wp_wunderground_measurement" name="wp_wunderground[measurement]">';
                $output .= '    <option value="fahrenheit"'; if($this->measurement == 'fahrenheit') { $output .= $c; } $output .= '>U.S. (&deg;F)</option>';
                $output .= '    <option value="celsius"'; if($this->measurement == 'celsius') { $output .= $c; } $output .= '>Metric (&deg;C)</option>';
                $output .= '</select>';
                $output .= '<label for="wp_wunderground_measurement" style="padding-left:10px;">Fahrenheit or Celsius:</label>';
                return $output;
        }


	function settings_link( $links, $file ) {
		static $this_plugin;
		if( ! $this_plugin )
			$this_plugin = plugin_basename(__FILE__);
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=wp_wunderground' )
			               . '">' . __('Settings', 'wp_wunderground') . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		}
		return $links;
	}

    function buildIconSet() {
        $c = ' selected="selected"';
        $output = '<label for="wp_wunderground_icon_set" style="padding-right:10px;">Icon Set:</label>';
        $output .= '<select id="wp_wunderground_icon_set" name="wp_wunderground[icon_set]">';
                $output .= '    <option value="Default"'; if($this->icon_set == 'Default') { $output .= $c; } $output .= '>Default</option>';
                $output .= '    <option value="Smiley"'; if($this->icon_set == 'Smiley') { $output .= $c; } $output .= '>Smiley</option>';
                $output .= '    <option value="Generic"'; if($this->icon_set == 'Generic') { $output .= $c; } $output .= '>Generic</option>';
                $output .= '    <option value="Old School"'; if($this->icon_set == 'Old School') { $output .= $c; } $output .= '>Old School</option>';
                $output .= '    <option value="Cartoon"'; if($this->icon_set == 'Cartoon') { $output .= $c; } $output .= '>Cartoon</option>';
                $output .= '    <option value="Mobile"'; if($this->icon_set == 'Mobile') { $output .= $c; } $output .= '>Mobile</option>';
                $output .= '    <option value="Simple"'; if($this->icon_set == 'Simple') { $output .= $c; } $output .= '>Simple</option>';
                $output .= '    <option value="Contemporary"'; if($this->icon_set == 'Contemporary') { $output .= $c; } $output .= '>Contemporary</option>';
                $output .= '    <option value="Helen"'; if($this->icon_set == 'Helen') { $output .= $c; } $output .= '>Helen</option>';
                $output .= '    <option value="Incredible"'; if($this->icon_set == 'Incredible') { $output .= $c; } $output .= '>Incredible</option>';
                $output .= '</select>';

                $output .= '
                        <div style="margin-top:1em; text-align:center;">
                        <div style="padding-right:10px; width:100px; height:75px; float:left;"><img src="http://icons-ecast.wxug.com/i/c/a/clear.gif" width="50" height="50" /><img src="http://icons-ecast.wxug.com/i/c/a/rain.gif" width="50" height="50" /><br />Default</div>
                        <div style="padding-right:10px; width:100px; height:67px; float:left; padding-top:8px;"><img src="http://icons-ecast.wxug.com/i/c/b/clear.gif" width="42" height="42" /><img src="http://icons-ecast.wxug.com/i/c/b/rain.gif" width="42" height="42" /><br />Smiley</div>
                        <div style="padding-right:10px; width:100px; height:75px; float:left;"><img src="http://icons-ecast.wxug.com/i/c/c/clear.gif" width="50" height="50" /><img src="http://icons-ecast.wxug.com/i/c/c/rain.gif" width="50" height="50" /><br />Generic</div>
                        <div style="padding-right:10px; width:100px; height:67px; float:left; padding-top:8px;"><img src="http://icons-ecast.wxug.com/i/c/d/clear.gif" width="42" height="42" /><img src="http://icons-ecast.wxug.com/i/c/d/rain.gif" width="42" height="42" /><br />Old School</div>
                        <div style="padding-right:10px; width:100px; height:67px; float:left; padding-top:8px;"><img src="http://icons-ecast.wxug.com/i/c/e/clear.gif" width="42" height="42" /><img src="http://icons-ecast.wxug.com/i/c/e/rain.gif" width="42" height="42" /><br />Cartoon</div>
                        <div style="padding-right:10px; width:100px; height:67px; float:left; padding-top:8px;"><img src="http://icons-ecast.wxug.com/i/c/f/clear.gif" width="42" height="42" /><img src="http://icons-ecast.wxug.com/i/c/f/rain.gif" width="42" height="42" /><br />Mobile</div>
                        <div style="padding-right:10px; width:100px; height:75px; float:left;"><img src="http://icons-ecast.wxug.com/i/c/g/clear.gif" width="50" height="50" /><img src="http://icons-ecast.wxug.com/i/c/g/rain.gif" width="50" height="50" /><br />Simple</div>
                        <div style="padding-right:10px; width:100px; height:75px; float:left;"><img src="http://icons-ecast.wxug.com/i/c/h/clear.gif" width="50" height="50" /><img src="http://icons-ecast.wxug.com/i/c/h/rain.gif" width="50" height="50" /><br />Contemporary</div>
                        <div style="padding-right:10px; width:100px; height:75px; float:left;"><img src="http://icons-ecast.wxug.com/i/c/i/clear.gif" width="50" height="50" /><img src="http://icons-ecast.wxug.com/i/c/i/rain.gif" width="50" height="50" /><br />Helen</div>
                        <div style="padding-right:10px; width:100px; height:75px; float:left;"><img src="http://icons-ecast.wxug.com/i/c/k/clear.gif" width="50" height="50" /><img src="http://icons-ecast.wxug.com/i/c/k/rain.gif" width="50" height="50" /><br />Incredible</div>
                        </div>
                ';

                return $output;
    }

    function postbox($id, $title, $content, $padding=false) {
        ?>
            <div id="<?php echo $id; ?>" class="postbox">
                <div class="handlediv" title="Click to toggle"><br /></div>
                <h3 class="hndle"><span><?php echo $title; ?></span></h3>
                <div class="inside" <?php if($padding) { echo 'style="padding:10px; padding-top:0;"'; } ?>>
                    <?php echo $content; ?>
                </div>
            </div>
        <?php
    }

    function configuration() {
    $date2 = date('m-d-y');
    $date = date('m/d/Y');
    $weekday = date('l');
        $html = <<<EOD
        <h4>Adding the Forecast to your Content</h4>
        <p class="howto updated" style="padding:1em;">If you configure the settings to the left, all you will need to do is add <code>[forecast]</code> to your post or page content or text widget to add the forecast table.</p>
        <h4>Date Formatting</h4>
        <p>You can use the following tags: <code>%%weekday%%</code>, <code>%%day%%</code>, <code>%%month%%</code>, <code>%%year%%</code>, as well as using <a href="http://www.php.net/manual/en/function.date.php" target="_blank">PHP date formatting</a>.</p>
        <h4>Example date options:</h4>
        <ul>
                <li><code>Today's Weather (%%weekday%%)</code> <em>outputs as:&nbsp;</em> <strong>Today's Weather ($weekday)</strong></li>
                <li><code>%%day%%<strong>/</strong>%%month%%<strong>/</strong>%%year%% Weather</code> <em>outputs as:&nbsp;</em> <strong>$date Weather</strong></li>
                <li><code>Weather on date("m/d/Y")</code> <em>outputs as:&nbsp;</em> <strong>Weather on $date</strong></li>
                <li><code>date("\W\e\a\\t\h\e\\r\ \\f\o\\r\ m-d-y")</code> <em>outputs as:&nbsp;</em> <strong>Weather for $date2</strong></li>
        </ul>
        <hr style="padding-top:1em; outline:none; border:none; border-bottom:1px solid #ccc;"/>
        <h4>Highs &amp; Lows Formatting</h4>
        <p>Use the following tags to modify how low and high temps display: <code>%%high%%</code> and <code>%%low%%</code>.</p>
        <ul>
                <li><strong>HTML is fully supported. This allows you to color-code:</strong><br /><code>&lt;span style=&quot;color:red;&quot;&gt;%%high%%&lt;/span&gt;/&lt;span style=&quot;color:blue;&quot;&gt;%%low%%&lt;/span&gt;</code> <em>outputs as:&nbsp;</em> <span style="color:red;">85</span>/<span style="color:blue;">55</span></li>
                <li><strong>Choose to only show high or low:</strong><br /><code>High of %%high%%.</code> <em>outputs as:&nbsp;</em> High of 85.</li>
                <li><strong>Use CSS classes too:</strong><br /><code>&lt;div class=&quot;temp&quot;&gt;High of &lt;span class=&quot;temphigh&quot;&gt;%%high%%&lt;/span&gt;&lt;br /&gt;Low of &lt;span class=&quot;templow&quot;&gt;%%low%%&lt;/span&gt;&lt;/div&gt;</code> <em>outputs as:&nbsp;</em> <div class="temp">High of <span class="temphigh">85</span><br />Low of <span class="templow">55</span></div></li>
        </ul>
        <hr style="padding-top:1em; outline:none; border:none; border-bottom:1px solid #ccc;"/>

        <h4>Using the <code>[forecast]</code> Shortcode</h4>

        <p>If you're a maniac for shortcodes, and you want all control all the time, this is a good way to use it.</p>

        <p><code>[forecast location="Tokyo, Japan" caption="Weather for Tokyo" measurement='F' todaylabel="Today" datelabel="date('m/d/Y')" highlow='%%high%%&deg;/%%low%%&deg;' numdays="3" iconset="Cartoon" class="css_table_class" cache="true" width="100%"]</code></p>

        <p><strong>The shortcode supports the following settings:</strong></p>
        <ul>
                <li><code>location="Tokyo, Japan"</code> - Use any city/state combo or US/Canada ZIP code
                </li><li><code>caption="Weather for Tokyo"</code> - Add a caption to your table (it's like a title)
                </li><li><code>measurement='F'</code> - Choose Fahrenheit or Celsius by using "F" or "C"
                </li><li><code>datelabel="date('m/d/Y')"</code> - Format the way the days display ("9/30/2012" in this example)
                </li><li><code>todaylabel="Today"</code> - Format how today's date appears ("Today" in this example)
                </li><li><code>highlow='%%high%%&deg;/%%low%%&deg;'</code> - Format how the highs & low temps display ("85&deg;/35&deg;" in this example)
                </li><li><code>numdays=3</code> - Change the number of days displayed in the forecast. Up to 6 day forecast.
                </li><li><code>iconset="Cartoon"</code> - Choose your iconset from one of 10 great options
                </li><li><code>cache="true"</code> - Whether to cache forecast results. Use <code>0</code> to disable (not recommended).
                </li><li><code>width="100%"</code> - Change the width of the forecast table
        </ul>

EOD;
        return $html;
	}

    // THANKS JOOST!
    function form_table($rows) {
        $content = '<table class="form-table" width="100%">';
        foreach ($rows as $row) {
            $content .= '<tr><th valign="top" scope="row" style="width:50%">';
            if (isset($row['id']) && $row['id'] != '')
                $content .= '<label for="'.$row['id'].'" style="font-weight:bold;">'.$row['label'].':</label>';
            else
                $content .= $row['label'];
            if (isset($row['desc']) && $row['desc'] != '')
                $content .= '<br/><small>'.$row['desc'].'</small>';
            $content .= '</th><td valign="top">';
            $content .= $row['content'];
            $content .= '</td></tr>';
        }
        $content .= '</table>';
        return $content;
    }

	function do_css($wpw_obj) {
/**
 * \todo CSS should be moved into a plugin configuration option with %% codes to replace certain items.
 */
		$widthp = floor((100 - ($wpw_obj->getDays() * 2)) / $wpw_obj->getDays());
		$icon = $wpw_obj->getIconPath() . "/clear.png";
		$info =  getimagesize($icon);
		if($info == false) {
			$width = $widthp . "%";
			$height = "auto";
		}
		else {
			$width  = $info[0] . "px";
			$height = $info[1] . "px";
		}
		$cssStr = "
.wpw-landscape {
	display:inline-block;
	vertical-align:top;
	align:center;
	margin: 0 1%;
	min-width: %%width%%;
}

.wpw-landscape div {
	width:50px;
	width: %%width%%;
	margin: 0 auto;
}

.wpw-portrait {
	display:block;
	clear:left;
	min-height: %%height%%;
}

.wpw-portrait > h4 + div {
	float:left;
	margin-right:5px;
}
.wpw-portrait > div {
	text-align:left;
}
	";
		$cssStr = str_replace('%%width%%', $width, $cssStr);
		$cssStr = str_replace('%%height%%', $height, $cssStr);
		return $cssStr;
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
		          , 'linkdays'    =>  "false"
		          , 'datelabel'   =>  "%%weekday%%"
		          , 'todaylabel'  =>  "Today"
		          , 'cache'       =>  false
		          , 'class'       =>  'wordpress_weather'
		          , 'highlow'     =>  '%%high%%&deg;/%%low%%&deg;'
		          , 'iconset'     =>  "humanity"
		          , 'orient'      =>  "horiz"
		          ), $atts );
		/*
		 * If the shortcode is just [forecast], pull from the options
		 */
		if(count($atts) == 1) {
                	foreach($this->options as $key=> $value) {
                        	$settings[$key] = $value;
                	}
		}
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
				if(intval($settings['cache']) >= 0)
					$wpw_obj->setCache($settings['cache']);
				else
					$wpw_obj->setCache(WPW_CACHETIME);
				break;
		}
		switch(strtolower($settings['orient'])) {
			case "vertical":
			case "v":
			case "portrait":
			case "p":
				$wpw_obj->setOrient($GLOBALS['ORIENT']->PORTRAIT);
				break;
			default:
				$wpw_obj->setOrient($GLOBALS['ORIENT']->LANDSCAPE);
				break;
		}

		$wpw_obj->getFeed();
		$output = $this->css($wpw_obj);
		$output .= $wpw_obj->displayFeed();
		return $output;
	}
}

add_action('widgets_init', create_function('', 'return register_widget("wordpressWeather");'));

