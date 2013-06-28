<?php

require_once( 'inc/core.class.php' );

class UploadPlusTests extends WP_UnitTestCase {

 function setup(){
     parent::setUp();
     $this->plugin = $GLOBALS['swer-uploadplus'];
 }

 function test_init(){
     $this->assertFalse( null == $this->plugin );
 }

 function test_version(){
     $this->assertEquals( '3.3.0-b2', $this->plugin->version, 'Option: uploadplus_version does not match.' );
 }

 function test_extensions(){
     $this->assertEquals( 'jpeg', $this->plugin->find_extension( 'filename.jpeg' ), 'Extension #1 is not jpeg' );
     $this->assertEquals( 'gif', $this->plugin->find_extension( 'some boring file name.gif' ), 'Extension #2 is not gif' );
     $this->assertEquals( 'pdf', $this->plugin->find_extension( 'Upload+ WordPress plugin || SWERgroup (20120425).png.pdf' ), 'Extension #3 is not PDF' );
 }
 
 function test_greeklish(){
     $convert = $this->plugin->sanitize_greeklish( 'Αισθάνομαι τυχερός' );
     $this->assertEquals( 'esthanome ticheros', $convert, 'String is not greeklish' );
 }

 function test_random(){
     $filename = "some boring, long and stupid file name.gif";
     update_option( 'uploadplus_random', 'on' );
     $res = $this->plugin->upp_mangle_filename( $filename );
     $this->assertTrue( $res !== $filename );

     $name = str_replace( '.gif', '', $res );
     $this->assertTrue( strlen( $name ) == 20, 'Random file name should be 20 characters long + extension.' );
 }

 function test_prefix(){
 	$filename = 'testfilename.ext';

 	$test1 = $this->plugin->_add_prefix( $filename, '1', '' );
 	$this->assertEquals( date( 'd' ).'testfilename.ext', $test1, 'Prefix #1 not equal' );

 	$test2 = $this->plugin->_add_prefix( $filename, '2', '' );
 	$this->assertEquals( date( 'md' ).'testfilename.ext', $test2, 'Prefix #3 not equal' );

 	$test3 = $this->plugin->_add_prefix( $filename, '3', '' );
 	$this->assertEquals( date( 'ymd' ).'testfilename.ext', $test3, 'Prefix #3 not equal' );

 	$test4 = $this->plugin->_add_prefix( $filename, '4', '' );
 	$this->assertEquals( date( 'Ymd' ).'testfilename.ext', $test4, 'Prefix #4 not equal' );

 	$test5 = $this->plugin->_add_prefix( $filename, '5', '' );
 	$this->assertEquals( date( 'YmdHi' ).'testfilename.ext', $test5, 'Prefix #5 not equal' );

 	$test6 = $this->plugin->_add_prefix( $filename, '6', '' );
 	$this->assertEquals( date( 'YmdHis' ).'testfilename.ext', $test6 ,'Prefix #6 not equal' );

 	$test7 = $this->plugin->_add_prefix( $filename, '7', '' );
 	$this->assertEquals( date( 'U' ).'testfilename.ext', $test7, 'Prefix #7 not equal' );

 	/* cases 8 and 9 are random, so we can't test them */

 	$test10 = $this->plugin->_add_prefix( $filename, '10', '' );
 	$this->assertEquals( 'TestBlog'.'testfilename.ext', $test10, 'Prefix #10 not equal' );

 	$test11 = $this->plugin->_add_prefix( $filename, 'A', '' );
 	$this->assertEquals( 'TestBlog'.'testfilename.ext', $test11, 'Prefix #A not equal' );

 	$test12 = $this->plugin->_add_prefix( $filename, 'B', '' );
 	$this->assertEquals( 'testfilename.ext', $test12, 'Prefix #B not equal' );

     $test13 = $this->plugin->_add_prefix( $filename, null, 'custom_' );
     $this->assertEquals( 'custom_testfilename.ext', $test13, 'Prefix custom not equal' );

     $test14 = $this->plugin->_add_prefix( $filename, '1', 'custom_' );
     $this->assertEquals( 'custom_'.date( 'd' ).'testfilename.ext', $test14, 'Prefix custom not equal' );

 }




}

