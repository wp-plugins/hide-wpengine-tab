<?php
/*
Plugin Name: 		Hide WPEngine Tab
Version: 			1.0.1
Description: 		This plugin is built to make it super easy to limit access to WPengine's Access tab so that only select people can rebuild the staging environment.
Author: 			Aaron Vanderzwan
Author URI: 		http://www.aaronvanderzwan.com
Text Domain: 		hide-wpengine-tab
Domain Path: 		/languages
License:			GPLv2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'HWPET_VERSION', 	'1.0.1' );
define( 'HWPET_URL', 		plugins_url('', __FILE__) );

// only run plugin in the admin interface
if ( !is_admin() )
	return false;


add_action('admin_menu', 'hwpet_admin_menu');
function hwpet_admin_menu() {
	add_options_page('Hide WPEngine Tab', 'Hide WPEngine Tab', 'manage_options', 'hide-wpe-tab', 'hwpet_admin_page');
}

function hwpet_admin_page() {
	$hwpet_settings = get_option('hwpet_settings');
	$notice = '';
	
	if( isset( $_POST['HWPET_Settings'] ) ) :
		$hwpet_settings = $_POST['user_str'];
		?>
		    <div id="message" class="updated">
		    	<p><?php _e('User exceptions updated successfully', 'hwpet-plugin'); ?></p>
		    </div>
		<?php
		update_option('hwpet_settings', $_POST['user_str']);
	endif;
	?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2>Hide WPEngine Tab</h2>
	<p>WPEngine is a fantastic hosting provider with a great function, one click staging environment.  This plugin is built to make it super easy to limit access to that button so that people do not overwrite changes by accident.  As always, if you are not sure if there are changes on STAGING... <strong>please backup STAGING environment before recreating it!!!</strong></p>
	<h3>User Exceptions</h3>
	<form method=post>
   		<input type="hidden" name="HWPET_Settings" value="1" />
		<input name="user_str" id="user_str" type="text" value="<?= $hwpet_settings; ?>" class="regular-text code">&nbsp;&nbsp;<em>A comma separated list of usernames that are able to view the WPEngine Tab.</em><br>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save"></p>  
	</form>
</div>
	<?php
}

// WPENGINE STAGING 
// Remove all evidence of WP Engine from the Dashboard, unless the logged in user is "wpengine"
add_action('plugins_loaded', 'hwpet_hide_tab');
function hwpet_hide_tab() {
	$hwpet_settings = get_option('hwpet_settings');
	$users_arr = explode(',', $hwpet_settings);
	$users_arr = array_filter(array_map('trim', $users_arr));
	if(!in_array('wpengine', $users_arr)) array_push( $users_arr, 'wpengine' );
	
	global $current_user;
	$user = wp_get_current_user();
	if ( !in_array( $user->user_login, $users_arr ) ) {
		add_action( 'admin_init', 'jpry_remove_menu_pages' );
		add_action( 'admin_bar_menu', 'jpry_remove_admin_bar_links', 999 );
		add_action( 'admin_head', 'jpry_remove_wpe_currated' );
	}else{
	}
}

/**
* Remove the WP Engine menu page
*/
function jpry_remove_menu_pages() {
	remove_menu_page( 'wpengine-common' );
}

/**
* Remove the "WP Engine Quick Links" from the menu bar
*/
function jpry_remove_admin_bar_links( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wpengine_adminbar' );
}

/**
* Do not display the WP Engine Curated image for the curated plugins
*/
function jpry_remove_wpe_currated() {
	echo '
	<style type="text/css"> 
	.curated { display: none; }
	</style>
	';
}
?>