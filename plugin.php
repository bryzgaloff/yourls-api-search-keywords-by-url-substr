<?php
    
    /*
     Plugin Name: Update Shortened URL
     Plugin URI: https://github.com/timcrockford/yourls-api-edit-url
     Description: Define a custom API action 'update' and 'geturl'
     Version: 0.2
     Author: Tim Crockford
     Author URI: http://codearoundcorners.com/
     */
    
    yourls_add_filter( 'api_action_update', 'api_edit_url_update' );
    yourls_add_filter( 'api_action_geturl', 'api_edit_url_get' );
    
    function api_edit_url_update() {
        if( !isset( $_REQUEST['shorturl'] ) ) {
            return array(
                'statusCode' => 400,
	    	'status' => 'fail',
                'simple'     => "Need a 'shorturl' parameter",
                'message'    => 'error: missing param',
            );
        }

        if( !isset( $_REQUEST['url'] ) ) {
            return array(
                'statusCode' => 400,
	         'status' => 'fail',
                'simple'     => "Need a 'url' parameter",
                'message'    => 'error: missing param',
            );
        }

        $shorturl = $_REQUEST['shorturl'];
        $url = urldecode($_REQUEST['url']);

        if( ! yourls_is_shorturl( $shorturl ) ) {
            return array(
                'statusCode' => 404,
	 	 'status' => 'fail',
                'simple '    => 'Error: short URL not found',
                'message'    => 'error: not found',
            );
        }

        $title = '';
        if ( isset($_REQUEST['title']) ) $title = $_REQUEST['title'];

        if( yourls_edit_link( $url, $shorturl, $shorturl, $title ) ) {
            return array(
                'statusCode' => 200,
                'simple'     => "Shorturl $shorturl updated to " . $url ,
                'message'    => 'success: updated',
            );
        } else {
            return array(
                'statusCode' => 500,
	 	'status' => 'fail',
                'simple'     => 'Error: could not edit shorturl, not sure why :-/',
                'message'    => 'error: unknown error',
            );
        }
    }

    function api_edit_url_get() {
        if ( ! isset( $_REQUEST['url'] ) ) {
            return array(
                'statusCode' => 400,
	 	'status' => 'fail',
                'simple'     => "Need a 'url' parameter",
                'message'    => 'error: missing param',
            );
        }

        $url = urldecode($_REQUEST['url']);
        $url_exists = yourls_url_exists($url);

        if ( $url_exists ) {
            return array(
                'statusCode' => 200,
                'simple'     => "Keyword for $url is " . $url_exists->keyword,
                'message'    => 'success: found',
                'keyword'    => $url_exists->keyword,
            );
        } else {
            return array(
                'statusCode' => 500,
	 	'status' => 'fail',
                'simple'     => "Error: could not find keyword for url $url",
                'message'    => 'error: not found',
                'keyword'    => '',
            );
        }
    }
