<?php
/*
Plugin Name: Upload+
Plugin URI: http://wordpress.org/extend/plugins/uploadplus/
Description: Security and sanity in file names while uploading. Once activate, please <a href="options-media.php#uploadplus">check your settings</a>. 
Author: SWERgroup
Version: 3.0.1
Author URI: http://swergroup.com/

Copyright (C) 2007+ Paolo Tresso / SWERgroup (http://swergroup.com/)

Includes hints and code by:
	Francesco Terenzani (http://terenzani.it/)
	Jennifer Hodgdon (http://www.poplarware.com/)
	difreo (http://wordpress.org/support/topic/plugin-upload-file-name-suffix?replies=3)

Make use of UTF8 PHP classes by http://phputf8.sourceforge.net/

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once 'utf8/utf8.php';
require_once 'utf8/str_ireplace.php';
require_once  UTF8 . '/utils/validation.php';
require_once  UTF8 . '/utils/ascii.php';
require_once 'utf8_to_ascii/utf8_to_ascii.php';
require_once 'uploadplus.class.php';

// Only create an instance of the plugin if it doesn't already exists in GLOBALS
if( ! array_key_exists( 'swer-uploadplus', $GLOBALS ) ) { 

	class SWER_uploadplus {
		 
		 // Initializes the plugin (actions/filters)
		function __construct() {
            add_action('admin_init', array( &$this, 'settings_init' ) );
            add_action('wp_handle_upload', array( 'SWER_uploadplus_core', 'upp_rename' ) );           
#            print_r( get_option('uploadplus_utf8toascii') ); die();
		}
	  
	    function settings_init() {
        	add_settings_section('upp_options_section', 'Upload+ Plugin', array( 'SWER_uploadplus_admin', 'upp_options_intro'), 'media');

        	add_settings_field('uploadplus_cleanlevel', 'Cleaning options', array( 'SWER_uploadplus_admin', 'upp_options_box_cleanlevel'), 'media', 'upp_options_section');
        	add_settings_field('uploadplus_case', 'Case options', array( 'SWER_uploadplus_admin', 'upp_options_box_case'), 'media', 'upp_options_section');
        	add_settings_field('uploadplus_prefix', 'Prefix', array( 'SWER_uploadplus_admin', 'upp_options_box_prefix'), 'media', 'upp_options_section');
        	add_settings_field('uploadplus_customprefix', 'Custom Prefix', array( 'SWER_uploadplus_admin', 'upp_options_box_customprefix'), 'media', 'upp_options_section');
        	add_settings_field('uploadplus_utf8toascii', 'Transcription', array( 'SWER_uploadplus_admin', 'upp_options_box_utf8toascii'), 'media', 'upp_options_section');

        	register_setting('media', 'uploadplus_cleanlevel');
        	register_setting('media', 'uploadplus_case');
        	register_setting('media', 'uploadplus_prefix');
        	register_setting('media', 'uploadplus_customprefix');
        	register_setting('media', 'uploadplus_utf8toascii');
        }
      
	  
	  
	  
	    function activate(){
	        if( ! get_option('uploadplus_version') ):
            	update_option('uploadplus_version', '3.0.1');
            	add_option('uploadplus_cleanlevel','');
            	add_option('uploadplus_case','');
            	add_option('uploadplus_prefix','');
            	add_option('uploadplus_customprefix','');
            	add_option('uploadplus_utf8toascii','');
        	endif;
	    }

        function deactivate(){}

        function uninstall(){
        	delete_option( 'uploadplus_cleanlevel' );
        	delete_option( 'uploadplus_case' );
        	delete_option( 'uploadplus_prefix' );
        	delete_option( 'uploadplus_customprefix' );
        	delete_option( 'uploadplus_utf8toascii' );
            delete_option( 'uploadplus_version' );
        }

	
	}
	
	$GLOBALS['swer-uploadplus'] = new SWER_uploadplus();	
}

register_activation_hook(   __FILE__, array( 'SWER_uploadplus','activate' ) );
register_deactivation_hook( __FILE__, array( 'SWER_uploadplus','deactivate' ) );
register_uninstall_hook(    __FILE__, array( 'SWER_uploadplus','uninstall' ));

?>