<?php


class SWER_uploadplus_core{
    
    /* find extension */
    function upp_findexts ($filename) { 
    	$exts = split("[/\\.]", $filename) ; 
    	$n = count($exts)-1; 
    	$exts = $exts[$n]; 
    	return $exts; 
    } 

    /* find full filename */
    function upp_find_filename ($filename) { 
    	$explode = explode("/",$filename);
    	$explode = array_reverse($explode);
    	return $explode[0];
    } 

    /*    sanitize uploaded file name    */
    function upp_mangle_filename($file_name){	

    	/* remove internal dots (cosmetical, it would be done by WP, but we need to display it :)*/
    	$ext = SWER_uploadplus_core::upp_findexts($file_name);
    	$file_name = str_replace(".".$ext,"",$file_name);
    	$file_name = str_replace(".","",$file_name);

    	// initial cleaning
    	$file_name = str_replace("(","",$file_name);
    	$file_name = str_replace(")","",$file_name);
    	$file_name = str_replace("'","",$file_name);
    	$file_name = str_replace('"',"",$file_name);
    	$file_name = str_replace(',',"",$file_name);

    	// some language-based prefilter. props denis.
    	$de_from 	= array('ä','ö','ü','ß','Ä','Ö','Ü');
    	$de_to 		= array('ae','oe','ue','ss','Ae','Oe','Ue');
    	$file_name	= str_replace($de_from, $de_to, $file_name);

        $utf8 = get_option('uploadplus_utf8toascii');
    	if( $utf8[0] === 1 ) $file_name = utf8_to_ascii($file_name); 

    	$file_name = $file_name.".".$ext;

        $case = get_option('uploadplus_case');
    	switch( $case[0] ):
    		case "1":
    			$file_name = utf8_strtolower($file_name);
    			break;
    		case "2":
    			$file_name = utf8_strtoupper($file_name);
    			break;
    	endswitch;

        $cleanlevel = get_option('uploadplus_cleanlevel');
    	switch( $cleanlevel[0] ):
    	case "1":
    		$file_name = ereg_replace("[^A-Za-z0-9._]", "-", $file_name);
    		$file_name = utf8_ireplace("_", "-", $file_name);	
    		$file_name = utf8_ireplace(" ", "-", $file_name);
    		$file_name = utf8_ireplace("%20", "-", $file_name);
    		break;
    	case "2":	
    		$file_name = ereg_replace("[^A-Za-z0-9._]", "", $file_name);
    		$file_name = utf8_ireplace("_", "", $file_name);	
    		$file_name = utf8_ireplace("-", "", $file_name);	
    		$file_name = utf8_ireplace("%20", "", $file_name);
    		break;
    	case "3":
    		$file_name = ereg_replace("[^A-Za-z0-9._]", "_", $file_name);
    		$file_name = utf8_ireplace("-", "_", $file_name);	
    		$file_name = utf8_ireplace(" ", "_", $file_name);
    		$file_name = utf8_ireplace("%20", "_", $file_name);
    		break;
    	endswitch;

    	$sep = ( $cleanlevel[0] ==='1') ? "-" : "";
    	if(!$sep) $sep = ( $cleanlevel[0] =='3') ? "_" : "";

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
            $file_name = $custom.$file_name;
        else:
            $file_name = $filename;
        endif;

    	return $file_name;
    }

    /* apply out changes to the real file while it's being moved to its destination */
    // $array( 'file' => $new_file, 'url' => $url, 'type' => $type );
    function upp_rename($array){ 
    global $action;
    	$current_name = SWER_uploadplus_core::upp_find_filename($array['file']);
    	$current_name = urldecode($current_name);
    	$new_name = SWER_uploadplus_core::upp_mangle_filename($current_name);		
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


class SWER_uploadplus_admin{


    /* admin panel intro */
    function upp_options_intro() {
    	$test_string1 = "WordPress Manual (for dummies and experts, if they're good at it) 2.2nd Edition.pdf";
    	#$test_string1 = "نرحب بكم في الموقع الرسمي لبرنامج ووردبريس المعرب،.pdf";
    	$demo_string1 = SWER_uploadplus_core::upp_mangle_filename($test_string1);
    	echo "<a name='uploadplus'></a>";
    	echo "<p>This plugin allows you to rename every file you upload, and in this page you can define this behaviour. ";
    	echo("
    	<blockquote style='width:70%;'>
    	<table class='widefat'style='background:transparent;'>
    	<tr> <th>The file:</th> <td><code>".$test_string1."</code></td> </tr>
    	<tr> <th>will be saved as:</th> <td><code style='font-weight:bold;'>".$demo_string1."</code></td> </tr>
    	</table>
    	</blockquote>
    	");
    }


    function upp_options_box_cleanlevel(){
        $actual = get_option('uploadplus_cleanlevel');

    	$styles = array(
    		"1" => array('label'=>'Convert spaces and underscores into dashes', 'demo'=>'wordpress-manual.pdf'), 
    		"2" => array('label'=>'Strip all spaces/dashes/underscores', 'demo'=>'wordpressmanual.pdf'), 
    		"3" => array('label'=>'Convert spaces into underscores (dashes allowed)', 'demo'=>'wordpress-manual.pdf'), 
    		);
    	foreach($styles as $key=>$info):
    		if($actual[0]==$key)	$flag = 'checked="checked"';	else $flag = '';
    		echo '
    		<p><input type="radio" name="uploadplus_cleanlevel[]" id="uploadplus_style-'.$key.'" '.$flag.' value="'.$key.'"/>
    		'.$info['label'].' <small>like in:</small> <code> '.$info['demo'].' </code></p>
    		';
    	endforeach;
    }

    function upp_options_box_case(){
        $case = get_option('uploadplus_case');

    	$cases = array(
    	"0"	=> "Leave it whatever it is", 
    	"1"	=> "Make all lowercase", 
    	"2"	=> "Make all UPPERCASE"
    	);
    	foreach($cases as $ca=>$se):
    		if( $case[0] == $ca): $flag = 'checked="checked"'; else: $flag = ""; endif;
    		echo '<p><input type="radio" name="uploadplus_case[]" id="uploadplus_lettercase-'.$ca.'" value="'.$ca.'" '.$flag.'/>'.$se.'</p>';
    	endforeach;
    }

    function upp_options_box_customprefix(){
        $prefix = get_option('uploadplus_customprefix');
        $value = ( $prefix !== '' ) ? $prefix : '';
        echo '<p> <input type="text" name="uploadplus_customprefix" id="uploadplus_customprefix" value="'.$value.'" /></p>';
    }


    function upp_options_box_prefix(){
        $clean = get_option('uploadplus_cleanlevel');
        $prefix = get_option('uploadplus_prefix');
        
        $sep = ($clean[0]=='1') ? "-" : "";
        if(!$sep) $sep = ($clean[0]=='3') ? "_" : "";


        $datebased = array(
        	"1" => 'dd (like: '.date('d').$sep.')',
        	"2" => 'mmdd (like: '.date('md').$sep.')',
        	"3" => 'yymmdd (like: '.date('ymd').$sep.')"',
        	"4" => 'yyyymmdd (like: '.date('Ymd').$sep.')',
        	"5" => 'yyyymmddhhmm (like: '.date('YmdHi').$sep.')',
        	"6" => 'yyyymmddhhmmss (like: '.date('YmdHis').$sep.')',
         	"7" => 'unix timestamp (like: '.date('U').$sep.')',
        	);

        $otherstyles = array(
        	"8" => '[random (mt-rand)] '.mt_rand().$sep,
        	"9" => '[random md5(mt-rand)] '.md5(mt_rand()).$sep,
         	"10" => '[blog name] '.str_replace( array(".", " ", "-", "_") ,$sep,strtolower(get_bloginfo('name'))).$sep,
         	"A" => '[short blog name] '.str_replace( array(".","_","-"," "),"",strtolower(get_bloginfo('name'))).$sep,
            "B" => 'WordPress style (unique filename)'
        	);

        $nullval = ($prefix=="") ? 'selected="selected"' : "";
        echo '
        	<select name="uploadplus_prefix" id="uploadplus_prefix">	
        	<option value="" label="No Prefix / Custom Prefix" '.$nullval.'>No Prefix / Custom Prefix</option>
        	<optgroup label="Date Based">
        	';
        	$flag = $oflag = "";
        	foreach($datebased as $key=>$val):
        		$flag = ($prefix==$key && $nullval=="") ? 'selected="selected"' : "";
        		echo '<option value="'.$key.'" label="'.$val.'" '.$flag.'>'.$val.'</option>
        		';
        	endforeach;
        	echo'
        	</optgroup>
        	<optgroup label="Other Styles">
        	';
        	foreach($otherstyles as $okey=>$oval):
        		$oflag = ($prefix==$okey && $nullval=="") ? 'selected="selected"' : "";
        		echo '<option value="'.$okey.'" label="'.$oval.'" '.$oflag.'>'.$oval.'</option>
        		';
        	endforeach;

        	echo '
        	</optgroup>	
        	</select>
        	<br/>
        	<small>Prefix will follow the other rules, so if you choose dashes, it will use dashes.</small>
        ';
    }


    function upp_options_box_utf8toascii(){
        $utf8ornot = get_option('uploadplus_utf8toascii');

    	$options = array(
    	"0"	=> "Don't convert <code>(safe mode)</code>", 
    	"1"	=> "Yes, please, convert utf8 characters into ASCII"
    	);
    	foreach($options as $uk=>$uv):
    		if( $utf8ornot[0] == $uk): $flag = 'checked="checked"'; else: $flag = ""; endif;
    		echo '<input type="radio" name="uploadplus_utf8toascii[]" id="uploadplus_utf8toascii-'.$uk.'" value="'.$uk.'" '.$flag.'/>'.$uv.' &nbsp; ';
    	endforeach;
    	echo "<br/><small>(Learn more about transcription on <a href='http://en.wikipedia.org/wiki/Transcription_(linguistics)'>Wikipedia</a>).</small>";
    }    

}

?>