<?php   
/* 
Plugin Name: IndiePub Current Location
Plugin URI: http://independentpublisher.me/plugins/indiepub-current-location/
Description: Plugin for recording and showing your current location in WordPress Posts/Pages using a shortcode. Includes a Remote API for updating your current location from external scripts.
Author: Raam Dev
Version: 1.0 
Author URI: http://raamdev.com/
*/  

/**
 * Admin functions 
 */
function ncl_admin() {  
    include('indiepub-current-location-admin-page.php');
}

function ncl_admin_actions() {  
    add_options_page("Current Location Settings", "Current Location", 'manage_options', 'ncl_options', "ncl_admin");
}  
add_action('admin_menu', 'ncl_admin_actions');

function ncl_load_meta_boxes() {
    include('indiepub-current-location-meta-boxes.php');
	add_action( 'load-post.php', 'ncl_post_meta_boxes_setup' );
	add_action( 'load-post-new.php', 'ncl_post_meta_boxes_setup' );
}
add_action('admin_menu', 'ncl_load_meta_boxes');

/**
 * Add Settings link to the plugin page
 */

// Add settings link on plugin page
function ncl_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=ncl_options">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'ncl_plugin_settings_link' );


/* Add mapThis JavaScript to show popup map of location */
function mapThis_js() {
	wp_enqueue_script(
		'mapThis',
		plugins_url('/mapThis.js', __FILE__),
		array( 'jquery' )
	);
}
add_action('wp_enqueue_scripts', 'mapThis_js');


/**
 * Shortcode for displaying current location in posts/pages
 */
function ncl_show_location ($atts) {

	// Extract Shortcode Parameters/Attributes
    extract( shortcode_atts( array( 'display' => NULL, 'wikify' => NULL ), $atts ) );
	
	if ($display == 'coordinates') {
		$return_text = get_option('ncl_coords');
	} else if ($display == 'text') {
		$return_text = get_option('ncl_text');
	} else if ($display == 'date') {
		// If Jeffrey's "Time Since" plugin is installed, use that to make date more readable
		if(function_exists('time_since')) {
			$return_text = time_since(abs(get_option('ncl_updated_date')), time());
		} else {
			// Returns date formatted to RFC822, e.g., Fri, 20 Jul 12 03:31:49 +0000 
			$return_text = date(DATE_RFC822, get_option('ncl_updated_date'));
		}
	} else {
		$return_text = get_option('ncl_text'); 
	}
	if ($wikify == 'true') {
		if ("" != trim(get_option('ncl_wiki_url'))) {
			$return_text = '<a href="' . get_option('ncl_wiki_url') . '" target="_new">' . stripslashes($return_text) . '</a>';
		}
	}
		
	return $return_text;
}
add_shortcode('ncl-current-location', 'ncl_show_location');

/**
 * Remote API for updating Current Location from external scripts
 */
if (isset($_GET['ncl_api_key'])) {
	
	$updated = FALSE;
	$ncl_api_key = get_option('ncl_api_key');
	$ncl_api_enable = get_option('ncl_api_enable');
	
	// Check if the Remote API has been enabled
	if ($ncl_api_enable != "1") {
		echo "Remote API is Disabled.";
		exit;
	}
	
	// Make sure the API key is valid
	if (trim($ncl_api_key) == "" || $ncl_api_key != $_GET['ncl_api_key']) {
		echo "Invalid API Key.";
		exit;
	} else {
		if (isset($_GET['location'])){
			$ncl_text = trim($_GET['location']);
        	update_option('ncl_text', $ncl_text);
			$updated = TRUE;
		}
		if (isset($_GET['coordinates'])){
	        $ncl_coords = trim($_GET['coordinates']);
	        update_option('ncl_coords', $ncl_coords);
			$updated = TRUE;
		}
		if (isset($_GET['wiki_url'])){
	        $ncl_wiki_url = trim(urldecode($_GET['wiki_url']));
	        update_option('ncl_wiki_url', $ncl_wiki_url);
			$updated = TRUE;
		}
		if (isset($_GET['updated'])){
	        $ncl_updated_date = trim($_GET['updated']);
	        update_option('ncl_updated_date', $ncl_updated_date);
			$updated = TRUE;
		}
		// If we're updating the location, but no date has been passed, update the current date with now
		if ($updated == TRUE && !isset($_GET['updated'])) {
	        update_option('ncl_updated_date', time());
		}
		if(isset($_GET['rss'])) {
			header("Content-Type: application/rss+xml; charset=ISO-8859-1");
			$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
			$rssfeed .= '<rss version="2.0">';
			$rssfeed .= '    <channel>';
			$rssfeed .= '        <item>';
			$rssfeed .= '            <title>' . get_option('ncl_text') . '</title>';
			$rssfeed .= '            <link>http://raamdev.com/travels/#map</link>';
			$rssfeed .= '        </item>';
			$rssfeed .= '    </channel>';
			$rssfeed .= '</rss>';
			echo $rssfeed;
			exit;
		}

		// If the API key was valid, but no other parameters were passed, just show the current location
		if ($updated == FALSE) {
			echo get_option('ncl_text') . " (". get_option('ncl_coords') .")" . " (". get_option('ncl_wiki_url') .")". " (". get_option('ncl_updated_date') .")";
		} else if ($updated == TRUE) {
			if($ncl_text) { $updated_text = $ncl_text; }
			if($ncl_coords) { $updated_text = $updated_text . " (" . $ncl_coords . ")"; }
			if($ncl_wiki_url) { $updated_text = $updated_text . " (" . $ncl_wiki_url . ")"; }
			if($ncl_updated_date) { $updated_text = $updated_text . " (" . date(DATE_RFC822, $ncl_updated_date) . ")"; }
			echo trim($updated_text);
		}
		
		exit;
	}
}
?>
