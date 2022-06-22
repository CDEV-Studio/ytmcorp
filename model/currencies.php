<?php

class Currencies
 {

    function getCurrencies( $database, $add_percent = true ) {

        $data = [];

        $currencies = $database->select( 'currencies', '*', [ 'ORDER' => [ 'currency_id' => 'ASC' ] ] );
        $coefficient_for_currencies = $database->select( 'settings', 'coefficient_for_currencies', [ 'setting_id' => 1 ] );

        foreach ( $currencies as $c ) {

            if($add_percent && $c[ 'code' ] !== 'KZT')  {
                $data[ $c[ 'code' ] ] = $c[ 'value' ] + ($c[ 'value' ] * ( $coefficient_for_currencies[ 0 ]/100 ));
            } else {
                $data[ $c[ 'code' ] ] = $c[ 'value' ];
            }

        }

        return $data;

    }

    function getPercent( $database ) {

        return $database->select( 'settings', 'coefficient_for_currencies', [ 'setting_id' => 1 ] )[ 0 ];

    }

    function setPercent( $database, $percent ) {

        $database->update( 'settings', [ 'coefficient_for_currencies' => $percent ], [ 'setting_id' => 1 ] );

    }

    function getCurrenciesFromAPI( $database ) {

        $_currencies = [

            'KZT',
            'USD',
            'EUR',
            'BYN',
            'RUB',
            'TJS',
            'UZS',
            'TMT',
            'AMD',
            'CHF',
            'KGS',
            'AZN',
            'MDL',
            'GEL'

        ];

        $currencies = file_get_contents( 'https://ytm.kz/calculator/?courses=json' );
        $currencies = json_decode( $currencies );

        foreach ( $currencies as $code=>$value ) {

            if ( in_array( $code, $_currencies ) && $database->has( 'currencies', [ 'code' => $code ] ) ) {

                $database->update( 'currencies', [ 'value' => $value ], [ 'code' => $code ] );

            } else if ( in_array( $code, $_currencies ) ) {

                $database->insert( 'currencies', [ 'value' => $value, 'code' => $code ] );

            }

        }

    }

}