<?php

class Calculation
 {

    private $data = [];

    function saveCalculation( $database, $data ) {

        $database->insert( 'calculation_result', $data );
        return $database->id();

    }

    function updateCalculation( $database, $data, $calculation_id ) {

        $database->update( 'calculation_result', $data, [ 'calculation_id' => $calculation_id ] );

    }

    function getCalculation( $database, $where = [] ) {

        return $database->select( 'calculation_result',  [ '[>]cargo_owners' => 'owner_id', '[>]railway_carriage' => 'railway_carriage_id' ], '*', $where );

    }

    function getCalculations( $database,  $where = [] ) {

        return $database->select( 'calculation_result', [ '[>]cargo_owners' => 'owner_id', '[>]railway_carriage' => 'railway_carriage_id' ], '*', $where );

    }

    function addCalculationCountries( $database, $data ) {

        $database->insert( 'calculation_result_countries', $data );

    }

    function getCalculationCountries( $database, $where = [] ) {

        return $database->select( 'calculation_result_countries', '*', $where );

    }

    function getTariffForCountries( $database, $where = [] ) {

        return  $database->select( 'tariff_for_countries', '*', $where );

    }

    function getCalculationFromMatrix( $database, $calculation ) {

        $data = [];

        $railway_carriage = [
            '1' => 'covered',
            '2' => 'gondola'
        ];

        $railway_carriage = $railway_carriage[ $calculation[ 'railway_carriage_id' ] ];

        $direction = false;

        if ( $calculation[ 'FSSourceLandName' ] == 'Казахстан' &&  $calculation[ 'TSSourceLandName' ] !== 'Казахстан' ) {

            $direction = 'Экспорт';

        } else if ( $calculation[ 'FSSourceLandName' ] !== 'Казахстан' &&  $calculation[ 'TSSourceLandName' ] == 'Казахстан' ) {

            $direction = 'Импорт';

        } else if ( in_array( $calculation[ 'FSSourceLandName' ], [ 'Россия', 'Беларусь', 'Украина', 'Молдова', 'Литва', 'Латвия', 'Эстония', 'Азербайджан', 'Армения', 'Грузия' ] ) &&  in_array( $calculation[ 'FSSourceLandName' ], [ 'Узбекистан', 'Киргизия', 'Туркмения', 'Таджикистан', 'Иран', 'Афганистан' ] ) ) {

            $direction = 'Транзит вниз';

        } else if ( in_array( $calculation[ 'FSSourceLandName' ], [ 'Узбекистан', 'Киргизия', 'Туркмения', 'Таджикистан', 'Иран', 'Афганистан' ] ) &&  in_array( $calculation[ 'TSSourceLandName' ], [ 'Россия', 'Беларусь', 'Украина', 'Молдова', 'Литва', 'Латвия', 'Эстония', 'Азербайджан', 'Армения', 'Грузия' ] ) ) {

            $direction = 'Транзит вверх';

        }

        if ( $direction ) {

            $result = $database->select( 'matrix_' . $railway_carriage, '*', [

                'LIMIT' => 1,
                'AND' => [
                    'land_in' =>  $calculation[ 'loading_station_country' ],
                    'road_in' =>  $calculation[ 'loading_station_railway' ],
                    'dp_in' =>  $calculation[ 'loading_station_department' ],
                    'land_out' =>  $calculation[ 'unloading_station_country' ],
                    'road_out' =>  $calculation[ 'unloading_station_railway' ],
                    'dp_out' =>  $calculation[ 'unloading_station_department' ],
                    'type' => $direction
                ]

            ] );

            if ( !$result ) {
                $result = $database->select( 'matrix_' . $railway_carriage, '*', [

                    'LIMIT' => 1,
                    'AND' => [
                        'land_in' =>  $calculation[ 'loading_station_country' ],
                        'road_in' =>  $calculation[ 'loading_station_railway' ],

                        'land_out' =>  $calculation[ 'unloading_station_country' ],
                        'road_out' =>  $calculation[ 'unloading_station_railway' ],
                        'dp_out' =>  $calculation[ 'unloading_station_department' ],
                        'type' => $direction
                    ]

                ] );
            }

            if ( !$result ) {
                $result = $database->select( 'matrix_' . $railway_carriage, '*', [

                    'LIMIT' => 1,
                    'AND' => [
                        'land_in' =>  $calculation[ 'loading_station_country' ],
                        'road_in' =>  $calculation[ 'loading_station_railway' ],
                        'dp_in' =>  $calculation[ 'loading_station_department' ],
                        'land_out' =>  $calculation[ 'unloading_station_country' ],
                        'road_out' =>  $calculation[ 'unloading_station_railway' ],

                        'type' => $direction
                    ]

                ] );
            }

            if ( !$result ) {
                $result = $database->select( 'matrix_' . $railway_carriage, '*', [

                    'LIMIT' => 1,
                    'AND' => [
                        'land_in' =>  $calculation[ 'loading_station_country' ],
                        'road_in' =>  $calculation[ 'loading_station_railway' ],

                        'land_out' =>  $calculation[ 'unloading_station_country' ],
                        'road_out' =>  $calculation[ 'unloading_station_railway' ],

                        'type' => $direction
                    ]

                ] );
            }

            if ( !$result ) {
                $result = $database->select( 'matrix_' . $railway_carriage, '*', [

                    'LIMIT' => 1,
                    'AND' => [
                        'land_in' =>  $calculation[ 'loading_station_country' ],
                        'road_in' =>  $calculation[ 'loading_station_railway' ],

                        'land_out' =>  $calculation[ 'unloading_station_country' ],

                        'type' => $direction
                    ]

                ] );
            }

            if ( !$result ) {
                $result = $database->select( 'matrix_' . $railway_carriage, '*', [

                    'LIMIT' => 1,
                    'AND' => [
                        'land_in' =>  $calculation[ 'loading_station_country' ],

                        'land_out' =>  $calculation[ 'unloading_station_country' ],
                        'road_out' =>  $calculation[ 'unloading_station_railway' ],

                        'type' => $direction
                    ]

                ] );
            }

            if ( !$result ) {
                $result = $database->select( 'matrix_' . $railway_carriage, '*', [

                    'LIMIT' => 1,
                    'AND' => [
                        'land_in' =>  $calculation[ 'loading_station_country' ],

                        'land_out' =>  $calculation[ 'unloading_station_country' ],

                        'type' => $direction
                    ]

                ] );
            }

            if ( $result ) {

                $data = [
                    'supplier_tax' => $result[ 0 ][ 'days_price_coeff' ],
                    'with_marge' => $result[ 0 ][ 'days_marge' ],
                ];

            }

        } else if ( $calculation[ 'FSSourceLandName' ] == 'Казахстан' &&  $calculation[ 'TSSourceLandName' ] == 'Казахстан' ) {

            $result = $database->select( 'matrix_' . $railway_carriage . '_kz', '*', [

                'LIMIT' => 1,
                'AND' => [
                    'from[>]' =>  $calculation[ 'Distance' ],
                    'to[<=]' =>  $calculation[ 'Distance' ],
                    'off_single[~]' => $calculation[ 'etsng_id' ]

                ]

            ] );

            if ( $result ) {

                $result = $database->select( 'matrix_' . $railway_carriage . '_kz', '*', [

                    'LIMIT' => 1,
                    'AND' => [
                        'from[>]' =>  $calculation[ 'Distance' ],
                        'to[<=]' =>  $calculation[ 'Distance' ],
                        'name' => 'Остальные грузы'

                    ]

                ] );

                $data = [
                    'price_marge' => $result[ 0 ][ 'price_marge' ]
                ];

            } else {

                $result = $database->select( 'matrix_' . $railway_carriage . '_kz', '*', [

                    'LIMIT' => 1,
                    'AND' => [
                        'from[>]' =>  $calculation[ 'Distance' ],
                        'to[<=]' =>  $calculation[ 'Distance' ],
                        'on_single[~]' => $calculation[ 'etsng_id' ]

                    ]

                ] );

                if ( $result ) {

                    $data = [
                        'price_marge' => $result[ 0 ][ 'price_marge' ]
                    ];

                } else {

                    $etsng = substr( strval( $calculation[ 'etsng_id' ] ), 0, 3 );
                    $result = $database->select( 'matrix_' . $railway_carriage . '_kz', '*', [

                        'LIMIT' => 1,
                        'AND' => [
                            'from[>]' =>  $calculation[ 'Distance' ],
                            'to[<=]' =>  $calculation[ 'Distance' ],
                            'on_group[~]' => $etsng

                        ]

                    ] );

                    if ( $result ) {

                        $data = [
                            'price_marge' => $result[ 0 ][ 'price_marge' ]
                        ];

                    } else {

                        $result = $database->select( 'matrix_' . $railway_carriage . '_kz', '*', [

                            'LIMIT' => 1,
                            'AND' => [
                                'from[>]' =>  $calculation[ 'Distance' ],
                                'to[<=]' =>  $calculation[ 'Distance' ],
                                'name' => 'Остальные грузы'

                            ]

                        ] );

                        if ( $result ) {

                            $data = [
                                'price_marge' => $result[ 0 ][ 'price_marge' ]
                            ];

                        }

                    }

                }

            }

        }

        return $data;

    }

    function tariffCalculation( $database, $calculation, $calculation_id ) {

        $helpers_methods = Load::model( 'Helpers' );

        $loaded_rate = 0;
        $loaded_rate_countries = [];
        $_loaded_rate_countries = [];
        $wagon_bid = 0;

        $this->data[ 'calculation' ] = $calculation[ 0 ];

        $currencies_model = Load::model( 'Currencies' );
        $currencies = $currencies_model->getCurrencies( $database );

        $this->data[ 'RUB' ] = $currencies[ 'RUB' ];
        $this->data[ 'currencies' ] = $currencies;

        $tariff_for_countries = [];
        $_tariff_for_countries = $this->getTariffForCountries( $database );

        foreach ( $_tariff_for_countries as $item ) {
            $tariff_for_countries[ $item[ 'country_name' ] ] = $item[ 'percent' ];
        }

        $countries  = $this->getCalculationCountries( $database,   [ 'calculation_id' => ( int )$calculation_id, 'ORDER' => [ 'sort_order' => 'ASC' ] ] );

        foreach ( $countries as $item ) {

            $loaded_rate_countries[ $item[ 'Name' ] ] = $helpers_methods->format( ( $item[ 'TotalPriceWoNDS' ] + ( $item[ 'TotalPriceWoNDS' ] * ( $tariff_for_countries[ $item[ 'Name' ] ]/100 ) ) ) * $currencies[ $item[ 'ABBR' ] ] );
            $_loaded_rate_countries[ $item[ 'Name' ] ] =  ( $item[ 'TotalPriceWoNDS' ] + ( $item[ 'TotalPriceWoNDS' ] * ( $tariff_for_countries[ $item[ 'Name' ] ]/100 ) ) ) * $currencies[ $item[ 'ABBR' ] ] ;
            $loaded_rate = $loaded_rate + ( ( $item[ 'TotalPriceWoNDS' ] + ( $item[ 'TotalPriceWoNDS' ] * ( $tariff_for_countries[ $item[ 'Name' ] ]/100 ) ) ) * $currencies[ $item[ 'ABBR' ] ] );

            $this->data[ 'countries' ][] = [

                'Name' => $item[ 'Name' ],
                'ABBR' => $item[ 'ABBR' ],
                'TotalPriceWoNDS' => $helpers_methods->format( $item[ 'TotalPriceWoNDS' ] ),
                'Distance' => $item[ 'Distance' ],
                'NDS' => $helpers_methods->format( $item[ 'NDS' ] ),
                'TotalPrice' => $helpers_methods->format( $item[ 'TotalPrice' ] ),
                'AddDues' => $item[ 'AddDues' ],
                'tariff_for_countries' => $tariff_for_countries[ $item[ 'Name' ] ],
                'sum' => $helpers_methods->format( $item[ 'TotalPriceWoNDS' ] * ( $tariff_for_countries[ $item[ 'Name' ] ]/100 ) ),
                'sum_2' => $helpers_methods->format( $item[ 'TotalPriceWoNDS' ] + ( $item[ 'TotalPriceWoNDS' ] * ( $tariff_for_countries[ $item[ 'Name' ] ]/100 ) ) ),
                'exchange' => isset( $currencies[ $item[ 'ABBR' ] ] ) ? $currencies[ $item[ 'ABBR' ] ] : '',
                'sum_3' => $helpers_methods->format( ( $item[ 'TotalPriceWoNDS' ] + ( $item[ 'TotalPriceWoNDS' ] * ( $tariff_for_countries[ $item[ 'Name' ] ]/100 ) ) ) * $currencies[ $item[ 'ABBR' ] ] ),

            ];
        }

        if ( $calculation[ 0 ][ 'loading_station_country' ] == $calculation[ 0 ][ 'unloading_station_country' ] ) {
            $this->data[ 'direction' ] = 'Внутренняя перевозка';
        } else {
            $this->data[ 'direction' ] = 'Международное';
        }

        $calculation_from_matrix = $this->getCalculationFromMatrix( $database, $calculation[ 0 ] );

        if ( isset( $calculation_from_matrix[ 'price_marge' ] ) ) {

            ///////////////////////////////
            $this->data[ 'day_bid_from_the_supplier_RUR' ] = 0;
            $this->data[ 'day_bid_marge_RUR' ] = 0;
            $this->data[ 'bid_marge_RUR' ] = (( $calculation_from_matrix[ 'price_marge' ] / 100 ) * 112) * $this->data[ 'RUB' ];
            ///////////////////////////////

            $this->data[ 'day_bid_from_the_supplier_KZT' ] = 0;
            $this->data[ 'day_bid_marge_KZT' ] = 0;
            $this->data[ 'bid_marge_KZT' ] = ( $calculation_from_matrix[ 'price_marge' ] / 100 ) * 112;
            $wagon_bid = ( $calculation_from_matrix[ 'price_marge' ] / 100 ) * 112;

        } else if ( isset( $calculation_from_matrix[ 'supplier_tax' ] ) && isset( $calculation_from_matrix[ 'with_marge' ] ) ) {

            $this->data[ 'day_bid_from_the_supplier_RUR' ] = $helpers_methods->format( $calculation_from_matrix[ 'supplier_tax' ] );
            $this->data[ 'day_bid_marge_RUR' ] = $helpers_methods->format( $calculation_from_matrix[ 'with_marge' ] );
            $this->data[ 'bid_marge_RUR' ] = $helpers_methods->format( $calculation_from_matrix[ 'with_marge' ] * $calculation[ 0 ][ 'PeriodOfDelivery' ] );

            $this->data[ 'day_bid_from_the_supplier_KZT' ] = $helpers_methods->format( $calculation_from_matrix[ 'supplier_tax' ] * $this->data[ 'RUB' ] );
            $this->data[ 'day_bid_marge_KZT' ] = $helpers_methods->format( $calculation_from_matrix[ 'with_marge' ] * $this->data[ 'RUB' ] );
            $this->data[ 'bid_marge_KZT' ] = $helpers_methods->format( $calculation_from_matrix[ 'with_marge' ] * $calculation[ 0 ][ 'PeriodOfDelivery' ] * $this->data[ 'RUB' ] );
            $wagon_bid = $calculation_from_matrix[ 'with_marge' ] * $calculation[ 0 ][ 'PeriodOfDelivery' ] * $this->data[ 'RUB' ];
        }

        $this->data[ 'loaded_rate' ] = $helpers_methods->format( $loaded_rate );
        $this->data[ 'loaded_rate_countries' ] = $loaded_rate_countries;
        $this->data[ 'wagon_bid' ] = $helpers_methods->format( $wagon_bid );
        $this->data[ 'total' ] = $helpers_methods->format( $loaded_rate + $wagon_bid );

        $exchange = [];

        foreach ( $currencies as $code=>$value ) {

            $countries = [];

            foreach ( $_loaded_rate_countries as $country=>$country_value ) {

                $countries[] = [
                    'name' => $country,
                    'value' => $helpers_methods->format( $country_value / $value )
                ];

            }

            $exchange[ $code ] = [
                'loaded_rate' => $helpers_methods->format( $loaded_rate / $value ),
                'countries' => $countries,
                'wagon_bid' => $helpers_methods->format( $wagon_bid / $value ),
                'total' => $helpers_methods->format( ( $loaded_rate + $wagon_bid ) / $value )
            ];

        }

        $this->data[ 'exchange' ] = json_encode( $exchange );

        return $this->data;

    }
}