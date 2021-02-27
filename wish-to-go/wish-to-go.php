<?php

/**
 * @package           WishToGo
 *
 * Plugin Name:       Wish To Go
 * Description:       The Travel Wish List
 * Version:           0.5.2
 * Author:            Wish To Go Travel
 * Author URI:        https://wish-to-go.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wish-to-go
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {                                                           // cSpell: disable-line
	die;
}

include 'settings-page.php';

/**
 * Currently plugin version.
 * Start at version 0.1<.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WISH_TO_GO_VERSION', '0.5.2' );

class WishToGo {
  private string $localeMeta;

  function __construct() {
    $this->localeMeta = "";
    $this->signUpRedirectMeta = "";
  }

  function enqueueAdminScripts() {
    //  wp_enqueue_style( 'a-style', plugins_url( '/backend/css/a.css', __FILE__) );
    //  wp_enqueue_script( 'a-js', plugins_url( '/backend/js/a.js', __FILE__) );
  }

	function enqueueFrontEndScripts() {
    wp_enqueue_style( 'wish-to-go-local-css', plugins_url( '/style.css', __FILE__ ) );
		wp_register_script( 'wish-to-go-js', 'https://cdn.wish-to-go.com/wish-to-go.main.js', null, null, true );
		wp_enqueue_script( 'wish-to-go-js' );
  }

  function register() {
    $pluginName = plugin_basename( __FILE__ );

    add_filter( "plugin_action_links_$pluginName", array( $this, 'settings_menu' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontEndScripts' ) );

    add_action('admin_footer-post-new.php', array( $this, 'addCustomFieldsMenuItems' ) );
    add_action('admin_footer-post.php', array( $this, 'addCustomFieldsMenuItems' ) );

    add_filter( 'the_title', array( $this, 'appendWishWidgetToTitle' ), 10, 2 );

    $options = get_option('wtg_settings', false );
    if ( !( $options && $options['wtg_setting_hide_wish_counter'] ) ) {
      add_action( 'wp_footer', array( $this, 'appendWishCounterToContent' ), 10, 2 );
    }

    if ( $options && $options['wtg_setting_locale'] ) {
      $this->localeMeta = $options['wtg_setting_locale'];
      add_action( 'wp_head', array( $this, 'appendLocaleMeta' ), 10, 2 );
    }
    
    if ( $options && $options['wtg_setting_sign_up_redirect'] ) {
      $this->signUpRedirectMeta = $options['wtg_setting_sign_up_redirect'];
      add_action( 'wp_head', array( $this, 'appendSignUpRedirectMeta' ), 10, 2 );
    }
    
    $this->registerShortCodes();
  }

  function registerShortCodes() {
    add_shortcode('wishwidget', array( $this, 'wishWidget' ) );
    add_shortcode('travelplanwidget', array( $this, 'travelPlanWidget' ) );
    add_shortcode('wishcounterwidget', array( $this, 'wishCounterWidget' ) );
    add_shortcode('sharetripwidget', array( $this, 'shareTripWidget' ) );
  }

  function settings_menu( $links ) {
    $pluginLinks = array(
      '<a href="'. admin_url( 'admin.php?page=wish-to-go' ).'">Settings</a>'
    );

    return array_merge( $pluginLinks, $links );
  }

  function wishWidget( $attributes = [], $content = null, $tag = '' ) {
    $att = $this->attributesToString( $attributes );

    return "<wishwidget $att></wishwidget>";
  }

  function travelPlanWidget( $attributes = [], $content = null, $tag = '' ) {
    $att = $this->attributesToString( $attributes );

    return "<travelplanwidget $att></travelplanwidget>";
  }

  function wishCounterWidget( $attributes = [], $content = null, $tag = '' ) {
    $att = $this->attributesToString( $attributes );

    return "<wishcounterwidget $att></wishcounterwidget>";
  }

  function shareTripWidget( $attributes = [], $content = null, $tag = '' ) {
    $att = $this->attributesToString( $attributes );

    return "<sharetripwidget $att></sharetripwidget>";
  }

  function attributesToString($attributes = []) {
    $att = '';
  
    if ( $attributes ) {
      foreach ( $attributes as $key => $value ) {
        $att = $att . $key . '="' . $value . '" ';
      }
    }

    return $att;
  }

  function appendLocaleMeta( $locale ) {
    echo "
      <meta name=\"wtg-locale\" content=\"$this->localeMeta\" />
    ";
  }

  function appendSignUpRedirectMeta( $locale ) {
    echo "
      <meta name=\"wtg-sign-up-redirect\" content=\"$this->signUpRedirectMeta\" />
    ";
  }

  function appendWishCounterToContent() {
    echo "
      <div class=\"wish-to-go wish-to-go-on-top\">
        <div class=\"stick-to-bottom\">  
          <wishcounterwidget></wishcounterwidget>
        </div>
      </div>
    ";
  }

  function appendWishWidgetToTitle( $title, $id ) {
    if ( is_admin() ) {
      return $title;
    }

    $post = get_post( $id );

    if ( ! ( in_the_loop() && $post instanceof WP_Post 
      && ( $post->post_type == 'post' 
        || $post->post_type == 'page'  //TODO: Make as option
      ) ) ) {
      return $title;
    }

    $country = get_post_meta( $id, 'country', true );
    $city = get_post_meta( $id, 'city', true );
    $activity = get_post_meta( $id, 'activity', true );
    $picture = get_the_post_thumbnail_url( $id, 'large' );
    $postURL = get_permalink( $id );

    if ( ! $country
      // || is_front_page() //TODO: Make as option
      ) {
      return $title;
    }

    $widget = "
      <wishwidget
        country=\"$country\"
        city=\"$city\"
        activity=\"$activity\"
        picture=\"$picture\"
        post=\"$postURL\"
      >
      </wishwidget>
    ";

    return $title . $widget;
  }

  function activate(){
    // Database::createDB();
    flush_rewrite_rules();
  }

  function deactivate(){
    flush_rewrite_rules();
  }

  /**
 * Programatically add custom fields.
 *
 * @see http://wordpress.stackexchange.com/questions/98269/programatically-add-options-to-add-new-custom-field-dropdown/
 */

  function addCustomFieldsMenuItems() {
    if (isset($GLOBALS['post'])) {
      $post_type = get_post_type($GLOBALS['post']);

      if (post_type_supports($post_type, 'custom-fields')) {
        ?>
          <script>
              // Cache:
              var $metakeyinput = jQuery('#metakeyinput'),
                  $metakeyselect = jQuery('#metakeyselect');
              // Does the default input field exist and is it visible?
              if ($metakeyinput.length && ( ! $metakeyinput.hasClass('hide-if-js'))) {
                  // Hide it:
                  $metakeyinput.addClass('hide-if-js'); // Using WP admin class.
                  // ... and create the select box:
                  $metakeyselect = jQuery('<select id="metakeyselect" name="metakeyselect">').appendTo('#newmetaleft');
                  // Add the default select value:
                  $metakeyselect.append('<option value="#NONE#">— Select —</option>');
              }
              // Does "country" already exist?
              if (jQuery("[value='country']").length < 1) {
                  // Add option:
                  $metakeyselect.append("<option value='country'>country</option>");
              }
              if (jQuery("[value='city']").length < 1) {
                  $metakeyselect.append("<option value='city'>city</option>");
              }
              if (jQuery("[value='activity']").length < 1) {
                  $metakeyselect.append("<option value='activity'>activity</option>");
              }
          </script>
        <?php
      }
    }
  }
}
  
class WishToGoWidget extends WP_Widget {
  public function __construct() {
    parent::__construct(
      'WishToGoWidget',
      __( 'Wish To Go Travel Planner', 'text_domain' ),
      array(
        'customize_selective_refresh' => true,
      )
    );
  }

  public function form( $instance ) {
  }   
  
  public function widget( $args, $instance ) {
    echo "
      <div style=\"margin-bottom: 2em;\">
        <travelplanwidget></travelplanwidget>
      </div>
    ";
  }
 
}


$wishToGo = new WishToGo();
$wishToGo->register();
register_activation_hook( __FILE__, array( $wishToGo, 'activate' ) );
register_deactivation_hook( __FILE__, array( $wishToGo, 'deactivate' ) );

function register_wtg_widget() {
  register_widget( 'WishToGoWidget' );
}

add_action( 'widgets_init', 'register_wtg_widget' );