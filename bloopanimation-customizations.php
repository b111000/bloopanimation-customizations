<?php
/*
 * Plugin Name: Bloopanimation - Customizations
 * Description: [When people purchase a certain Memberpress membership, the price is discounted by all previous purchases]
 * Author: William
 * Version: 1.0.0.4
 * Author URI: https://app.codeable.io/tasks/new?preferredContractor=77368
 * Text Domain: bloopanimation
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Classes
 */
include( plugin_dir_path( __FILE__ ) . 'classes/memberpress/checkout.php' );
include( plugin_dir_path( __FILE__ ) . 'classes/memberpress/meta-fields.php' );
include( plugin_dir_path( __FILE__ ) . 'classes/memberpress/shortcodes.php' );
include( plugin_dir_path( __FILE__ ) . 'classes/assets/assets.php' );
include( plugin_dir_path( __FILE__ ) . 'classes/groundhogg/groundhogg.php' );
include( plugin_dir_path( __FILE__ ) . 'classes/groundhogg/shortcodes.php' );

/**
 * Functions
 */
include( plugin_dir_path( __FILE__ ) . 'functions/functions.php' );