<?php

class autocompleteController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {

        // Проверить права доступа

        $success = true;
        $error = false;
        $message = '';
        $redirect = false;
        $data = [];

        $helpers_methods = Load::model( 'Helpers' );

        if ( $request->getHeaderLine( 'X-Requested-With' ) === 'XMLHttpRequest' ) {

            $request = $request->getParsedBody();

            if ( isset( $request[ 'type' ] ) && !empty( $request[ 'type' ] ) ) {

                switch ( $request[ 'type' ] ):

                case 'countries_phones':

                if ( isset( $request[ 'filter' ] ) && !empty( $request[ 'filter' ] ) ) {

                    $countries_model = Load::model( 'Countries' );
                    $countries_phones = $countries_model->getCountriesPhones( $database, $request[ 'filter' ] );

                    foreach ( $countries_phones as $country_phone ) {

                        $data[] = [
                            'country_id' => $country_phone[ 'country_id' ],
                            'mask' => $country_phone[ 'mask' ],
                            'name' => $country_phone[ 'name' ],
                            'prefix' => explode( ' ', $country_phone[ 'mask' ] )[ 0 ],
                            'icon' => $helpers_methods->getImage( $country_phone[ 'icon' ] ),
                        ];

                    }
                }

                break;

                case 'owners_filter':

                $time_methods = Load::model( 'Timer' );

                if ( isset( $request[ 'filter' ] ) && !empty( $request[ 'filter' ] ) &&  isset( $request[ 'item' ] ) && !empty( $request[ 'item' ] ) ) {

                    $cargo_owners_model = Load::model( 'CargoOwners' );

                    if ( $request[ 'item' ] !== 'registration_date' && $request[ 'item' ] !== 'last_visit_date' && $request[ 'item' ] !== 'verification_request_date' ) {
                        $filter =  $request[ 'filter' ];
                        $filters = [
                            'AND' => [
                                'verified' => 0,
                                $request[ 'item' ] . '[~]' =>  $filter . '%'

                            ],

                        ];
                    } else {
                        $start_time = strtotime( str_replace( '.', '-', $request[ 'filter' ] ) );
                        $end_time = $start_time + 86400;
                        $filters = [
                            'AND' => [
                                'verified' => 0,
                                $request[ 'item' ] . '[<>]'  => [ $start_time, $end_time ]

                            ],
                        ];
                    }

                    $cargo_owners = $cargo_owners_model->getOwners( $database, $filters );

                    foreach ( $cargo_owners as $x=>$cargo_owner ) {
                        if ( $request[ 'item' ] !== 'registration_date' && $request[ 'item' ] !== 'last_visit_date' && $request[ 'item' ] !== 'verification_request_date' ) {
                            $data[] = [
                                'id' => time() . $x,
                                $request[ 'item' ] => $cargo_owner[ $request[ 'item' ] ]
                            ];
                        } else {
                            $data[ $time_methods->getTime( $cargo_owner[ $request[ 'item' ] ] ) ] = [
                                'id' => time() . $x,
                                $request[ 'item' ] => $time_methods->getTime( $cargo_owner[ $request[ 'item' ] ] )
                            ];
                        }
                    }

                }

                break;

                case 'loading_station':
                case 'unloading_station':
                case 'etsng':
                case 'gng':
                case 'related':

                if ( isset( $request[ 'filter' ] ) && !empty( $request[ 'filter' ] ) ) {

                    $api_model = Load::model( 'API' );

                    if ( $request[ 'type' ] == 'etsng' ) {
                        $data = $api_model->getLoadingETSNG( $request[ 'filter' ] );
                    } else if ( $request[ 'type' ] == 'gng' ) {
                        $data = $api_model->getLoadingGNG( $request[ 'filter' ] );
                    } else if ( $request[ 'type' ] == 'related' ) {
                        $data = $api_model->getRelated( $request[ 'filter' ] );
                    } else {
                        $data = $api_model->getLoadingStation( $request[ 'filter' ] );
                    }

                    if ( $data[ 'success' ] && $data[ 'http_code' ] == 200 &&  ( ( isset( $data[ 'response' ][ 'SearchInRefResult' ] ) && is_array( $data[ 'response' ][ 'SearchInRefResult' ] ) && count( $data[ 'response' ][ 'SearchInRefResult' ] ) > 0 )  || ( isset( $data[ 'response' ][ 'GetRelatedFreightsResult' ] ) && is_array( $data[ 'response' ][ 'GetRelatedFreightsResult' ] ) && count( $data[ 'response' ][ 'GetRelatedFreightsResult' ] ) > 0 ) ) ) {

                        if ( isset( $data[ 'response' ][ 'SearchInRefResult' ] ) ) {
                            $_data = $data[ 'response' ][ 'SearchInRefResult' ];
                        } else {
                            $_data = $data[ 'response' ][ 'GetRelatedFreightsResult' ];
                        }

                        foreach ( $_data as $station ) {

                            $road = [];
                            $name_and_road = [];
                            $country = '';
                            $department = '';
                            $railway = '';

                            if ( isset( $station[ 'Name' ] ) && !empty( $station[ 'Name' ] ) ) {

                                $name_and_road[] = $station[ 'Name' ];
                            }

                            if ( isset( $station[ 'Road' ] ) && !empty( $station[ 'Road' ] ) ) {
                                $road[] = $station[ 'Road' ];
                                $name_and_road[] = $station[ 'Road' ];
                                $railway = $station[ 'Road' ];
                            }

                            if ( isset( $station[ 'DP' ] ) && !empty( $station[ 'DP' ] ) ) {
                                $road[] = $station[ 'DP' ];
                                $name_and_road[] = $station[ 'DP' ];
                                $department = $station[ 'DP' ];
                            }

                            if ( isset( $station[ 'LandName' ] ) && !empty( $station[ 'LandName' ] ) ) {
                                $road[] = $station[ 'LandName' ];
                                $name_and_road[] = $station[ 'LandName' ];
                                $country = $station[ 'LandName' ];
                            }

                            $data[] = [
                                'station_id' => $station[ 'Code' ],
                                'name' => $station[ 'Name' ],
                                'name_and_road' =>  join( ', ', $name_and_road ),
                                'road' => join( ' - ', $road ),
                                'country' => $country,
                                'department' => $department,
                                'railway' => $railway

                            ];

                        }

                    } else {

                        $data = false;

                    }

                }

                break;

                endswitch;
            }

            if ( isset( $data[ 'query' ] ) ) {
                unset( $data[ 'query' ] );
            }

            if ( isset( $data[ 'success' ] ) ) {
                unset( $data[ 'success' ] );
            }

            if ( isset( $data[ 'message' ] ) ) {
                unset( $data[ 'message' ] );
            }

            if ( isset( $data[ 'http_code' ] ) ) {
                unset( $data[ 'http_code' ] );
            }

            if ( isset( $data[ 'error' ] ) ) {
                unset( $data[ 'error' ] );
            }

            if ( isset( $data[ 'response' ] ) ) {
                unset( $data[ 'response' ] );
            }

            $this->data = [
                'success'   => $success,
                'error'     => $error,
                'message'   => $message,
                'redirect'  => $redirect,
                'data'  => $data,
            ];
        }
    }
}