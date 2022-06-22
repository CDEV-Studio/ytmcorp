<?php

class Images
 {

    function getImageLink( $image ) {

        if ( !empty( $image ) ) {

            return '/data/img/' . $image;

        } else {

            return false;
            
        }

    }

}