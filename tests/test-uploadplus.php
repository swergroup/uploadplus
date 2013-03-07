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
        $this->assertEquals( 'jpeg', $this->plugin->find_extension( 'filename.jpeg' ), 'extract extension from file name' );
    }

    function testFindFilename(){
        $this->assertEquals( 'filename.jpeg', $this->plugin->find_filename( '/Users/utente/Sites/filename.jpeg' ), 'extract last filename from path' );        
    }

    function testSanitizeGreeklish(){
		$convert = $this->plugin->sanitize_greeklish( "Αισθάνομαι τυχερός" );
        $this->assertEquals( 'esthanome ticheros', $convert, 'convert greek to greeklish' );
    }


}

