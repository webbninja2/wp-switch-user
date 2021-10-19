<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_switch_user
 * @subpackage Ace_switch_user/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ace_switch_user
 * @subpackage Ace_switch_user/admin
 * @author     acewebx <webbninja2@gmail.com>
 */
class Ace_switch_user_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ace_switch_user_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ace_switch_user_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ace_switch_user-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ace_switch_user_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ace_switch_user_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ace_switch_user-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function ace_user_row_switch_link( $actions, $user_object ) {

	    if ( current_user_can( 'administrator', $user_object->ID ) ) {
	    
		    $user = wp_get_current_user();
		    if($user && ( $user->ID !== $user_object->ID) ) {
		    	$bare_url = wp_login_url().'?action=switch_user&user_id='.$user_object->ID;
		    	$c_url = wp_nonce_url( $bare_url, 'switch_to_user_'.$user_object->ID );  // complete url
		        $actions['switch'] = '<a href="'.$c_url.'">Switch to</a>';
		    }

	    }
	    elseif( isset( $_SESSION['old_user'] ) ) {
	    	if ( $_SESSION['old_user'] === $user_object->ID ){
	    	
		    	$user = wp_get_current_user();
			    if($user && ( $user->ID !== $user_object->ID) ) {
			    	$bare_url = wp_login_url().'?action=switch_user_back&user_id='.$user_object->ID;
			    	$c_url = wp_nonce_url( $bare_url, 'switch_to_olduser_'.$user_object->ID );  // complete url
			        $actions['switch'] = '<a href="'.$c_url.'">Switch back to</a>';
			    }
			}
	    }

		return $actions;

	}

	//function hooked to init
	public function ace_switch_session(){
		if( !session_id() )
	 	{
	    	session_start();
	  	}
		if( isset($_REQUEST['action']) && isset($_REQUEST['user_id']) && !empty($_REQUEST['action']) && !empty($_REQUEST['user_id']) )
		{
			if( sanitize_text_field( $_REQUEST['action'] ) == 'switch_user' )
			{
				$action  = ( isset( $_REQUEST['action'] ) ) ? sanitize_text_field( $_REQUEST['action'] ): '';
				$user_id = ( isset( $_REQUEST['user_id'] ) ) ? sanitize_text_field( $_REQUEST['user_id'] ): '';

				// Check authentication:
				if ( ! current_user_can( 'administrator', $user_id ) ) {
					wp_die( esc_html__( 'Could not switch users.', 'switching-user' ) );
				}

				// Check intent:
				check_admin_referer( "switch_to_user_$user_id" );

				$user_exists = get_userdata( $user_id );
				if ( $user_exists === false) {
					return false;
				}else{

					$current_user   = wp_get_current_user();
					$old_user_id = $current_user->ID;
					$_SESSION['old_user'] = $old_user_id;
					
					wp_clear_auth_cookie();
				    wp_set_current_user ( $user_id );
				    wp_set_auth_cookie  ( $user_id );

				    if( current_user_can('administrator') ) {
								wp_safe_redirect( admin_url() );
				    			exit();
					} else {
								wp_safe_redirect( home_url() );
				    			exit();
					}	
				} 
			}
			elseif( sanitize_text_field( $_REQUEST['action'] ) == 'switch_user_back' )
			{

				$action  = ( isset( $_REQUEST['action'] ) ) ? sanitize_text_field( $_REQUEST['action'] ): '';
				$user_id = ( isset( $_REQUEST['user_id'] ) ) ? sanitize_text_field( $_REQUEST['user_id'] ): '';

				// Check authentication when switch back:
				$user = new WP_User( $user_id );
				if ( ! empty( $user->roles ) && is_array( $user->roles ) && !in_array( 'administrator', $user->roles ) ) {
				    wp_die( esc_html__( 'Could not switch users.', 'switching-user' ) );
				}

				// Check intent:
				check_admin_referer( "switch_to_olduser_$user_id" );

				$user_exists = get_userdata( $user_id );
				if ( $user_exists === false) {
					return false;
				}else{

					unset( $_SESSION['old_user'] ); 

					wp_clear_auth_cookie();
				    wp_set_current_user ( $user_id );
				    wp_set_auth_cookie ( $user_id );

				    if ( isset($_REQUEST['redirect_to']) )
				    {
				    	$url = urldecode( $_REQUEST['redirect_to'] );
				    	wp_safe_redirect( $url );
				    	exit();
				    } 
				    elseif( current_user_can('administrator') ) 
				    {
				    	$url = admin_url().'?user_switched=success';
						wp_safe_redirect( $url );
				    	exit();
					} 
					else {
						wp_safe_redirect( home_url() );
				    	exit();
					}
				} 
			}
		}
	}

	 //Adds a 'Switch back to user' link to the Admin Bar Menu. 
	public function ace_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {

		if ( ! is_admin_bar_showing() ) {
			return;
		}


		if ( is_user_logged_in() && isset( $_SESSION['old_user'] ) ) {

			$old_user_id = $_SESSION['old_user'];
		    $old_user = get_userdata( $old_user_id );

			$current_url = urlencode( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		    $bare_url = wp_login_url().'?action=switch_user_back&user_id='.$old_user_id.'&redirect_to='.$current_url;
		    $c_url = wp_nonce_url( $bare_url, 'switch_to_olduser_'.$old_user_id ); 
			$wp_admin_bar->add_menu( array(
				'parent' => false,
				'id'     => 'switch-user-back',
				'title'  => esc_html( sprintf(
					 // Print: %1: user display name; %2: username; 
					__( 'Switch back to %1$s (%2$s)', 'switching-user' ),
					$old_user->user_login,
					$old_user->display_name
				) ),
				'href'   => esc_url( $c_url ),
			) );

		}		

	}

	 //Adds a 'Switch back to user' link to the Meta sidebar widget. 
	public function ace_wp_meta(){

		if ( is_user_logged_in() && isset( $_SESSION['old_user'] ) ) {

			$old_user_id = $_SESSION['old_user'];
	        $old_user = get_userdata( $old_user_id );

			$current_url = urlencode( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	        $bare_url = wp_login_url().'?action=switch_user_back&user_id='.$old_user_id.'&redirect_to='.$current_url;
	        $c_url = wp_nonce_url( $bare_url, 'switch_to_olduser_'.$old_user_id ); 
			$link = sprintf(
							 // Print: %1: user display name; %2: username; 
							__( 'Switch back to %1$s (%2$s)', 'switching-user' ),
							$old_user->user_login,
							$old_user->display_name
						);
			echo '<p id="switching_user_back"><a href="' . esc_url( $c_url ) . '">' . esc_html( $link ) . '</a></p>';

		}

	}

	//Adds a 'Switch back to user' link to the footer if meta sidebar widget and admin bar is not active.
	public function ace_switch_footer(){
		
		if ( is_admin_bar_showing() || did_action( 'wp_meta' ) ) {
			return;
		}

		if ( is_user_logged_in() && isset( $_SESSION['old_user'] ) ) {
			$old_user_id = $_SESSION['old_user'];
	        $old_user = get_userdata( $old_user_id );

			$current_url = urlencode( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	        $bare_url = wp_login_url().'?action=switch_user_back&user_id='.$old_user_id.'&redirect_to='.$current_url;
	        $c_url = wp_nonce_url( $bare_url, 'switch_to_olduser_'.$old_user_id ); 
			$link = sprintf(
							 // Display: 1-user display name; 2-username; 
							__( 'Switch back to %1$s (%2$s)', 'switching-user' ),
							$old_user->user_login,
							$old_user->display_name
						);
			echo '<p id="switching_user_back"><a href="' . esc_url( $c_url ) . '">' . esc_html( $link ) . '</a></p>';
		}
	}
	
 // Displays the 'Switched to user' and 'Switch back to user' notices in the admin section.
	public function ace_switch_usernotic(){
			if ( is_user_logged_in() && isset( $_SESSION['old_user'] ) ) {
			
			$old_user_id = $_SESSION['old_user'];
	        $old_user = get_userdata( $old_user_id );
	        $bare_url = wp_login_url().'?action=switch_user_back&user_id='.$old_user_id;
	        $c_url = wp_nonce_url( $bare_url, 'switch_to_olduser_'.$old_user_id ); //nonce for secure url token
			$link = sprintf(
							 // Display: 1-user display name; 2-username; 
							__( 'Switch back to %1$s (%2$s)', 'switching-user' ),
							$old_user->user_login,
							$old_user->display_name
						);
			?>
				<div id="switching_user" class="updated notice is-dismissible">
				<p><span class="dashicons dashicons-admin-users" style="color:#46b450" aria-hidden="true"></span>
			<?php
			echo '<a href="' . esc_url( $c_url ) . '">' . esc_html( $link ) . '</a></p></div>';

		}
		elseif( isset($_GET['user_switched']) && $_GET['user_switched'] == 'success' ){
			
			echo '<div id="switching_user" class="updated notice is-dismissible">';
			echo		'<p>';
							echo esc_html( sprintf(
								 // Display: 1-user display name, 2-username; 
								__( 'User Successfully Switched back.', 'switching-user' ) ) );
						
					
			echo		'</p>';
			echo '</div>';
			
		}

	}

	// if user after switching/before switching logs out destroys session variable containing user id.
	Public function ace_destroy_sessions(){
		if ( !is_user_logged_in() || isset( $_SESSION['old_user'] ) ) {
   		unset( $_SESSION['old_user'] ); //Clears session variable old user if user logs out 
		}
	}

}
