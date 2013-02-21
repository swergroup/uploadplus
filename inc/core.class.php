<?php


if( ! array_key_exists( 'swer-uploadplus-core', $GLOBALS ) ) { 

    class SWER_uploadplus_core{
    
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

        /*    sanitize uploaded file name    */
        function upp_mangle_filename($file_name){	
            $sep = "-";

        	$ext = self::find_extension($file_name);

            $utf8 = get_option('uploadplus_utf8toascii');
        	if( $utf8[0] == "1" ):
                $Ar = new I18N_Arabic('Transliteration');
                $file_name = trim( $Ar->ar2en( $file_name ) );
        	    $file_name = URLify::downcode($file_name); 
        	endif;

        	$file_name = str_replace(".".$ext,"",$file_name);
        	$file_name = str_replace(".","",$file_name);

            $file_name = preg_replace('~[^\\pL0-9_]+~u', '-', $file_name);
    		$file_name = preg_replace ('/^\s+|\s+$/', '', $file_name);
            
        	$file_name = $file_name.".".$ext;

            $case = get_option('uploadplus_case');
        	switch( $case[0] ):
        		case "1":
        			$file_name = strtolower($file_name);
        			break;
        		case "2":
        			$file_name = strtoupper($file_name);
        			break;
        		default:
        		    $file_name = trim($file_name);
        		    break;
        	endswitch;

            $cleanlevel = get_option('uploadplus_cleanlevel');
        	switch( $cleanlevel[0] ):
        	case "1":
        		$file_name = ereg_replace("[^A-Za-z0-9._]", "-", $file_name);
        		$file_name = preg_replace ('/[-\s]+/', '-', $file_name);
        		$sep = "-";
        		break;
        	case "2":	
        		$file_name = ereg_replace("[^A-Za-z0-9._]", "", $file_name);
        		$file_name = preg_replace ('/[-\s]+/', '', $file_name);
        		$sep = "-";
        		break;
        	case "3":
        		$file_name = ereg_replace("[^A-Za-z0-9._]", "_", $file_name);
        		$file_name = preg_replace ('/[-\s]+/', '_', $file_name);
        		$sep = "_";
        		break;
        	endswitch;

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
    			case "10":		$file_name = str_replace( array(".","_","-"," ") ,$sep, utf8_to_ascii(get_bloginfo('name'))).$sep.$file_name; break;
    			case "A":		$file_name = str_replace( array(".","_","-"," ") ,"", utf8_to_ascii(get_bloginfo('name'))).$sep.$file_name;	break;
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
        
        function wp_handle_upload_prefilter( $meta ){
            $new_meta = $meta;
            $new_meta['name'] = self::upp_mangle_filename( $meta['name'] );		
            #print_r($new_meta); die();
            return $new_meta;
        }
        

        /**
         * upp_rename   old plugin core 
         */
        function upp_rename($array){ 
        global $action;
        	$current_name = self::find_filename($array['file']);
        	$current_name = urldecode($current_name);
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

    }

    $GLOBALS['swer-uploadplus-core'] = new SWER_uploadplus_core();
}

?>