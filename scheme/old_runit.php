<?php

declare(strict_types=1);
if ( ! defined('ABPSATH') || ! defined( 'rozard' ) ){ exit; }


if ( ! class_exists( 'rozard_scheme_engine' ) ) {


    class rozard_scheme_engine{

       
        private $node = array( 
            'cockpit' =>  array( 
                            'index.php', 
                            'my-sites', 
                            'cockpit-', 
                            'admin-ajax' 
                        ), 

            'feeders' =>  array( 
                            'comments.php',  
                            'feeder-', 
                            'admin-ajax' 
                        ), 

            'manages' =>  array( 
                            'users.php', 
                            'user-new.php', 
                            'profile.php?wp_http_referer=', 
                            'themes.php', 
                            'plugins.php', 
                            'tools.php', 
                            'import.php', 
                            'export.php', 
                            'site-health.php', 
                            'export-personal-data.php', 
                            'erase-personal-data.php', 
                            'options-', 
                            'manage-', 
                            'admin-ajax' 
                        ),

            'formats' =>  array( 
                            'edit.php', 
                            'upload.php', 
                            'admin-ajax.php' 
                        ), 
        );


        private $core = array( 
            'insight' =>  array( 
                            'network/index.php', 
                            'panel-', 
                            'edit.php', 
                            'admin-ajax' 
                        ), 
            'service' =>  array( 
                            'sites', 
                            'site-', 
                            'edit.php', 
                            'admin-ajax' 
                        ), 
            'manages' =>  array( 
                            'users', 
                            'plugins', 
                            'themes', 
                            'admin-ajax' 
                        ),
            'wizards' =>  array( 
                            'upgrade.php', 
                            'update-', 
                            'wizard-', 
                            'admin-ajax' 
                        ),  
            'setting' =>  array( 
                            'settings.php', 
                            'edit.php', 
                            'admin-ajax' 
                        ), 
            'customs' =>  array( ), 
        );
  


    /** RUNITS */


        public function __construct() {
          $this->hook();
        }


        private function hook() {
            
            // system
            if ( is_network_admin() ) {
                add_filter( 'custom_menu_order',  array( $this, 'system_menu' ) );
                add_action( 'plugins_loaded',     array( $this, 'system_init' ) );
            }
            

            // service
            if ( is_admin() ){
                add_filter( 'custom_menu_order',  array( $this, 'servis_menu' ) );
                add_action( 'plugins_loaded',     array( $this, 'servis_init' ) );
            }


            // themes 
            $this->themes_init();
            

            // global
            add_action( 'plugins_loaded',  array( $this, 'object_init' ) );
        }


    /** OBJECT */


        public function object_init() {


            require_once 'objects/post.php';
            new rozard_scheme_object_post;


            require_once 'objects/term.php';
            new rozard_scheme_object_term;

            
            if ( ! is_admin() ) {
                return;
            }

            require_once 'objects/boxs.php';
            new rozard_scheme_object_boxs;
        }



    /** SYSTEM */


        public function system_menu( $order ) {

            if ( ! is_network_admin() ) {
                return $order;
            }

            global $menu, $submenu, $pagenow, $former;


            // redirect 
            if ( $pagenow === 'index.php' && ! uri_has( 'node=' ) ) {
                wp_safe_redirect( network_admin_url( 'index.php?node=general' ) );
            }
    

            // insight
            $menu[2][0]  = 'Insights';
            $menu[2][2]  = 'index.php?node=general';
            $menu[2][6]  = 'dashicons-chart-pie';
            $submenu['index.php'][0][0]  = 'Dashboard';
           

            // service
            $menu[5][0] = 'Services';
            $menu[5][2] = 'sites.php?node=manage';
            $menu[5][6] = 'dashicons-cloud';
            $submenu['sites.php'][5][0]  = 'Manages';
            $submenu['sites.php'][5][4]  = 'manage';
         

            // manage
            $menu[10][0] = 'Manage';
            $menu[10][2] = 'users.php?node=users';
            $menu[10][6] = 'dashicons-art';
          

            // wizard
            $menu[15][0] = 'Wizards';
            $menu[15][1] = 'upgrade_network';
            $menu[15][2] = 'upgrade.php?node=general';
            $menu[15][3] = '';
            $menu[15][4] = 'menu-top menu-icon-appearance';
            $menu[15][5] = 'menu-appearance';
            $menu[15][6] = 'dashicons-screenoptions';
          
           
            // setting 
            $menu[25][2] = 'settings.php';
            $menu[25][6] = 'dashicons-layout';
            

            // cleanup 
            unset( $submenu['index.php'][0] );
            unset( $submenu['index.php'][10] );
            unset( $submenu['index.php'][15] );
            unset( $submenu['sites.php'][10] );
            unset( $menu[20] );
  
            return $order;
        }


        public function system_init() {


            if ( ! is_network_admin() ) {
                return;
            }


             if ( uris_has( $this->core['insight'] ) ) {
                require_once 'system/insights.php';
                new rozard_system_insight;
            }

            
            if ( uris_has( $this->core['service'] ) ) {

                require_once 'system/service.php';
                new rozard_system_service();
            }

            
            if ( uris_has( $this->core['manages'] ) && ! uri_has( 'site-' ) ) {

                require_once 'system/manage.php';
                new rozard_system_manage;
            }


            if ( uris_has( $this->core['setting'] ) && ! uri_has( 'site-' ) )  {

                require_once 'system/settings.php';
                new rozard_system_setting;
            }

            
            if ( uris_has( $this->core['wizards'] )  ) {

                require_once 'system/wizards.php';
                new rozard_system_wizards;
            }


            require_once 'system/module.php';
            new rozard_system_module;
        }



    /** SERVIS */


        public function servis_menu( $order ) {

            if ( is_network_admin() ) {
                return $order;
            }
           
            global $menu, $submenu, $pagenow;


            // redirect
            if ( $pagenow === 'index.php' && ! uri_has( 'node=' ) ) {
                wp_safe_redirect( admin_url( 'index.php?node=dashboard' ) );
            }


            // cockpit
            $menu[2][0] = 'Cockpit';
            $menu[2][2] = admin_url( 'index.php?node=dashboard' );
            $menu[2][6] = 'dashicons-share-alt';


            // single
            $menu[5][0] = 'Website';
            $menu[5][2] = admin_url( 'edit.php?post_type=post&node=website' );
            $menu[5][6] = 'dashicons-insert';


            // manage
            $menu[60][0] = 'Feeders';
            $menu[60][2] = admin_url( 'edit-comments.php?node=feedback' );
            $menu[60][6] = 'dashicons-screenoptions';

        
            
            // manage
            $menu[70][0] = 'Manage';
            $menu[70][2] = admin_url( 'users.php?node=users' );
            $menu[70][6] = 'dashicons-art';

    
            $menu[80][0] = 'Settings';
            $menu[80][2] = admin_url( 'options-general.php?node=core' );
            $menu[80][6] = 'dashicons-layout';

            
            
            unset( $menu[4] );
            unset( $menu[10] );
            unset( $menu[20] );
            unset( $menu[25] );
            unset( $menu[59] );
            unset( $menu[65] );
            unset( $menu[75] );
            unset( $menu[80] );
            unset( $menu[99] );
                
          
            return $order;
        }


        public function servis_init() {


            if ( is_network_admin() ) {
                return;
            }


            if ( uris_has( $this->node['cockpit'] ) && ! is_network_admin() ) {

                require_once 'service/cockpits.php';
                new rozard_nodest_cockpit();
            }


            if ( uris_has( $this->node['feeders'] ) && ! is_network_admin() ) {

                require_once 'service/feeders.php';
                new rozard_service_feeders();
            }

            
            if ( uris_has( $this->node['manages'] ) && ! is_network_admin() ) {

                require_once 'service/manage.php';
                new rozard_service_manage();
            }


            if ( uris_has( $this->node['formats'] ) && ! is_network_admin() ) {

                require_once 'service/formats.php';
                new rozard_service_formats;
            }
        }



    /** THEMES */

        public function themes_init() {
            require_once  'themes/loader.php'; 
            new rozard_theme_loader;
        }
    }
}