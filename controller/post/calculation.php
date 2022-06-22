<?php

class calculationController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {

        $success = true;
        $error = false;
        $message = '';
        $redirect = false;

        $helpers_methods = Load::model( 'Helpers' );

        if ( $request->getHeaderLine( 'X-Requested-With' ) === 'XMLHttpRequest' ) {

            $request = $request->getParsedBody();

            if ( isset( $request[ 'type' ] ) && !empty( $request[ 'type' ] ) ) {

                switch ( $request[ 'type' ] ):

                case 'new':

                if (
                    isset( $request[ 'loading_station_name' ] ) && !empty( $request[ 'loading_station_name' ] ) &&
                    isset( $request[ 'loading_station_id' ] ) && !empty( $request[ 'loading_station_id' ] ) && // ( int )$request[ 'loading_station_id' ] > 0 &&
                    isset( $request[ 'loading_station_country' ] ) && !empty( $request[ 'loading_station_country' ] ) &&
                    isset( $request[ 'loading_station_railway' ] ) && !empty( $request[ 'loading_station_railway' ] ) &&
                    isset( $request[ 'loading_station_department' ] )  && //!empty( $request[ 'loading_station_department' ] ) &&
                    isset( $request[ 'unloading_station_name' ] ) && !empty( $request[ 'unloading_station_name' ] ) &&
                    isset( $request[ 'unloading_station_id' ] ) && !empty( $request[ 'unloading_station_id' ] ) &&  //  ( int )$request[ 'unloading_station_id' ] > 0 &&
                    isset( $request[ 'unloading_station_country' ] ) && !empty( $request[ 'unloading_station_country' ] ) &&
                    isset( $request[ 'unloading_station_railway' ] ) && !empty( $request[ 'unloading_station_railway' ] ) &&
                    isset( $request[ 'unloading_station_department' ] ) && //!empty( $request[ 'unloading_station_department' ] ) &&
                    isset( $request[ 'etsng_id' ] ) && !empty( $request[ 'etsng_id' ] ) && //  ( int )$request[ 'etsng_id' ] > 0 &&
                    isset( $request[ 'etsng_name' ] ) && !empty( $request[ 'etsng_name' ] ) &&
                    isset( $request[ 'gng_id' ] ) && // !empty( $request[ 'gng_id' ] ) &&  //  ( int )$request[ 'gng_id' ] > 0 &&
                    // isset( $request[ 'gng_name' ] ) && !empty( $request[ 'gng_name' ] ) &&
                    isset( $request[ 'railway_carriage_id' ] ) && !empty( $request[ 'railway_carriage_id' ] ) &&  //  ( int )$request[ 'railway_carriage_id' ] > 0 &&
                    isset( $request[ 'weight' ] ) && !empty( $request[ 'weight' ] )  // &&    ( float )$request[ 'weight' ] > 0

                ) {

                    unset( $request[ 'type' ] );
                    $request[ 'owner_id' ] = 12;
                    /// $access[ 'session_id' ];
                    /////////////////////////////////
                    $request[ 'calculation_date' ] = time();
                    $calculation_model = Load::model( 'Calculation' );
                    $calculation_id = $calculation_model->saveCalculation( $database, $request );

                    if ( $calculation_id ) {

                        $railway_model = Load::model( 'Railway' );
                        $railway_carriage = $railway_model->getRailwayCarriage( $database, [ 'railway_carriage_id' => $request[ 'railway_carriage_id' ] ] );

                        if ( $railway_carriage ) {

                            $calculation_result = [];
                            $countries = [];

                            $api_model = Load::model( 'API' );
                            $data = $api_model->getCalculationFromAPI( $request[ 'loading_station_id' ], $request[ 'unloading_station_id' ],  $request[ 'etsng_id' ], $request[ 'gng_id' ], $request[ 'weight' ], $railway_carriage[ 0 ][ 'api_code' ] );

                            $calculation_result[ 'api_query' ] = $data [ 'query' ][ 'path' ];
                            $calculation_result[ 'api_response' ] = json_encode( $data[ 'response' ] );
                            $calculation_result[ 'api_http_code' ] = $data [ 'http_code' ];

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'Countries' ] ) && is_array( $data[ 'response' ][ 'CalcResult' ][ 'Countries' ] ) ) {

                                foreach ( $data[ 'response' ][ 'CalcResult' ][ 'Countries' ] as $x=>$country ) {

                                    $countries[ $x ] = $country;
                                    $countries[ $x ][ 'calculation_id' ] = $calculation_id;
                                    $countries[ $x ][ 'sort_order' ] = $x;

                                }

                            }

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'PeriodOfDelivery' ] ) ) {
                                $calculation_result[ 'PeriodOfDelivery' ] = $data[ 'response' ][ 'CalcResult' ][ 'PeriodOfDelivery' ];
                            }

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'Distance' ] ) ) {
                                $calculation_result[ 'Distance' ] = $data[ 'response' ][ 'CalcResult' ][ 'Distance' ];
                            }

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'FSSourceLandName' ] ) ) {
                                $calculation_result[ 'FSSourceLandName' ] = $data[ 'response' ][ 'CalcResult' ][ 'FSSourceLandName' ];
                            }

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'FSSourceLand' ] ) ) {
                                $calculation_result[ 'FSSourceLand' ] = $data[ 'response' ][ 'CalcResult' ][ 'FSSourceLand' ];
                            }

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'FSLandName' ] ) ) {
                                $calculation_result[ 'FSLandName' ] = $data[ 'response' ][ 'CalcResult' ][ 'FSLandName' ];
                            }

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'TSSourceLandName' ] ) ) {
                                $calculation_result[ 'TSSourceLandName' ] = $data[ 'response' ][ 'CalcResult' ][ 'TSSourceLandName' ];
                            }

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'TSSourceLand' ] ) ) {
                                $calculation_result[ 'TSSourceLand' ] = $data[ 'response' ][ 'CalcResult' ][ 'TSSourceLand' ];
                            }

                            if ( isset( $data[ 'response' ][ 'CalcResult' ][ 'TSLandName' ] ) ) {
                                $calculation_result[ 'TSLandName' ] = $data[ 'response' ][ 'CalcResult' ][ 'TSLandName' ];
                            }

                            $calculation_model->updateCalculation( $database, $calculation_result, $calculation_id );
                            $calculation_model->addCalculationCountries( $database, $countries );

                            $redirect = '/calculation/result_' . $calculation_id;
                        }

                    } else {

                        $success = false;
                        $message = 'Ошибка базы данных!';
                        $error = true;

                    }

                } else {

                    $success = false;
                    $message = 'Заполните все поля!';
                    $error = true;

                }

                break;

                endswitch;

                $this->data = [
                    'success'   => $success,
                    'error'     => $error,
                    'message'   => $message,
                    'redirect'  => $redirect,
                ];

            }

        }
    }
}