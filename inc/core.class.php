<?php

/**
 * UploadPlus core class
 * @package WordPress_Plugins
 * @subpackage UploadPlus
 */
class SWER_uploadplus_core {

	/**
	 * default separator
	 */
	var $sep = '-';

	var $exif_mime = array( 'image/jpeg', 'image/tiff' );

	/**
	* transliterate greek script into english characters
	* @link http://www.freestuff.gr/forums/viewtopic.php?p=194579#194579
	* @param $text    (maybe-)greek string to transliterate
	* @return string  transliterated string 
	*/
	static function sanitize_greeklish( $text ) {
		$expressions = array(
			'/[αΑ][ιίΙΊ]/u' => 'e',
			'/[οΟΕε][ιίΙΊ]/u' => 'i',
			'/[αΑ][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'af$1',
			'/[αΑ][υύΥΎ]/u' => 'av',
			'/[εΕ][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'ef$1',
			'/[εΕ][υύΥΎ]/u' => 'ev',
			'/[οΟ][υύΥΎ]/u' => 'ou',

			'/(^|\s)[μΜ][πΠ]/u' => '$1b',
			'/[μΜ][πΠ](\s|$)/u' => 'b$1',
			'/[μΜ][πΠ]/u' => 'mp',
			'/[νΝ][τΤ]/u' => 'nt',
			'/[τΤ][σΣ]/u' => 'ts',
			'/[τΤ][ζΖ]/u' => 'tz',
			'/[γΓ][γΓ]/u' => 'ng',
			'/[γΓ][κΚ]/u' => 'gk',
			'/[ηΗ][υΥ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'if$1',
			'/[ηΗ][υΥ]/u' => 'iu',

			'/[θΘ]/u' => 'th',
			'/[χΧ]/u' => 'ch',
			'/[ψΨ]/u' => 'ps',
	
			'/[αάΑΆ]/u' => 'a',
			'/[βΒ]/u' => 'v',
			'/[γΓ]/u' => 'g',
			'/[δΔ]/u' => 'd',
			'/[εέΕΈ]/u' => 'e',
			'/[ζΖ]/u' => 'z',
			'/[ηήΗΉ]/u' => 'i',
			'/[ιίϊΙΊΪ]/u' => 'i',
			'/[κΚ]/u' => 'k',
			'/[λΛ]/u' => 'l',
			'/[μΜ]/u' => 'm',
			'/[νΝ]/u' => 'n',
			'/[ξΞ]/u' => 'x',
			'/[οόΟΌ]/u' => 'o',
			'/[πΠ]/u' => 'p',
			'/[ρΡ]/u' => 'r',
			'/[σςΣ]/u' => 's',
			'/[τΤ]/u' => 't',
			'/[υύϋΥΎΫ]/u' => 'i',
			'/[φΦ]/iu' => 'f',
			'/[ωώ]/iu' => 'o',
		);
		$text = preg_replace( array_keys( $expressions ), array_values( $expressions ), $text );
		return $text;
	}

	/**
	* wrapper around (future) multiple transliteration logics
	* @param string $file_name  filename to transliterate
	* @return string  transliterated filename 
	*/
	static function _utf8_transliteration( $file_name ){
		if ( ! class_exists( 'I18N_Arabic_Transliteration' ) ) 
			$arabic = new I18N_Arabic( 'Transliteration' );

		$file_name = trim( I18N_Arabic_Transliteration::ar2en( $file_name ) );
		$file_name = self::sanitize_greeklish( $file_name );
		$file_name = URLify::downcode( $file_name ); 
		return $file_name;
	}

	/**
	* convert string between cases, according to options
	* @param string   $file_name file name to convert
	* @return string  converted filename 
	*/
	static function _clean_case( $file_name ){
		$case = get_option( 'uploadplus_case' );
		switch ( $case[0] ):
		case '1' :
			$file_name = strtolower( trim( $file_name ) );
			break;
		case '2' :
			$file_name = strtoupper( trim( $file_name ) );
			break;
		case '3' :
			$file_name = ucwords( trim( $file_name ) );
			break;
		default:
			$file_name = trim( $file_name );
			break;
		endswitch;
		return $file_name;
	}

	/**
	* (deprecated) replace whitespaces with dashes or underscores
	* @deprecated
	* @param string   $file_name file name to convert
	* @return string  converted filename 
	*/
	static function _clean_global( $file_name ){
		global $sep;
		$cleanlevel = get_option( 'uploadplus_separator' );
		$level = isset( $cleanlevel[0] ) ? $cleanlevel[0] : 'dash';

		switch ( $level ):
		case 'dash' :
		default:
			$file_name = preg_replace( '/[-\s]+/', '-', $file_name );
			$sep = '-';
			break;
		case 'space' :
			$file_name = preg_replace( '/[-\s]+/', ' ', $file_name );
			$sep = ' ';
			break;
		case 'underscore':
			$file_name = preg_replace( '/[-\s]+/', '_', $file_name );
			$sep = '_';
			break;
		endswitch;
		return $file_name;
	}

	/**
	* add a standard or custom prefix to a filename
	* @param string $file_name  filename to add prefix to 
	* @param string $options    if not null, force a standard prefix (1-10 || A-B)
	* @param string $custom     if not null, force a custom prefix
	* @return string 
	*/
	static function _add_prefix( $file_name, $options = '', $custom = '' ){
		global $current_user;
		$option_sep = get_option( 'uploadplus_separator', true );
		switch ( $option_sep ):
		case 'dash' :
		default:
			$sep = '-';
			break;
		case 'space' :
			$sep = ' ';
			break;
		case 'underscore':
			$sep = '_';
			break;
		endswitch;
		
		$options = ( $options == '' ) ? get_option( 'uploadplus_prefix' ) : $options;
		$custom  = ( $custom == '' ) ? get_option( 'uploadplus_customprefix' ) : $custom;

		switch ( $options ):
		case '1':	
			$file_name = date( 'd' ) . $sep . $file_name;
			break;
		case '2':
			$file_name = date( 'md' ) . $sep . $file_name;
			break;
		case '3':
			$file_name = date( 'ymd' ) . $sep . $file_name;
			break;
		case '4':
		case '5':
		case '6':
			$file_name = date( 'Ymd' ) . $sep . $file_name;
			break;
		case '7':
			$file_name = date( 'U' ) . $sep . $file_name;
			break;
		case '8':
			$file_name = mt_rand() . $sep . $file_name;
			break;
		case '9':
			$file_name = md5( mt_rand() ) . $sep . $file_name;
			break;
		case '10':
		case 'A':
			$file_name = sanitize_file_name( get_bloginfo( 'name' ) ) . $sep . $file_name;
			break;
		case 'B':
			$uploads   = wp_upload_dir();
			$dir = ( $uploads['path'] );
			$filename  = wp_unique_filename( $dir, $file_name, null );
			$file_name = $filename;
			break;
		case 'C':
			get_currentuserinfo();
			$file_name = $current_user->user_login . $sep . $file_name;
			break;
		default: 
			$file_name = $file_name; 
			break;
		endswitch;

		if ( $custom !== '' ):
			$return_file_name = $custom . $file_name;
		else :
			$return_file_name = $file_name;
		endif;
		
		// allow custom filters on the original filename
		$return_file_name = apply_filters( 'uploadplus_prefix', $return_file_name, $file_name );
		
		return $return_file_name;
	}

	static function _fix_separators( $file_name ){
		$option_sep = get_option( 'uploadplus_separator', true );
		switch ( $option_sep ):
		case 'dash' :
		default:
			$sep = '-';
			break;
		case 'space' :
			$sep = ' ';
			break;
		case 'underscore':
			$sep = '_';
			break;
		endswitch;

		return str_replace( array( '-', '_', ' ', '&nbsp;' ), $sep, $file_name );
	}

	static function _fix_dots( $file_name ){
		$replace = array( '-.', '_.', ' .' );
		return str_replace( $replace, '.', $file_name );
	}

	/**
	* (dev) hash filename and add it to metadata
	* @param string  $file_name file name to hash
	* @return string  filename hash 
	*/
	function _hash_filename( $file_name ) {
		$info = pathinfo( $file_name );
		$ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];
		$name = basename( $file_name, $ext );
		// add to post_meta
		return md5( $name ) . $ext;
	}
	# add_filter('sanitize_file_name', 'make_filename_hash', 10);

	/**
	* master function: applies every plugin step
	* @param string  $file_name file name to clean
	* @return string  clean filename 
	*/
	static function upp_mangle_filename( $file_name ){	
		# $ext  = self::find_extension( $file_name );
		$check = wp_check_filetype( $file_name );
		$ext   = $check['ext'];
		$utf8  = get_option( 'uploadplus_utf8toascii' );
		if ( $utf8[0] == '1' ):
			$file_name = self::_utf8_transliteration( $file_name );
		endif;

		$random = get_option( 'uploadplus_random' );
		if ( 'on' === $random ):
			$file_name = substr( sha1( time() ), 0, 20 ) . '.' . $ext;
		else :
			$file_name = self::_add_prefix( $file_name );
			$file_name = sanitize_file_name( $file_name );
			$file_name = self::_clean_case( $file_name );
			#$file_name = self::_clean_global( $file_name );

			$file_name = self::_fix_separators( $file_name );
			$file_name = self::_fix_dots( $file_name );
		endif;
		return $file_name;
	}

	/**
	* Apply cleaning methods
	* 
	* 
	* @param array $meta upload meta
	* @return array
	*/
	function wp_handle_upload_prefilter( $meta ){
		
		// clean filename
		$meta['name'] = self::upp_mangle_filename( $meta['name'] );		

		// If the file has EXIF data, proceed.
		if ( in_array( $meta['type'], $this->exif_mime ) && function_exists( 'exif_read_data' ) ):
			$exif_data = exif_read_data( $meta['tmp_name'], 0, true );
			$meta = self::_exif_datetime( $meta, $exif_data );	

			// http://blog.sucuri.net/?p=7654
			if ( isset( $exif_data['IFD0']['Model'] ) ):
				preg_match( '/\b(base64_decode|eval)\b/i', $exif_data['IFD0']['Model'], $matches );
				if (
					isset( $exif_data['IFD0']['Make'] ) &&
					'/.*/e' === $exif_data['IFD0']['Make'] &&
					0 < count( $matches ) 
					):
						// Set error to 'reject from extension'. 
						// Looks like the appropriate level here.
						$meta['error'] = 8;
						error_log( 'EXIF malware detected! Check http://blog.sucuri.net/?p=7654 for details. ' . "\n" . json_encode( array( $meta, $exif_data ) ) );
						if ( isset( $_POST['html-upload'] ) )
							wp_die( 'EXIF malware detected! Check http://blog.sucuri.net/?p=7654 for details.', 'EXIF malware detected' );
				endif;
			endif;

		endif;
		
		// let filters here
		$meta = apply_filters( 'uploadplus_upload_prefilter', $meta );

		return $meta;
	}

	function _exif_datetime( $meta, $exif_data ){
		if ( isset( $exif_data['FILE']['FileDateTime'] ) ):
			$meta['created_timestamp'] = $exif_data['FILE']['FileDateTime'];
		endif;
		return $meta;
	}

	/**
	* read image metadata
	* @param array $meta  file meta
	* @param string $file filename
	* @param string $sourceImageType
	* @return array
	*/
	function wp_read_image_metadata( $meta, $file, $sourceImageType ){
		$filename = basename( $file );
		#$meta['caption'] = $filename;
		$meta['title'] = $filename;

		$exif_data = array(
			'datetime_digitized' => 'DateTimeDigitized',
			'datetime_original' => 'DateTimeOriginal',
			'datetime_file' => 'FileDateTime',
			# 'latitude' => 'GPSLatitude',
			# 'latitude_ref' => 'GPSLatitudeRef',
			# 'longitude' => 'GPSLongitude',
			# 'longitude_ref' => 'GPSLongitudeRef',
		);
		
		$image_types = apply_filters(
			'wp_read_image_metadata_types',
			array(
				IMAGETYPE_JPEG,
				IMAGETYPE_TIFF_II,
				IMAGETYPE_TIFF_MM,
				)
			);
		
		if (
			is_callable( 'exif_read_data' ) && 
			in_array( $sourceImageType, $image_types )
		) {
			$exif = @exif_read_data( $file );	
			foreach ( $exif_data as $key => $value ){
				if ( ! empty( $exif[ $value ] ) )
					$meta[ $key ] = $exif[ $value ];
				}
				
			if ( isset( $exif['FileDateTime'] ) ):
				$meta['created_timestamp'] = $exif['FileDateTime'];
			elseif ( isset( $exif['DateTimeOriginal'] ) ):
				$meta['created_timestamp'] = $exif['DateTimeOriginal'];
			elseif ( isset( $exif['DateTimeDigitized'] ) ):
				$meta['created_timestamp'] = $exif['DateTimeDigitized'];
			endif;
		}
		
		$meta = apply_filters( 'uploadplus_image_metadata', $meta );
		error_log( json_encode( $meta ) );
		
		return $meta;
	}

	function add_attachment( $post_ID ){
		if ( false == get_post( $post_ID ) )
			return false;

		$obj   = get_post( $post_ID );
		$title = $obj->post_title;
		$title = apply_filters( 'uploadplus_attachment_title', $title );

		// Update the post into the database
		$uploaded_post = array();
		$uploaded_post['ID'] = $post_ID;
		$uploaded_post['post_title'] = $title;
		wp_update_post( $uploaded_post );

		# update_post_meta( $post_ID, 'filehash', $this->_hash_filename( $title ) );

		return $post_ID;
	}

}