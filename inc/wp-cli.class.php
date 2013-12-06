<?php
/**
 * UploadPlus plugin command-line utilities
 *
 * @package wp-cli
 */
class UploadPlus_Cmds extends WP_CLI_Command{

	/**
	 * Transliterate string from UTF8 characters to ASCII
	 *
	 * ## OPTIONS
	 *
	 * <string>
	 * : String to convert.
	 *
	 * @alias convert
	 * @synopsis <string>
	 */
	function transliterate( $args ) {
		list( $string ) = $args;
		$file_name = SWER_uploadplus_core::_utf8_transliteration( $string );
		WP_CLI::line( $file_name );
	}

	/**
	 * Apply transformations to string, according to settings
	 *
	 * ## OPTIONS
	 *
	 * <string>
	 * : String to convert.
	 *
	 * @synopsis <string>
	 */
	function clean( $args ) {
		list( $string ) = $args;
		$file_name = SWER_uploadplus_core::upp_mangle_filename( $string );
		WP_CLI::line( $file_name );
	}

	/**
	 * Show UploadPlus settings
	 */
	function settings(){
		$case = get_option( 'uploadplus_case', true );
		WP_CLI::line( 'Case: ' . $case[0] );

		$sep = get_option( 'uploadplus_separator', true );
		WP_CLI::line( 'Separator: ' . $sep[0] );

		$prefix = get_option( 'uploadplus_prefix', true );
		WP_CLI::line( 'Prefix: ' . $prefix );

		$custom = get_option( 'uploadplus_customprefix', true );
		WP_CLI::line( 'Custom Prefix: ' . $custom );

		$utf8 = get_option( 'uploadplus_utf8toascii', true );
		WP_CLI::line( 'UTF8: ' . $utf8[0] );

		$random = get_option( 'uploadplus_random', true );
		WP_CLI::line( 'Random: ' . $random );
	}
	
}

WP_CLI::add_command( 'uploadplus', 'UploadPlus_Cmds' );
