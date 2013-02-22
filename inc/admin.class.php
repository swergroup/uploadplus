<?php

class SWER_uploadplus_admin{


    /* admin panel intro */
    function upp_options_intro() {
    	$test_string1 = "WordPress Manual (for dummies and experts, if they're good at it) 2.2nd Edition.pdf";
    	#$test_string1 = "نرحب بكم في الموقع الرسمي لبرنامج ووردبريس المعرب،.pdf";
    	$test_string1 = "Τὸ δημιούργημα τῆς κόκα κόλα ποὺ ἀποδεχθήκαμε ὥς Ἅη Βασίλη.jpg";
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
    	    "0" => array('label'=>'Do not modify', 'demo'=>'Wordpress Manual.pdf'),
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
    	"0"	=> "Do not modify", 
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