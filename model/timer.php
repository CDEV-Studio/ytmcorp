<?php

class Timer
 {

    public function getTime( $timestamp ) {

        if ( $timestamp > 0 ) {
            return date( 'd.m.Y', $timestamp );
        } else {
            return '';
        }

    }

}