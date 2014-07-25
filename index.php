<?php
/*
Plugin Name: Sticky Post
Plugin URI: http://zourbuth.com/plugins/sticky-post
Description: A plugin for displaying sticky posts based on custom taxonomy, order, and much more.
Version: 1.0
Author: zourbuth
Author URI: http://zourbuth.com
License: GPL2
*/


/*  Copyright 2011  zourbuth.com  (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Launch the plugin. */
add_action( 'plugins_loaded', 'sticky_post_plugin_loaded' );

/* Initializes the plugin and it's features. */
function sticky_post_plugin_loaded() {

	// Set constant for this plugin
	define( 'STICKY_POST_VERSION', '1.0' );
	define( 'STICKY_POST_DIR', plugin_dir_path( __FILE__ ) );
	define( 'STICKY_POST_URL', plugin_dir_url( __FILE__ ) );
	
	require_once( STICKY_POST_DIR . 'sticky-post.php' );
	
	// Loads and registers the widgets
	add_action( 'widgets_init', 'sticky_post_load_widgets' );	
}

function sticky_post_load_widgets($atts) {
	// Load widget and register the countdown widget
	require_once( STICKY_POST_DIR . 'sticky-post-instance.php' );
	register_widget( 'Sticky_Post_Widget' );
}
?>