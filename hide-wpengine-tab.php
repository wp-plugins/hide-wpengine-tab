<?php
/*
Plugin Name: 		Hide WPEngine Tab
Version:			1.1.2
Description: 		This plugin makes it super easy to limit access to the WP Engine tab.  This protects the staging environment and other WPE actions.
Author:				Aaron Vanderzwan
Author URI: 		http://www.aaronvanderzwan.com/
Text Domain: 		hide-wpengine-tab
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

define( 'HWPET_VERSION', 	'1.1.2' );
define( 'HWPET_URL', 		plugins_url('', __FILE__) );

// only run plugin in the admin interface
if ( !is_admin() || !is_wpe() )
	return false;


add_action('admin_menu', 'hwpet_admin_menu');
function hwpet_admin_menu() {
	add_options_page('Hide WPEngine Tab', 'Hide WPEngine Tab', 'manage_options', 'hide-wpe-tab', 'hwpet_admin_page');
}

function hwpet_admin_page() {
	$hwpet_users = get_option('hwpet_settings');
	$hwpet_lock = get_option('hwpet_lock');
	$hwpet_locked_by = get_option('hwpet_locked_by');
	$hwpet_lock_date = get_option('hwpet_lock_date');
	$hwpet_lock_message = get_option('hwpet_lock_message');
	
	if( isset( $_POST['HWPET_Settings'] ) ) :
		$hwpet_users = $_POST['hwpet_users'];
		$lock_updated = $hwpet_lock != $_POST['hwpet_lock'];
		$hwpet_lock = $_POST['hwpet_lock'];
		// $hwpet_locked_by && $hwpet_date_locked set from current user
		$hwpet_lock_message = $_POST['hwpet_lock_message'];
		?>
	    <div id="message" class="updated">
	    	<p><?php _e('Settings updated successfully.  <a href="">Reload</a> page to see WPEngine tab visibility change.', 'hwpet-plugin'); ?></p>
	    </div>
		<?php
		// Users Update
		update_option('hwpet_settings', $hwpet_users );
		
		
		// Lock Update
		if( empty( $hwpet_lock ) ) $hwpet_lock = 'off';
		update_option('hwpet_lock', $hwpet_lock );
		
		
		// Locked By & Date Locked
		if( $lock_updated ) {
			if( $hwpet_lock == 'off' ) {
				$current_user_login = '';
			}else{
				global $current_user;
				$user = wp_get_current_user();
				$current_user_login = $user->user_login;
			}
			$hwpet_locked_by = $current_user_login;
			update_option('hwpet_locked_by', $current_user_login );
			
			$hwpet_lock_date = current_time('mysql');
			update_option('hwpet_lock_date', $hwpet_lock_date );
		}
		
		
		// Confirm Message Update
		update_option('hwpet_lock_message', $hwpet_lock_message );
	endif;
	?>
	
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	
	<h2>Hide WPEngine Tab</h2>
	<p>WPEngine is a fantastic hosting provider with a great function - the one click staging environment.  Hide WPEngine Tab is meant to make it ridiculously easy to limit access to that button so that people do not overwrite changes by accident.  As always, if you are not sure if there are changes on STAGING... <strong>please backup STAGING environment before recreating it!!!</strong></p>
	
	<form id="hwpet-form" method="post">
   		<input type="hidden" name="HWPET_Settings" value="1" />
		
		<h3>User Access</h3>
		<p>A comma separated list of usernames that are able to view the WPEngine Tab. Note: The "wpengine" user automatically has access.</p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Usernames</th>
					<td>
						<input name="hwpet_users" id="hwpet_users" type="text" value="<?= $hwpet_users; ?>" class="regular-text code">
					</td>
				</tr>
			</tbody>
		</table>
		
		<h3>Lock Staging Environment</h3>
		<p>This is meant to be an extra precaution.  If staging is "locked" a confirmation message will popup when someone clicks on the "create staging area" button.</p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Lock Staging</th>
					<td>
						<input name="hwpet_lock" id="hwpet_lock" type="checkbox" <?= $hwpet_lock == "on" ? 'checked="checked"' : '' ?> />
						<label for="hwpet_lock">Lock staging to add a confirmation message to "create staging area" clicks.</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Locked By</th>
					<td>
						<?= $hwpet_lock == "on" ? $hwpet_locked_by : '<em>Not currently locked</em>' ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Date Locked</th>
					<td>
						<?= $hwpet_lock == "on" ? $hwpet_lock_date : '<em>Not currently locked</em>' ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Confirmation Message</th>
					<td>
						<textarea id="hwpet_lock_message" cols="50" rows="8" name="hwpet_lock_message"><?= $hwpet_lock_message ?></textarea>
						<?php $default_message = "Please contact the web administrator to schedule a time to recreate this environment."; ?>
						<p>Example Message: "<?= $default_message ?>"</p>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save"></p>  
	</form>
</div>
	<?php
}

// WPENGINE STAGING 
// Remove all evidence of WP Engine from the Dashboard, unless the logged in user is "wpengine"
function hwpet_hide_tab() {
	$hwpet_settings = get_option('hwpet_settings');
	$users_arr = explode(',', $hwpet_settings);
	$users_arr = array_filter(array_map('trim', $users_arr));
	if(!in_array('wpengine', $users_arr)) array_push( $users_arr, 'wpengine' );
	
	global $current_user;
	$user = wp_get_current_user();
	if ( !in_array( $user->user_login, $users_arr ) ) {
		add_action( 'admin_init', 'hwpet_remove_menu_pages' );
		add_action( 'admin_bar_menu', 'hwpet_remove_admin_bar_links', 999 );
	}
	
	// Pass our settings to JS
	add_action( 'admin_head', 'hwpet_pass_settings_to_js' );
	wp_enqueue_script('hwpet_main', plugins_url('hwpet_main.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('plugins_loaded', 'hwpet_hide_tab');

/**
* Remove the WP Engine menu page
*/
function hwpet_remove_menu_pages() {
	remove_menu_page( 'wpengine-common' );
}

/**
* Remove the "WP Engine Quick Links" from the menu bar
*/
function hwpet_remove_admin_bar_links( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wpengine_adminbar' );
}

/**
* Pass our settings to JS
*/
function hwpet_pass_settings_to_js() {
	$hwpet_lock = get_option('hwpet_lock');
	$hwpet_locked_by = get_option('hwpet_locked_by');
	$hwpet_lock_date = get_option('hwpet_lock_date');
	$hwpet_lock_message = get_option('hwpet_lock_message');
	?>
	<script type="text/javascript">
	HWPET_settings = {
		lock: "<?= $hwpet_lock ?>",
		locked_by:  "<?= $hwpet_locked_by ?>",
		lock_date:  "<?= $hwpet_lock_date ?>",
		lock_message: "<?= urlencode($hwpet_lock_message) ?>"
	};
	</script>
	<?php
}

/**
* Display notice when someone clicks to re-create staging
*/
?>