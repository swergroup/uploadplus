<?php
/**
 * Implements example command.
 *
 * @package wp-cli
 */
class Test_Greeklish extends WP_CLI_Command {

	/**
	 * Greeklish test ( "Αισθάνομαι τυχερός" in "esthanome ticheros" )
	 * 
	 * @synopsis <string>
	 */
	function testgreeklish( $args, $assoc_args ) {
		list( $string ) = $args;

		$convert = SWER_uploadplus_core::sanitize_greeklish( $string );

		// Print a success message
		WP_CLI::success( "input: " . $string . " | output: " . $convert );
	}
}

WP_CLI::add_command( 'uploadplus', 'Test_Greeklish' );

?>