<?php

class Coefficients
 {

    function getCoefficients( $database ) {

        return $database->select( 'tariff_for_countries', '*' );

    }

    function setCoefficient( $database, $percent, $tariff_id ) {

        $database->update( 'tariff_for_countries', [ 'percent' => $percent ], [ 'tariff_id' => $tariff_id ] );

    }

}