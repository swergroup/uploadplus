<?php

class SWER_uploadplus_admin extends SWER_uploadplus_core {

 /* admin panel intro */
 static function upp_options_intro() {
  $test_string = 'αι βασίλης και σύζηγος.jpeg';
  $demo_string = parent::upp_mangle_filename( $test_string );
  ?>
  <p>The options listed below let you choose how to clean and customize uploads file and post name:<br>
  <ul>
      <li><strong>Separator</strong> &mdash; choose your favourite one: dashes, underscores or spaces.</li>
      <li><strong>Case</strong>  &mdash; lower, upper or camel case. Or leave its own.</li>
      <li><strong>Prefix</strong> &mdash; choose a date-based one, a random one, your blogname or a custom one!</li>
      <li><strong>Transliteration</strong> &mdash; convert letters from any charset to ascii! <a href="http://en.wikipedia.org/wiki/Transliteration">Learn More</a></li>
  </ul>
  </p>

  <p>According to actual options, a file named <code><?php _e( $test_string ); ?></code> would be saved as <code><?php _e( $demo_string ); ?></code></p>
  <?php
 }


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

 static function upp_options_box_case(){
  $case = get_option( 'uploadplus_case' );
  $cases = array(
    '0'	=> 'Leave its own', 
    '1'	=> 'lowercase', 
    '2'	=> 'UPPERCASE',
    '3' => 'CamelCase',
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

 static function upp_options_box_customprefix(){
  $prefix = get_option( 'uploadplus_customprefix' );
  $value = ( $prefix !== '' ) ? $prefix : '';
  echo '<p> <input type="text" name="uploadplus_customprefix" id="uploadplus_customprefix" value="'.$value.'" /></p>';
}


 static function upp_options_box_prefix(){
  global $sep;
  $clean = get_option( 'uploadplus_separator' );
  $prefix = get_option( 'uploadplus_prefix' );

  $datebased = array(
	 '1' => 'dd (like: ' . date( 'd' ) . $sep . ')',
	 '2' => 'mmdd (like: ' . date( 'md' ) . $sep . ')',
	 '3' => 'yymmdd (like: ' . date( 'ymd' ) . $sep . ')"',
	 '4' => 'yyyymmdd (like: ' . date( 'Ymd' ) . $sep . ')',
	 '5' => 'yyyymmddhhmm (like: ' . date( 'YmdHi' ) . $sep . ')',
	 '6' => 'yyyymmddhhmmss (like: ' . date( 'YmdHis' ) . $sep . ')',
 	 '7' => 'unix timestamp (like: ' . date( 'U' ) . $sep . ')',
	 );

  $otherstyles = array(
	 '8' => '[random (mt-rand)] '.mt_rand().$sep,
	 '9' => '[random md5(mt-rand)] '.md5( mt_rand() ).$sep,
 	 '10' => '[blog name] '.str_replace( array( '.', ' ', '-', '_' ) ,$sep, get_bloginfo( 'name' ) ).$sep,
 	 'A' => '[short blog name] '.str_replace( array( '.', '_', '-', ' '), '', get_bloginfo( 'name' ) ).$sep,
   'B' => 'WordPress style (unique filename)',
  );

  $nullval = ( $prefix == '' ) ? 'selected="selected"' : '';
  echo '
	<select name="uploadplus_prefix" id="uploadplus_prefix">	
	<option value="0" label="No prefix or custom prefix" '.$nullval.'>No prefix or custom prefix</option>
	<optgroup label="Date Based">
	';
  $flag = $oflag = '';
  foreach ( $datebased as $key => $val ) :
    $flag = ( $prefix == $key && $nullval == '' ) ? 'selected="selected"' : '';
    echo '<option value="'.$key.'" label="'.$val.'" '.$flag.'>'.$val.'</option>
    ';
  endforeach;
  echo'
  </optgroup>
  <optgroup label="Other Prefix">
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


 static function upp_options_box_utf8toascii(){
  $utf8ornot = get_option( 'uploadplus_utf8toascii' );
  $options = array(
    '0'	=> 'Do nothing', 
    '1'	=> 'Transliterate UTF8 chars into ASCII',
  );
  foreach ( $options as $uk => $uv ) :
   if ( $utf8ornot[0] == $uk) $flag = 'checked="checked"'; else $flag = '';
   echo '<input type="radio" name="uploadplus_utf8toascii[]" id="uploadplus_utf8toascii-'.$uk.'" value="'.$uk.'" '.$flag.'/>'.$uv.' <br>';
  endforeach;
}    

}
