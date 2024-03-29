<?php


declare(strict_types=1);
if ( ! defined('ABPSATH') || ! defined( 'rozard' ) ){ exit; }




/** STRING SERIES */


    // string to plural
    function str_plural( string $string ) {

        if ( ! is_string( $string ) ) { return; }

        $number = array( 0, 1, 2, 3, 4, 5, 6, 7, 8 , 9 );

        $last = $string[strlen( $string ) - 1];

        if( in_array( $last, $number ) ) {       // convert y to ies
            return sanitize_text_field( $string );
        }                     
        else if( $last == 'y' ) {                // convert y to ies
            $cut = substr( $string, 0, -1 );   
            $plural = $cut . 'ies';
        }
        else if( $last == 's' ) {                // convert s to s
            $cut = substr( $string, 0, -1 );
            $plural = $string . '';           
        }
        else {                                   // just attach an s
            $plural = $string . 's';
        }
        $plural = sanitize_text_field( $plural );
        return $plural;
    }


    // string to text
    function str_text( string $string ) {

        if ( ! is_string( $string ) ) { return; }

        $string = preg_replace('/[^A-Za-z0-9]/', ' ', $string);
        return  $string;
    }


    // string to keys
    function str_keys( string $string ) {

        if ( ! is_string( $string ) ) { return; }

        $string = preg_replace('/[^A-Za-z0-9]/', '_', $string);
        $string = strtolower( $string );
        $string = sanitize_key( $string );
        return $string;
    }


    // string to slug
    function str_slug( string $string ) {

        if ( ! is_string( $string ) ) { return; }

        $string = preg_replace('/[^A-Za-z0-9]/', '-', $string);
        $string = strtolower($string);
        $string = sanitize_html_class( $string );
        return $string;
    }


    // string to filename
    function str_file( string $string ) {

        if ( ! is_string( $string ) ) { return; }

        $string = preg_replace('/[^A-Za-z0-9 .\-]/', '', $string);
        $string = strtolower( str_replace( ' ', '-', $string ) );
        return $string;
    }



/** ARRAYS SERIES */


    // array to html element
    function array_html( array $datum ) {
        
        if ( ! is_array( $string ) ) { return; }

        array_walk($datum, function(&$value, $key) {
            $value = '<span class="key">'. sanitize_key( $key ) .'</span><span class="delimit">:</span><span class="value">'. sanitize_text_field( $value ).'</span>';
        });
        $result = implode(' ', $datum);
        return $result; 
    }


    // array to html attribute
    function array_attr( array $datum ) {

        if ( ! is_array( $datum ) ) { return; }

        array_walk($datum, function(&$value, $key) {
            if ( empty( $value ) || $value === false ) {
                $value = null;
            }
            else if ( $value === true ) {
                $value = $key;
            }
            else {
                $value = sanitize_key( $key ) .'="'. sanitize_text_field( $value ) .'"';
            }
        });
        $result = implode(' ', $datum);
        return $result; 
    }

    
    // array to keys
    function array_key( array $datum ) {
    
        if ( ! is_array( $datum ) ) { return; }

        $sanitized = array();
        foreach( $datum as $keys => $data ) {
            $sanitized_data   = pure_keys( $data );
            $sanitized[$keys] = $sanitized_data;
        }
        return $sanitized;
    }


    // array to url query arguments
    function array_url_query( array $datum ) {

        if ( ! is_array( $string ) ) { return; }

        $whitelist = array('m', 'paged', 'order', 'orderby', 'post_type', 'posts_per_page', 'post_status', 'perm');

        // filter exclude data
        foreach( $datum as $key => $data ) {
            if ( ! in_array( $key, $whitelist ) ) {
                unset($datum[$key]);
            }
        }

        // render to uri
        array_walk($datum, function(&$value, $key) {
            
            if ( ! empty( $value )  ) {
                $value = '&'.sanitize_key( $key ) .'='. sanitize_html_class( $value );
            } 
        });
        $result = implode('', $datum);
        return $result; 
    }

    
    // convert arrow array value to keys 
    function arrays_key( array $datum ) {

        if ( ! is_array( $string ) ) { return; }

        $sanitized = array();
        foreach( $datum as $keys => $data ) {
            $sanitized_keys = str_keys( $keys );
            $sanitized_data = str_keys( $data );
            $sanitized[$sanitized_keys] = $sanitized_data;
        }
        return $sanitized;
    }


    // convert  arrow array to slug 
    function arrays_slug( array $datum ) {

        if ( ! is_array( $string ) ) { return; }

        $sanitized = array();
        foreach( $datum as $keys => $data ) {
            $sanitized_keys = str_keys( $keys );
            $sanitized_data = str_slug( $data );
            $sanitized[$sanitized_keys] = $sanitized_data;
        }
        return $sanitized;
    }

    // convert arrow array to text 
    function arrays_text( array $datum ) {

        if ( ! is_array( $string ) ) { return; }

        $sanitized = array();
        foreach( $datum as $keys => $data ) {
            $sanitized_keys = str_keys( $keys );
            $sanitized_data = str_text( $data );
            $sanitized[$sanitized_keys] = $sanitized_data;
        }
        return $sanitized;
    }



/** URI SERIES */


    // change uri to path
    function uri_path( string $uri ) {

        if ( filter_var( $uri, FILTER_VALIDATE_URL ) ) {
            $path = rtrim( ABSPATH, '/' ) . str_replace( get_site_url() , '',$uri );
        } else {
            $path = rtrim( ABSPATH, '/' ) . $uri;
        }

        if ( file_exists(  $path ) ) {
            return $path;
        } else {
            return false;
        }
    }

    // change path to uri
    function path_uri( string $data ) {
        if ( is_admin() ) {
            $uri = strstr($data, 'wp-admin');
        }
		$uri = get_site_url(). '/' .$uri;
		return $uri;
	}



    