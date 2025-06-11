<?php
/**
 * Plugin Name: WPE Syndicate Child
 * Description: Connects to a Syndicate Master site and receives syndicated content.
 * Version: 0.0.1
 * Author: Alan Blair
 */

require_once __DIR__ . '/vendor/autoload.php';

use WPE_SyndicateChild\Plugin;

Plugin::init();
