<?php
/**
 * Implements example command.
 *
 * @package wp-cli
 */
class UploadPlus_Cmds extends WP_CLI_Command{

	function clean( $args, $assoc_args ) {
		list( $string ) = $args;
		$file_name = SWER_uploadplus_core::_clean_global( $string );
		$file_name = SWER_uploadplus_core::_clean_case( $file_name );
		#$file_name = SWER_uploadplus_core::_add_prefix( $file_name );
		WP_CLI::line( $file_name );
	}

}

WP_CLI::add_command( 'upplus', 'UploadPlus_Cmds' );
