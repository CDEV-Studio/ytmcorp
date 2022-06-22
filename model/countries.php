<?php

class Countries
 {

    function getCountries( $database ) {

        return $database->select( 'countries_residence', '*' );
    }

    function getCountriesPhones( $database, $filter = false, $order = [ "country_id" => "ASC" ] ) {

        if ( !$filter ) {
            return $database->select( 'countries_phones', '*', [ 'ORDER' => $order ] );
        } else {
            return $database->select( 'countries_phones', '*', [ 'name[~]' => $filter . '%', 'ORDER' => $order ] );
        }
    }

    function deleteCountryPhone( $database, $country_id ) {

        $database->delete( 'countries_phones',  [ 'country_id' => $country_id ] );
    }

    function updateCountryPhone( $database, $column, $data, $where ) {

        $database->update( 'countries_phones', [ $column => $data ], $where );

    }

    function addCountryPhone( $database, $data ) {

        $database->insert( 'countries_phones', $data );
        return  $database->id();

    }

}