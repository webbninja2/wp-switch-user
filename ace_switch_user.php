<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://acewebx.com
 * @since             1.0.0
 * @package           Ace_switch_user
 *
 * @wordpress-plugin
 * Plugin Name:       Wp Switch User
 * Plugin URI:        acewebx.com
 * Description:       Wp switch user allows users( administrators ) to visit user's account without user's username and password.
 * Version:           1.0.0
 * Author:            acewebx
 * Author URI:        https://acewebx.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ace_switch_user
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ace_switch_user-activator.php
 */
function activate_ace_switch_user() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ace_switch_user-activator.php';
	Ace_switch_user_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ace_switch_user-deactivator.php
 */
function deactivate_ace_switch_user() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ace_switch_user-deactivator.php';
	Ace_switch_user_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ace_switch_user' );
register_deactivation_hook( __FILE__, 'deactivate_ace_switch_user' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ace_switch_user.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ace_switch_user() {

	$plugin = new Ace_switch_user();
	$plugin->run();

}
run_ace_switch_user();
