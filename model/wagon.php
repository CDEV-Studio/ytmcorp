<?php

class Wagon
 {

    function addWagon( $database, $data ) {

        $database->insert( 'railway_carriage', $data );

    }

}