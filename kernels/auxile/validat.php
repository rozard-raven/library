<?php


declare(strict_types=1);
if ( ! defined('ABPSATH') || ! defined( 'rozard' ) ){ exit; }



/** GENERAL HELPER */


    function general_validator( array $data, string $validator ) {

        $authentication = array();
        foreach ( $data as $access_object ) 
        {
            $status = ( $access_object ===  $validator || $access_object ===  'global' ) ? 'granted' : 'denied'; 
            array_push( $authentication, $status );
        }
        $status = ( in_array( 'granted' , $authentication ) ) ? true : false;  
        return $status;
    }
    
    

/** VALUE SERIES */


    function is_bole( $data ) {
        if ( is_bool( $data ) ) {
            return $data;
        }
        return null;
    }



/** ARRAY SERIES */


    // array contains
    function array_has( string $string , array $array ) {

        if ( ! is_array( $array ) ) {
            if ( WP_DEBUG === true ) { der('This value its not a array'); }
            return;
        } 
            
        if ( ! is_string( $string ) ) {
            if ( WP_DEBUG === true ) { der('This value its not a string'); }
            return;
        } 
    
        $result = '';
        foreach ( $array as $key => $data ) {
            if ( str_contains( $string, $data) === true  )  {
                $result = true;
                break;
            } 
            else {
                continue;
            } 
        }
        return ( empty( $result) ) ? false : $result;
    }



/** USER SERIES */


    // role existing validator
    function role_exist( array $role ) {

        if ( is_user_logged_in() && empty( $role ) === false ) 
        {
            $value = $GLOBALS['wp_roles']->is_role( $role );
            return $value;
        } 
        return array();
    }

    // user have role validator
    function has_role( array $data ) {


        $authentication = array();
        foreach ( $data as $access_roles ) 
        {
            $users_roles = get_user_role();
            $role_access = ( in_array( $access_roles , $users_roles ) ) ? 'granted' : 'denied'; 
            array_push( $authentication, $role_access );
        }
        $status = ( in_array( 'granted' , $authentication ) ) ? true : false;  
        return $status;

    }

    // user multiple capabilies validator
    function has_caps( array $data ) {

        if ( function_exists( 'current_user_can') && empty( $data ) === false ) 
        {
            $authentication = array();
            foreach ( $data as $caps_validator ) 
            {
                $status = ( current_user_can( $caps_validator ) === true ) ? 'granted' : 'denied'; 
                array_push( $authentication, $status );
            }
            $status = ( in_array( 'granted' , $authentication ) ) ? true : false;  
            return $status;
        }
        return null;
    }


    // user must have all capabilities 
    function must_caps( array $data ) {

        if ( function_exists( 'current_user_can') && empty( $data ) === false ) 
        {
            $authentication = array();
            foreach ( $data as $caps_validator ) 
            {
                $status = ( current_user_can( $caps_validator ) === true ) ? 'granted' : 'denied'; 
                array_push( $authentication, $status );
            }
            $status = ( in_array( 'denied' , $authentication ) ) ?  false  : true;  
            return $status;
        }
        return null;
    }


    // user single capabilities validator
    function usr_can( string $capabilities, $args = null ) {

        if ( current_user_can( $capabilities, $args ) ){
            return true;
        }
        return false;
    }



/** URI AND PAGING SERIES */


    // uri validator
    function is_uris( string $uri ) {

        if ( str_contains( esc_url( $_SERVER["REQUEST_URI"] ), $uri ) && is_string( $uri ) )  {
            return true;
        }
        else if ( ! is_string( $uri ) ) {
            return false;
        }
        else {
            return false;
        }
    }

    // uri single query validator
    function uri_has( string $query ) {
        if ( str_contains( take_uri(), $query ) ) {
            return true;
        }
        return false;
    }


    // uri multiple query validator
    function uris_has( array $queries ) {
        $result = array();
        foreach ( $queries as $query ) {
            $result[] = ( str_contains( take_uri(), $query ) ) ? 'granted' : 'denied';
        }
        if ( in_array( 'granted', $result ) ) {
            return true;
        }
        return false;
    }


    // admin uri validator 
    function has_link( array $data ) {

        if (  empty( $data ) === false ) 
        {
            $current_links = get_page_link();  // current admin page uri
            $status = general_validator( $data, $current_links );
            return $status;
        }
        return null;
    }

    // screen id validator  
    function has_screen( array $data ) {

        if ( function_exists( 'get_current_screen' ) && empty( $data ) === false ) 
        {
            $current_screen = get_current_screen()->id;    // current screen id
            $status = general_validator( $data, $current_screen );
            return  $status ;
        }
        return null;
    }

    // uri active
    function uri_active( $data ) {

        // Initializing new session
        $ch = curl_init( $data );
        
        // Request method is set
        curl_setopt( $ch, CURLOPT_NOBODY, true );
        
        // Executing cURL session
        curl_exec($ch);
        
        // Getting information about HTTP Code
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        

        // Testing for 404 Error
        if($retcode === 404) {
            $status = false;
        }
        else {
            $status = true;
        }

        curl_close($ch);

        return $status;
    }


/** FILE SYSTEM SERIES */

    // file extension and existing validator
    function has_file( string $path, array $extensions ) {

        $validator   = array();
        $exten_temps = array();
        $target_ext  = pathinfo( $path, PATHINFO_EXTENSION);


        // path validator
        if ( file_exists( $path ) === true ) {
            array_push( $validator, 'valid' );
        } 
        else {
            array_push( $validator, 'invalid' );
        }


        // extension validator
        if ( ( ! empty( $extensions ) ) ) {

            foreach( $extensions as $extension )  {
                if ( $target_ext === $extension ) {
                    array_push( $exten_temps, 'valid' );
                } else {
                    array_push( $exten_temps, 'invalid' );
                    dev( 'array', 'File extension is not match, current file extension is "' . $extension .'" and target file extension is "' . $target_ext .'"');
                }
            }

            if ( in_array( 'valid',  $exten_temps ) ) {
                array_push( $validator, 'valid' );
            } 
        }

        $status = ( in_array( 'invalid',  $validator ) ) ? false : true ;
        return $status;
    }



/** POST OBJECT SERIES */

    // current post type validator 
    function has_post_type( array $data ) {

        if (  empty( $data ) === false ) 
        {
            $current_format = get_post_type();
            general_validator( $data, $current_format );
            return true;
        }
        return null;
    }