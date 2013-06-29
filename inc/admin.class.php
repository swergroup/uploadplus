<?php

/**
 * UploadPlus admin UI class
 * @see SWER_uploadplus_core
 * @package WordPress_Plugins
 * @subpackage UploadPlus
 */
class SWER_uploadplus_admin extends SWER_uploadplus_core {

	/**
	* options header text
	*/
	static function upp_options_intro() {
	$test_string = 'αι βασίλης και σύζηγος.jpeg';
	$demo_string = parent::upp_mangle_filename( $test_string );
	?>
		<div style="border:1px solid #eee; padding:.2em .5em; width:20%;" class="alignright">
		<p><a href="http://wordpress.org/plugins/uploadplus/">UploadPlus</a> <?php _e( 'is mantained and developed by', 'uploadplus' ); ?><a href="http://swergroup.com" target="_blank">SWERgroup</a>, <?php _e( 'with the help of the open source community.', 'uploadplus' ); ?></p>

		<ul>
		<li><a href="http://wordpress.org/support/plugin/uploadplus/"><?php _e( 'Support forum on wordpress.org', 'uploadplus' ); ?></a>
		<li><a href="http://github.com/swergroup/uploadplus/"><?php _e( 'Code repository on Github', 'uploadplus' ); ?></a>
		<li><a href="https://travis-ci.org/swergroup/uploadplus"><?php _e( 'Build status on Travis CI', 'uploadplus' ); ?></a>
		</ul>

		<p><?php _e( 'Found this plugin useful? Would like to thanks developers and support team? Need a new feature? Feel free to', 'uploadplus' ); ?> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=K9J3Y78FPD7KW"><?php _e( 'sponsor a coding session!', 'uploadplus' ); ?></a></p>
		</div>

		<p><?php _e( 'UploadPlus will clean and customize your uploads\' file and post name with your own style.', 'uploadplus' ); ?> <!-- You can preview how a file name will be cleaned using this tool: --><br>
		<ul>
		<li><strong><?php _e( 'Separator', 'uploadplus' ); ?></strong> &mdash; <?php _e( 'change whitespaces into dashes or underscores.', 'uploadplus' ); ?></li>
		<li><strong><?php _e( 'Case', 'uploadplus' ); ?></strong>  &mdash; <?php _e( 'lowercase, UPPERCASE or CamelCase.', 'uploadplus' ); ?></li>
		<li><strong><?php _e( 'Prefix', 'uploadplus' ); ?></strong> &mdash; <?php _e( 'date/time, blogname, random, custom...', 'uploadplus' ); ?></li>
		<li><strong><?php _e( 'Transliteration', 'uploadplus' ); ?></strong> &mdash; <?php _e( 'convert any non-latin script to latin characters. <a href="http://en.wikipedia.org/wiki/Transliteration">Learn More</a>', 'uploadplus' ); ?></li>
		<li><strong><?php _e( 'Random File Name', 'uploadplus' ); ?></strong>  &mdash; <?php _e( 'Every file is unique!', 'uploadplus' ); ?></li>
		</ul>
		</p>


		<p>
		<strong><?php _e( 'Test file name:', 'uploadplus' ); ?></strong> <code><?php _e( $test_string ); ?></code> &bull;
		<strong><?php _e( 'Output:', 'uploadplus' ); ?></strong> <code><?php _e( $demo_string ); ?></code>
		</p>
	<?php
	}

	/**
	* setting box: clean level (separator)
	*/
	static function upp_options_box_cleanlevel(){
	$actual = get_option( 'uploadplus_separator' );

	$styles = array(
		'space' => 'Space <code>&nbsp;</code>', 
		'dash' => 'Dashes <code>-</code>', 
		'underscore' => 'Underscores <code>_</code>', 
	);

	echo '<p>';
	foreach ( $styles as $key => $info ) : 
		if ( $actual[0] == $key ) $flag = 'checked="checked"'; else $flag = '';
		echo '
		<input type="radio" name="uploadplus_separator[]" id="uploadplus_style-'.$key.'" '.$flag.' value="'.$key.'"/>
		'.$info.'<br>
		';
	endforeach;
	echo '</p>';
	}

	/**
	* setting box: letter case
	*/
	static function upp_options_box_case(){
		$case  = get_option( 'uploadplus_case' );
		$cases = array(
			'0'	=> __( 'leave as is', 'uploadplus' ), 
			'1'	=> __( 'lowercase', 'uploadplus' ), 
			'2'	=> __( 'UPPERCASE', 'uploadplus' ),
			'3' => __( 'CamelCase', 'uploadplus' ),
		);
		foreach ( $cases as $ca => $se ):
			if ( $case[0] == $ca ):
				$flag = 'checked="checked"';
			else :
				$flag = '';
			endif;
			echo '<p><input type="radio" name="uploadplus_case[]" id="uploadplus_lettercase-'.$ca.'" value="'.$ca.'" '.$flag.'/>'.$se.'</p>';
		endforeach;
	}

	/**
	 * setting box: custom prefix
	 */
	static function upp_options_box_customprefix(){
	$prefix = get_option( 'uploadplus_customprefix' );
	$value  = ( $prefix !== '' ) ? $prefix : '';
	echo '<p> <input type="text" name="uploadplus_customprefix" id="uploadplus_customprefix" value="'.$value.'" /></p>';
	}


	/**
	 * setting box: standard prefix
	 */
	static function upp_options_box_prefix(){
		global $sep;
		#$clean  = get_option( 'uploadplus_separator' );
		$prefix = get_option( 'uploadplus_prefix' );

		$datebased = array(
			'1' => 'd (' . date( 'd' ) . $sep . ')',
			'2' => 'md (' . date( 'md' ) . $sep . ')',
			'3' => 'ymd (' . date( 'ymd' ) . $sep . ')"',
			'4' => 'Ymd (' . date( 'Ymd' ) . $sep . ')',
			'5' => 'YmdHi (' . date( 'YmdHi' ) . $sep . ')',
			'6' => 'YmdHis (' . date( 'YmdHis' ) . $sep . ')',
			'7' => 'unix timestamp (' . date( 'U' ) . $sep . ')',
		);
		$otherstyles = array(
			'8' => '['.__( 'Random', 'uploadplus' ).'] '.mt_rand().$sep,
			'9' => '['.__( 'Random 2x', 'uploadplus' ).'] '.md5( mt_rand() ).$sep,
			'10' => '['.__( 'Blog name', 'uploadplus' ).'] '.str_replace( array( '.', ' ', '-', '_' ) ,$sep, get_bloginfo( 'name' ) ).$sep,
			'A' => '['.__( 'Short blog name', 'uploadplus' ).'] '.str_replace( array( '.', '_', '-', ' ' ), '', get_bloginfo( 'name' ) ).$sep,
			'B' => __( 'WordPress unique filename', 'uploadplus' ),
		);

		$nullval = ( $prefix == '' ) ? 'selected="selected"' : '';
		echo '
		<select name="uploadplus_prefix" id="uploadplus_prefix">	
		<option value="0" label="'.__( 'No prefix / custom prefix', 'uploadplus' ).'" '.$nullval.'>No prefix or custom prefix</option>
		<optgroup label="'.__( 'Date/time based prefix', 'uploadplus' ).'">
		';
		$flag = $oflag = '';
		foreach ( $datebased as $key => $val ) :
			$flag = ( $prefix == $key && $nullval == '' ) ? 'selected="selected"' : '';
		echo '<option value="'.$key.'" label="'.$val.'" '.$flag.'>'.$val.'</option>
		';
		endforeach;
		echo'
		</optgroup>
		<optgroup label="'.__( 'Others', 'uploadplus' ).'">
		';
		foreach ( $otherstyles as $okey => $oval ) :
			$oflag = ( $prefix == $okey && $nullval == '' ) ? 'selected="selected"' : '';
		echo '<option value="'.$okey.'" label="'.$oval.'" '.$oflag.'>'.$oval.'</option>
		';
		endforeach;

		echo '
		</optgroup>	
		</select>
		';
	}

	/**
	* setting box: transliteration
	*/
	static function upp_options_box_utf8toascii(){
		$utf8ornot = get_option( 'uploadplus_utf8toascii' );
		$options   = array(
			'0'	=> __( 'Leave as is', 'uploadplus' ), 
			'1'	=> __( 'Transliterate into latin characters', 'uploadplus' ),
			);
		foreach ( $options as $uk => $uv ) :
			$flag = ( $utf8ornot[0] == $uk) ? 'checked="checked"' : '';
		echo '<input type="radio" name="uploadplus_utf8toascii[]" id="uploadplus_utf8toascii-'.$uk.'" value="'.$uk.'" '.$flag.'/>'.$uv.' <br>';
		endforeach;
	}    

	/**
	* setting box: random file name
	*/
	static function upp_options_box_random(){
		$random = get_option( 'uploadplus_random' );
		$check  = ( $random ) ? ' checked="checked" ' : '';
		echo '<input type="checkbox" name="uploadplus_random" id="uploadplus_random" '.$check.'> <br>';
	}

}
