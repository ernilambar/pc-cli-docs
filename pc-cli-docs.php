<?php
/**
 * Plugin Name: PC CLI Docs
 * Description: Commands to generate docs for PCP.
 * Requires at least: 6.3
 * Requires PHP: 7.0
 * Version: 0.1.0
 * Author: Nilambar Sharma
 * Author URI: https://www.nilambar.net/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: pc-cli-docs
 *
 * @package pc-cli-docs
 */

// Includes.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'class-pcp-command.php';
require_once plugin_dir_path( __FILE__ ) . 'command.php';
