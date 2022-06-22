<?php

class Session {
    private $data = array();

    public function __construct() {

        if ( !session_id() ) {
            /*
            ini_set( 'session.cookie_httponly', true );
            ini_set( 'session.use_only_cookies', true );
            ini_set( 'session.use_trans_sid', 'Off' );

            ini_set( 'session.cookie_secure', true );

            session_set_cookie_params( 2592000, '/', '.cargo.ytm.kz', true );

            session_cache_limiter ( false );
            */
            session_start();
        }

        $this->data = &$_SESSION;
    }

    public function __get( $key ) {
        return $this->data[ $key ];
    }

    public function __set( $key, $value ) {
        $this->data[ $key ] = $value;
    }

    public function get( $key ) {

        if ( isset( $this->data[ $key ] ) ) {
            return $this->data[ $key ];
        } else {
            return false;
        }

    }

    public function set( $key, $value ) {
        $this->data[ $key ] = $value;
    }

    function get_id() {
        return session_id();
    }

    function delete() {
        $this->data = array();

    }

    public function get_access() {
        if ( isset( $this->data[ 'session_id' ] ) && !empty( $this->data[ 'session_id' ] ) ) {
            return array (

                'session_id' => $this->data[ 'session_id' ],

            );
        } else {
            return array (

                'session_id' => 0

            );

        }
    }
}

