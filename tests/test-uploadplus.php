<?php

require_once( "inc/core.class.php");

class UploadPlusTests extends WP_UnitTestCase {

    function setUp(){
        parent::setUp();
        $this->plugin = $GLOBALS['swer-uploadplus'];
    }

    function testPluginInit(){
        $this->assertFalse( null == $this->plugin );
    }
    
    function testFindExtensions(){
        $this->assertEquals( 'jpeg', $this->plugin->find_extension( 'filename.jpeg' ), 'Extension not found' );
    }

    function testFindFilename(){
        $this->assertEquals( 'filename.jpeg', $this->plugin->find_filename( '/Users/utente/Sites/filename.jpeg' ), 'Filename not found' );        
    }

    function testSanitizeGreeklish(){
		$convert = $this->plugin->sanitize_greeklish( "Αισθάνομαι τυχερός" );
        $this->assertEquals( 'esthanome ticheros', $convert, 'String is not greeklish' );
    }

    function testAddPrefix(){
    	$filename = "testfilename.ext";

    	$test1 = $this->plugin->_add_prefix( $filename, '1', '');
    	$this->assertEquals( date('d').'testfilename.ext', $test1, 'Prefix #1 not equal');

    	$test2 = $this->plugin->_add_prefix( $filename, '2', '');
    	$this->assertEquals( date('md').'testfilename.ext', $test2, 'Prefix #3 not equal');

    	$test3 = $this->plugin->_add_prefix( $filename, '3', '');
    	$this->assertEquals( date('ymd').'testfilename.ext', $test3, 'Prefix #3 not equal');

    	$test4 = $this->plugin->_add_prefix( $filename, '4', '');
    	$this->assertEquals( date('Ymd').'testfilename.ext', $test4, 'Prefix #4 not equal');

    	$test5 = $this->plugin->_add_prefix( $filename, '5', '');
    	$this->assertEquals( date('YmdHi').'testfilename.ext', $test5, 'Prefix #5 not equal');

    	$test6 = $this->plugin->_add_prefix( $filename, '6', '');
    	$this->assertEquals( date('YmdHis').'testfilename.ext', $test6 ,'Prefix #6 not equal');

    	$test7 = $this->plugin->_add_prefix( $filename, '7', '');
    	$this->assertEquals( date('U').'testfilename.ext', $test7, 'Prefix #7 not equal');

    	/* cases 8 and 9 are random, so we can't test them */

    	$test10 = $this->plugin->_add_prefix( $filename, '10', '');
    	$this->assertEquals( 'TestBlog'.'testfilename.ext', $test10, 'Prefix #10 not equal');

    	$test11 = $this->plugin->_add_prefix( $filename, 'A', '');
    	$this->assertEquals( 'TestBlog'.'testfilename.ext', $test11, 'Prefix #11 not equal');

    	$test12 = $this->plugin->_add_prefix( $filename, 'B', '');
    	$this->assertEquals( 'testfilename.ext', $test12, 'Prefix #12 not equal');
    }




}

