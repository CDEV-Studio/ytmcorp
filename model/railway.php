<?php

class Railway
 {

    function getRailwayCarriage( $database, $where ) {

        return $database->select( 'railway_carriage', '*', $where );

    }
}