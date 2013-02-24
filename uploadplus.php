<?php
/*
Plugin Name: UploadPlus : File Name Cleaner
Plugin URI: http://wordpress.org/extend/plugins/uploadplus/
Description: Clean file names and enhance security while uploading. 
Author: SWERgroup
Version: 3.1.3
Author URI: http://swergroup.com/

Copyright (C) 2007+ Paolo Tresso / SWERgroup (http://swergroup.com/)

Includes code libraries:
*   Arabic PHP (LGPL) - http://www.ar-php.org/
*   URLify (Django PHP port) - https://github.com/jbroadway/urlify/

Hints and code:
* Francesco Terenzani (http://terenzani.it/)
* Jennifer Hodgdon (http://www.poplarware.com/)
* difreo (http://wordpress.org/support/topic/plugin-upload-file-name-suffix?replies=3)

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

define( 'UPLOADPLUS_VERSION', '3.1.3' );

require_once 'lib/URLify.php';
require_once 'lib/Arabic.php';
require_once 'inc/core.class.php';
require_once 'inc/admin.class.php';

if( ! array_key_exists( 'swer-uploadplus', $GLOBALS ) ) { 

    class SWER_uploadplus {

        // Initializes the plugin (actions/filters)
        function __construct() {
            #$core = new SWER_uploadplus_core();
            add_action( 'admin_init', array( &$this, '_admin_init' ) );
            add_action( 'wp_handle_upload', array( 'SWER_uploadplus_core', 'wp_handle_upload' ) );
            add_action( 'wp_handle_upload_prefilter', array( 'SWER_uploadplus_core', 'wp_handle_upload_prefilter' ), 1, 1);
            #add_filter( 'wp_read_image_metadata' , array( 'SWER_uploadplus_core','wp_read_image_metadata'), 1, 3);
            #add_filter( 'sanitize_file_name', array( 'SWER_uploadplus_core', 'sanitize_file_name' ) );
            add_action( 'add_attachment', array( 'SWER_uploadplus_core', 'add_attachment' ) );
        }

        function _admin_init() {
            add_settings_section( 'upp_options_section', 'UploadPlus plugin (file name cleaner)', array('SWER_uploadplus_admin', 'upp_options_intro'), 'media');

            add_settings_field( 'uploadplus_separator', 'Separator', 
                array( 'SWER_uploadplus_admin', 'upp_options_box_cleanlevel'), 'media', 'upp_options_section');
            register_setting('media', 'uploadplus_separator');
            
            add_settings_field('uploadplus_case', 'Case', 
                array( 'SWER_uploadplus_admin', 'upp_options_box_case'), 'media', 'upp_options_section');
            register_setting('media', 'uploadplus_case');

            add_settings_field('uploadplus_prefix', 'Prefix', 
                array( 'SWER_uploadplus_admin', 'upp_options_box_prefix'), 'media', 'upp_options_section');
            register_setting('media', 'uploadplus_prefix');

            add_settings_field('uploadplus_customprefix', 'Custom Prefix', 
                array( 'SWER_uploadplus_admin', 'upp_options_box_customprefix'), 'media', 'upp_options_section');
            register_setting('media', 'uploadplus_customprefix');

            add_settings_field('uploadplus_utf8toascii', 'Transliteration', 
                array( 'SWER_uploadplus_admin', 'upp_options_box_utf8toascii'), 'media', 'upp_options_section');
            register_setting('media', 'uploadplus_utf8toascii');
            
            }

        function activate(){
            if( ! get_option('uploadplus_version') )
                update_option( 'uploadplus_version', UPLOADPLUS_VERSION );
            if( ! get_option('uploadplus_separator') )                
                update_option( 'uploadplus_separator' ,'dash' );
            if( ! get_option('uploadplus_case') )
                update_option( 'uploadplus_case', '0' );
            if( ! get_option('uploadplus_prefix') )
                update_option( 'uploadplus_prefix', '0' );
            if( ! get_option('uploadplus_customprefix') )
                update_option( 'uploadplus_customprefix', '' );
            if( ! get_option('uploadplus_utf8toascii') )
                update_option( 'uploadplus_utf8toascii', '0' );
            endif;
        }

        function deactivate(){ 
            // do nothing (yet).
        }

        function uninstall(){
            delete_option( 'uploadplus_separator' );
            delete_option( 'uploadplus_case' );
            delete_option( 'uploadplus_prefix' );
            delete_option( 'uploadplus_customprefix' );
            delete_option( 'uploadplus_utf8toascii' );
            delete_option( 'uploadplus_version' );
        }
        
    }   // end class

    $GLOBALS['swer-uploadplus'] = new SWER_uploadplus();
}

register_activation_hook(   __FILE__, array( 'SWER_uploadplus','activate' ) );
register_deactivation_hook( __FILE__, array( 'SWER_uploadplus','deactivate' ) );
register_uninstall_hook(    __FILE__, array( 'SWER_uploadplus','uninstall' ));
?>