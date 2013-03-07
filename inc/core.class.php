<?php
#if( ! array_key_exists( 'swer-uploadplus-core', $GLOBALS ) ) { 

    class SWER_uploadplus_core{
        
        var $sep = "-";

        // Based on http://www.freestuff.gr/forums/viewtopic.php?p=194579#194579
        function sanitize_greeklish($text) {
            if ( !defined('WP_CLI') && !WP_CLI && !is_admin() ) return $text;
    
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
                '/[ωώ]/iu' => 'o'
            );
    
            $text = preg_replace( array_keys($expressions), array_values($expressions), $text );
            return $text;
        }

                    
        /* find extension */
        function find_extension ($filename) { 
        	$exts = split("[/\\.]", $filename) ; 
        	$n = count($exts)-1; 
        	$exts = $exts[$n]; 
        	return $exts; 
        } 

        /* find full filename */
        function find_filename ($filename) { 
        	$explode = explode("/",$filename);
        	$explode = array_reverse($explode);
        	return $explode[0];
        } 
        
        function _utf8_transliteration( $file_name ){
            #$Ar = new I18N_Arabic('Transliteration');
            #$file_name = trim( I18N_Arabic_Transliteration::ar2en( $file_name ) );
            $file_name = self::sanitize_greeklish($file_name);
    	    $file_name = URLify::downcode($file_name); 
        	return $file_name;
        }
        
        function _clean_filename( $ext, $file_name ){
        	$file_name = str_replace(".".$ext,"",$file_name);
        	$file_name = str_replace(".","",$file_name);
            $file_name = preg_replace('~[^\\pL0-9_]+~u', '-', $file_name);
    		$file_name = preg_replace ('/^\s+|\s+$/', '', $file_name);            
        	$file_name = $file_name.".".$ext;
        	return $file_name;
        } 
        
        function _clean_case( $file_name ){
            $case = get_option('uploadplus_case');
        	switch( $case[0] ):
        		case "1":
        			$file_name = strtolower($file_name);
        			break;
        		case "2":
        			$file_name = strtoupper($file_name);
        			break;
        		case "3":
        			$file_name = ucwords($file_name);
        			break;
        		default:
        		    $file_name = trim($file_name);
        		    break;
        	endswitch;
        	return $file_name;
        }

        function _clean_global( $file_name ){
            global $sep;
            $cleanlevel = get_option('uploadplus_separator');
        	switch( $cleanlevel[0] ):
        	case "dash":
        	default:
        		$file_name = preg_replace ('/[-\s]+/', '-', $file_name);
        		$sep = "-";
        		break;
        	case "space":	
        		$file_name = preg_replace ('/[-\s]+/', ' ', $file_name);
        		$sep = "-";
        		break;
        	case "underscore":
        		$file_name = preg_replace ('/[-\s]+/', '_', $file_name);
        		$sep = "_";
        		break;
        	endswitch;
        	return $file_name;
        }

        function _add_prefix( $file_name ){
            global $sep;
    		switch( get_option('uploadplus_prefix') ):
    			case "1":		$file_name = date('d').$sep.$file_name;			break;
    			case "2":		$file_name = date('md').$sep.$file_name;		break;
    			case "3":		$file_name = date('ymd').$sep.$file_name;		break;
    			case "4":		$file_name = date('Ymd').$sep.$file_name;		break;
    			case "5":		$file_name = date('YmdHi').$sep.$file_name;		break;
    			case "6":		$file_name = date('YmdHis').$sep.$file_name;	break;
    			case "7":		$file_name = date('U').$sep.$file_name;			break;
    			case "8":		$file_name = mt_rand().$sep.$file_name;			break;
    			case "9":		$file_name = md5(mt_rand()).$sep.$file_name;	break;
    			case "10":		$file_name = str_replace( array(".","_","-"," ") ,$sep,  get_bloginfo('name') ).$sep.$file_name; break;
    			case "A":		$file_name = str_replace( array(".","_","-"," ") ,"", get_bloginfo('name') ).$sep.$file_name;	break;
                case "B":
                    $uploads = wp_upload_dir();
                    $dir = ( $uploads['path'] );
                    $filename = wp_unique_filename( $dir, $file_name, $unique_filename_callback = null );
                    $file_name = $filename;
                break;
            
                default:
                        $file_name = $file_name;
                break;
            endswitch;
            $custom = get_option('uploadplus_customprefix');
            if( $custom !== '' ):
                $return_file_name = $custom.$file_name;
            else:
                $return_file_name = $file_name;
            endif;
            return $return_file_name;
        }
        

        /*    sanitize uploaded file name    */
        function upp_mangle_filename($file_name){	
            global $sep;
        	$ext = self::find_extension($file_name);

            $utf8 = get_option('uploadplus_utf8toascii');
        	if( $utf8[0] == "1" ):
                $file_name = self::_utf8_transliteration( $file_name );
        	endif;

            $file_name = self::_clean_global( $file_name );
            $file_name = self::_clean_filename( $ext, $file_name );
            $file_name = self::_clean_case( $file_name );
            $file_name = self::_add_prefix( $file_name );

        	return $file_name;
        }
        
        function wp_handle_upload_prefilter( $meta ){
            $meta['name'] = self::upp_mangle_filename( $meta['name'] );		
            return $meta;
        }
        
        function wp_handle_upload($array){             
            global $action;
        	$current_name = self::find_filename($array['file']);
        	$new_name = self::upp_mangle_filename($current_name);		

        	$lpath = str_replace($current_name, "", urldecode($array['file']));
        	$wpath = str_replace($current_name, "", urldecode($array['url']));
        	$lpath_new = $lpath . $new_name;
        	$wpath_new = $wpath . $new_name;
        	if( @rename($array['file'], $lpath_new) )
        	return array(
        		'file' => $lpath_new,
        		'url' => $wpath_new,
        		'type' => $array['type']
        		);
        	return $array;
        }
                
        function add_attachment( $post_ID ){
        	if ( !$post = get_post( $post_ID ) )
        		return false;
            global $wpdb;

            $post = get_post($post_ID);
            $ext = self::find_extension($post->post_title);            
        	#$post_title = self::upp_mangle_filename($post->post_title);
        	$post_title = str_replace( array('-',"_"), " ", $post->post_title);
            $wpdb->query(  $wpdb->prepare( 
                    "UPDATE $wpdb->posts SET post_title='%s', post_name='%s' WHERE ID ='%d' LIMIT 1;", 
                    $post_title, 
                    $post_title, 
                    $post_ID
                    ) );
            return $post_ID;
        }
        
        function wp_read_image_metadata( $meta, $file, $sourceImageType ){
        	$current_name = self::find_filename($file);
        	$ext = self::find_extension($current_name);
        	$meta['caption'] = str_replace( array($ext, '_', '-'), ' ', $current_name);
            return $meta;
        }

        function sanitize_file_name( $filename, $filename_raw ){
        }

    }

#    $GLOBALS['swer-uploadplus-core'] = new SWER_uploadplus_core();
#}

?>