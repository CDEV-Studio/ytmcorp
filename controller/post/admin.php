<?php
use Shuchkin\SimpleXLSX;

class adminController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {

        $success = true;
        $error = false;
        $message = '';
        $redirect = false;

        $helpers_methods = Load::model( 'Helpers' );

        if ( $request->getHeaderLine( 'X-Requested-With' ) === 'XMLHttpRequest' ) {

            $uploadedFiles = $request->getUploadedFiles();
            $request = $request->getParsedBody();

            if ( isset( $request[ 'order' ] ) &&  !empty( $request[ 'order' ] ) && isset( $request[ 'page' ] ) &&  !empty( $request[ 'page' ] ) ) {

                $order = json_decode( $request[ 'order' ], true );

                if ( is_array( $order ) ) {

                    $cargo_owners_model = Load::model( 'CargoOwners' );
                    $time_methods = Load::model( 'Timer' );

                    $filters = [
                        'AND' => [
                            'verified' => 0,
                        ],
                        'ORDER' => $order
                    ];

                    if ( isset( $request[ 'owner_id' ] ) && is_array( $request[ 'owner_id' ] ) && count( $request[ 'owner_id' ] ) > 0 ) {
                        $filters[ 'AND' ][ 'owner_id' ] = $request[ 'owner_id' ];
                    }

                    if ( isset( $request[ 'registration_date' ] ) && is_array( $request[ 'registration_date' ] ) && count( $request[ 'registration_date' ] ) > 0 ) {

                        foreach ( $request[ 'registration_date' ] as $registration_date ) {
                            $start_time = strtotime( str_replace( '.', '-', $registration_date ) );

                            $end_time = $start_time + 86400;
                            $filters[ 'AND' ][ 'registration_date[<>]' ] = [ $start_time, $end_time ];

                        }

                    }

                    if ( isset( $request[ 'last_visit_date' ] ) && is_array( $request[ 'last_visit_date' ] ) && count( $request[ 'last_visit_date' ] ) > 0 ) {

                        foreach ( $request[ 'last_visit_date' ] as $last_visit_date ) {
                            $start_time = strtotime( str_replace( '.', '-', $last_visit_date ) );

                            $end_time = $start_time + 86400;
                            $filters[ 'AND' ][ 'last_visit_date[<>]' ] = [ $start_time, $end_time ];

                        }

                    }

                    if ( isset( $request[ 'email' ] ) && is_array( $request[ 'email' ] ) && count( $request[ 'email' ] ) > 0 ) {
                        $filters[ 'AND' ][ 'email' ] = $request[ 'email' ];
                    }

                    if ( isset( $request[ 'phone' ] ) && is_array( $request[ 'phone' ] ) && count( $request[ 'phone' ] ) > 0 ) {
                        $filters[ 'AND' ][ 'phone' ] = $request[ 'phone' ];
                    }

                    if ( isset( $request[ 'verification_request_date' ] ) && is_array( $request[ 'verification_request_date' ] ) && count( $request[ 'verification_request_date' ] ) > 0 ) {

                        foreach ( $request[ 'verification_request_date' ] as $verification_request_date ) {
                            $start_time = strtotime( str_replace( '.', '-', $verification_request_date ) );

                            $end_time = $start_time + 86400;
                            $filters[ 'AND' ][ 'last_visit_date[<>]' ] = [ $start_time, $end_time ];

                        }

                    }

                    $cargo_owners_count = $cargo_owners_model->getCountOwners( $database, $filters );

                    $this->data[ 'pages' ] = round( $cargo_owners_count / CARGO_OWNERS_LIMIT );
                    if ( !$this->data[ 'pages' ] ) {
                        $this->data[ 'pages' ] = 1;
                    }
                    $this->data[ 'this_page' ] = $request[ 'page' ];

                    $start = ( ( int )$request[ 'page' ] - 1 ) * CARGO_OWNERS_LIMIT;
                    $end = $start + CARGO_OWNERS_LIMIT;

                    $filters[ 'LIMIT' ] =  [ $start, $end ];

                    $cargo_owners = $cargo_owners_model->getOwners( $database, $filters );

                    foreach ( $cargo_owners as $cargo_owner ) {

                        $this->data[ 'cargo_owners' ][] = [
                            'owner_id' => $cargo_owner[ 'owner_id' ],
                            'registration_date' => $time_methods->getTime( $cargo_owner[ 'registration_date' ] ),
                            'last_visit_date' => $time_methods->getTime( $cargo_owner[ 'last_visit_date' ] ),
                            'email' => $cargo_owner[ 'email' ],
                            'phone' => $cargo_owner[ 'phone' ],
                            'verification_request_date' => $time_methods->getTime( $cargo_owner[ 'verification_request_date' ] ),
                            'rejection_reason' => $cargo_owner[ 'rejection_reason' ],
                        ];

                    }

                } else {

                    $success = false;
                    $message = 'Неверный запрос!';
                    $error = true;
                }

            } else if ( isset( $request[ 'type' ] ) &&  !empty( $request[ 'type' ] ) ) {

                $countries_model = Load::model( 'Countries' );

                if ( $request[ 'type' ] == 'delete' && isset( $request[ 'country_id' ] ) &&  !empty( $request[ 'country_id' ] ) ) {

                    $countries_model->deleteCountryPhone( $database, $request[ 'country_id' ] );

                } else if ( $request[ 'type' ] == 'save' && isset( $request[ 'json' ] ) &&  !empty( $request[ 'json' ] ) ) {

                    $array_from_json = json_decode( $request[ 'json' ], true );

                    if ( is_array( $array_from_json ) && isset( $request[ 'data' ] ) && isset( $array_from_json[ 'WHERE' ] ) && isset( $array_from_json[ 'COLUMN' ] ) && is_array( $array_from_json[ 'WHERE' ] ) && !empty( $array_from_json[ 'COLUMN' ] ) ) {

                        $countries_model->updateCountryPhone( $database, $array_from_json[ 'COLUMN' ], $request[ 'data' ],  $array_from_json[ 'WHERE' ] );

                    }

                } else if ( $request[ 'type' ] == 'new' && isset( $request[ 'data' ] ) &&  is_array( $request[ 'data' ] ) ) {

                    $this->data[ 'country_id' ] = $countries_model->addCountryPhone( $database, $request[ 'data' ] );

                } else if ( $request[ 'type' ] == 'image' && isset( $request[ 'country_id' ] )  && isset( $uploadedFiles[ 'file' ] ) && $uploadedFiles[ 'file' ]->getSize() && ( $uploadedFiles[ 'file' ]->getClientMediaType() == 'image/png' || $uploadedFiles[ 'file' ]->getClientMediaType() == 'image/jpg' || $uploadedFiles[ 'file' ]->getClientMediaType()  == 'image/jpeg' ) ) {

                    $images = Load::model( 'Images' );

                    $filename = $helpers_methods->moveUploadedFile( DIR_IMAGE . 'countries/', $uploadedFiles[ 'file' ], 50 );

                    if ( ( int )$request[ 'country_id' ] ) {
                        $countries_model->updateCountryPhone( $database, 'icon', 'countries/' . $filename,  [ 'country_id' => ( int )$request[ 'country_id' ] ] );
                    }

                    $this->data[ 'icon' ] =  $images->getImageLink( 'countries/' . $filename );
                    $this->data[ 'filename' ] =  'countries/' . $filename;
                    $this->data[ 'country_id' ] =  $request[ 'country_id' ];

                } else if ( $request[ 'type' ] == 'saveCargoOwnerInfo' && isset( $request[ 'owner_id' ] ) && ( int )$request[ 'owner_id' ]  && isset( $request[ 'data' ] ) &&  is_array( $request[ 'data' ] ) ) {

                    $update = true;

                    if ( !isset( $request[ 'data' ][ 'phone' ] ) || empty( $request[ 'data' ][ 'phone' ] ) ) {

                        $update = false;
                        $success = false;
                        $message = 'Неверный телефон!';
                        $error = true;

                    }

                    if ( $update ) {

                        $cargo_owners_model = Load::model( 'CargoOwners' );
                        $cargo_owners_model->editOwner( $database, $request[ 'data' ],  ( int )$request[ 'owner_id' ] );

                    }

                } else if ( $request[ 'type' ] == 'import' && isset( $request[ 'railway_carriage_id' ] ) &&  ( int )$request[ 'railway_carriage_id' ] > 0 && isset( $uploadedFiles[ 'file' ] ) && $uploadedFiles[ 'file' ]->getSize() && ( $uploadedFiles[ 'file' ]->getClientMediaType() == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $uploadedFiles[ 'file' ]->getClientMediaType() == 'application/vnd.ms-excel' ) ) {

                    set_time_limit( 0 );

                    $file = $helpers_methods->moveUploadedFile( DIR_EXEL, $uploadedFiles[ 'file' ], 0 );

                    $sheets = array(
                        'экспорт'			=> '1',
                        'импорт'				=> '2',
                        'транзит вниз'		=> '3',
                        'транзит вверх'	=> '4',
                        'казахстан'			=> '5'
                    );

                    $railway_carriages = [
                        '1' => 'matrix_covered',
                        '2' => 'matrix_gondola'
                    ];

                    $table_name = $railway_carriages[ $request[ 'railway_carriage_id' ] ];

                    $xlsx = SimpleXLSX::parse( DIR_EXEL . $file );

                    if ( isset( $request[ 'clean' ] ) && $request[ 'clean' ] == 'true' ) {
                        if ( $table_name == 'matrix_covered' ) {
                            $database->delete( 'matrix_covered', [] );
                            $database->delete( 'matrix_covered_kz', [] );
                        } else if ( $table_name == 'matrix_gondola' ) {
                            $database->delete( 'matrix_gondola', [] );
                            $database->delete( 'matrix_gondola_kz', [] );
                        }
                    }

                    foreach ( $xlsx->sheetNames() as $i => $sheetName ) {
                        if ( in_array( mb_strtolower( $sheetName ), array_keys( $sheets ) ) ) {
                            if ( mb_strtolower( $sheetName ) != 'казахстан' ) {
                                $route = [];
                                $error_upd = [];
                                $error_ins = [];
                                $sheet = $xlsx->rows( $i );
                                if ( mb_strpos( mb_strtolower( $sheet[ 11 ][ 3 ] ), 'станция погрузки' ) === 0 ) {
                                    foreach ( $sheet as $n => $res ) {
                                        if ( $n > 8 && $n < count( $sheet ) - 3 ) {
                                            foreach ( $sheet[ $n ] as $k => $row ) {
                                                $land_in		 = '';
                                                $road_in		 = '';
                                                $dp_in		 = '';
                                                $land_out	 = '';
                                                $road_out	 = '';
                                                $dp_out		 = '';
                                                if ( $k > 3 && ( ( $k+1 ) % 5 ) == 0 ) {
                                                    if ( $sheet[ $n + 3 ][ 3 ] && !empty( $sheet[ 6 ][ $k ] ) ) {
                                                        $current_coef				 = isset( $sheet[ 3 ][ $k + 1 ] ) && $sheet[ 3 ][ $k + 1 ] ? ( float )$sheet[ 3 ][ $k + 1 ] : $sheet[ 2 ][ 3 ];
                                                        $current_marge				 = isset( $sheet[ 5 ][ $k + 4 ] ) && $sheet[ 5 ][ $k + 4 ] ? ( float )$sheet[ 5 ][ $k + 4 ] : $sheet[ 4 ][ 3 ];
                                                        $sup_price_odd				 = @ceil( $sheet[ $n + 3 ][ $k ] * $current_coef );
                                                        $days_price_coeff			 = @ceil( $sup_price_odd / $sheet[ $n + 3 ][ $k + 2 ] );
                                                        $days_marge					 = @floor( $days_price_coeff * $current_marge );
                                                        if ( $sheet[ $n + 3 ][ 0 ] )	$land_in = $sheet[ $n + 3 ][ 0 ];
                                                        if ( $sheet[ $n + 3 ][ 1 ] )	$road_in = $sheet[ $n + 3 ][ 1 ];
                                                        if ( $sheet[ $n + 3 ][ 2 ] )	$dp_in = $sheet[ $n + 3 ][ 2 ];
                                                        if ( $sheet[ 6 ][ $k ] )			$land_out = $sheet[ 6 ][ $k ];
                                                        if ( $sheet[ 7 ][ $k ] )			$road_out = $sheet[ 7 ][ $k ];
                                                        if ( $sheet[ 8 ][ $k ] )			$dp_out = $sheet[ 8 ][ $k ];
                                                        $route[ $sheet[ $n + 3 ][ 3 ] ][] = array(
                                                            'station_in'			=> $sheet[ $n + 3 ][ 3 ],
                                                            'station_out'			=> $sheet[ 9 ][ $k ],
                                                            'price'					=> $sheet[ $n + 3 ][ $k ],
                                                            'price_sup'				=> $sup_price_odd,
                                                            'days'					=> $sheet[ $n + 3 ][ $k + 2 ],
                                                            'days_price_coeff'	=> $days_price_coeff,
                                                            'days_marge'			=> $days_marge,
                                                            'current_coef'			=> $current_coef,
                                                            'current_marge'		=> $current_marge,
                                                            'land_in'				=> $land_in,
                                                            'road_in'				=> $road_in,
                                                            'dp_in'					=> $dp_in,
                                                            'land_out'				=> $land_out,
                                                            'road_out'				=> $road_out,
                                                            'dp_out'					=> $dp_out,
                                                            'type'					=> $sheet[ 1 ][ 2 ]
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $ins = [];
                                    $upd = [];
                                    $err = '';
                                    foreach ( $route as $k => $row ) {
                                        foreach ( $row as $var => $val ) {

                                            $check =  $database->query( "SELECT * FROM `{$table_name}` WHERE `station_in` LIKE '%{$val["station_in"]}%' AND `station_out` LIKE '%{$val["station_out"]}%' LIMIT 1" )->fetchAll();

                                            if ( $check ) {
                                                $up = "UPDATE `{$table_name}` SET ";
                                                foreach ( $val as $k => $v ) {
                                                    $up .= "`{$k}`='{$v}',";
                                                }
                                                $upd[] = trim( $up, ',' )." WHERE `id`='{$check[0]["id"]}'";

                                            } else {
                                                $vr = '';
                                                foreach ( $val as $k => $v ) {
                                                    $vr .= "'{$v}',";
                                                }
                                                $vr = trim( $vr, ',' );
                                                $ins[] = "INSERT INTO `{$table_name}` (`".implode( '`,`', array_keys( $val ) )."`) VALUES({$vr})";
                                            }
                                        }
                                    }
                                    if ( !empty( $upd ) ) {
                                        foreach ( $upd as $q ) {
                                            $database->query( $q );

                                        }
                                    }
                                    if ( !empty( $ins ) ) {
                                        foreach ( $ins as $q ) {
                                            $database->query( $q );

                                        }
                                    }
                                }
                            } else {
                                $route = [];
                                $error_kz_upd = [];
                                $error_kz_ins = [];
                                $sheet = $xlsx->rows( $i );
                                if ( mb_strpos( mb_strtolower( $sheet[ 9 ][ 1 ] ), 'исключения одиночные позиции' ) === 0 ) {
                                    foreach ( $sheet as $n => $res ) {
                                        if ( $n > 11 && $n < count( $sheet ) ) {
                                            foreach ( $sheet[ $n ] as $k => $row ) {
                                                if ( $k > 1 && ( ( $k+1 ) % 3 ) == 0 ) {
                                                    $current_coef	 = isset( $sheet[ 3 ][ $k + 1 ] ) && $sheet[ 3 ][ $k + 1 ] ? ( float )$sheet[ 3 ][ $k + 1 ] : $sheet[ 2 ][ 1 ];
                                                    $current_marge	 = isset( $sheet[ 5 ][ $k + 4 ] ) && $sheet[ 5 ][ $k + 4 ] ? ( float )$sheet[ 5 ][ $k + 4 ] : $sheet[ 4 ][ 1 ];
                                                    $price = @ceil( $sheet[ $n ][ $k ] );
                                                    $price_coef = @ceil( $price * $current_coef );
                                                    $price_marge = @floor( $price_coef * $current_marge );
                                                    $route[ $sheet[ 6 ][ $k ] ][] = array(
                                                        'from'				=> $sheet[ $n ][ 0 ],
                                                        'to'					=> $sheet[ $n ][ 1 ],
                                                        'name'				=> $sheet[ 6 ][ $k ],
                                                        'on_group'			=> $sheet[ 7 ][ $k ],
                                                        'on_single'			=> $sheet[ 8 ][ $k ],
                                                        'off_single'		=> $sheet[ 9 ][ $k ],
                                                        'price'				=> $price,
                                                        'price_coef'		=> $price_coef,
                                                        'price_marge'		=> $price_marge,
                                                        'current_coef'	=> $current_coef,
                                                        'current_marge'	=> $current_marge
                                                    );
                                                }
                                            }
                                        }
                                    }
                                    $ins = [];
                                    $upd = [];
                                    $err = '';
                                    $table_name .= '_kz';
                                    foreach ( $route as $k => $row ) {
                                        foreach ( $row as $var => $val ) {
                                            if ( $val[ 'to' ] ) {

                                                $check =  $database->query( "SELECT * FROM `{$table_name}` WHERE `from`='{$val["from"]}' AND `to`='{$val["to"]}' AND `name`='{$val["name"]}' LIMIT 1" )->fetchAll();

                                                if ( $check ) {
                                                    $up = "UPDATE `{$table_name}` SET ";
                                                    foreach ( $val as $k => $v ) {
                                                        $up .= "`{$k}`='{$v}',";
                                                    }
                                                    $upd[] = trim( $up, ',' )." WHERE `id`='{$check[0]["id"]}'";
                                                } else {
                                                    $vr = '';
                                                    foreach ( $val as $k => $v ) {
                                                        $vr .= "'{$v}',";
                                                    }
                                                    $vr = trim( $vr, ',' );
                                                    $ins[] = "INSERT INTO `{$table_name}` (`".implode( '`,`', array_keys( $val ) )."`) VALUES({$vr})";
                                                }
                                            }
                                        }
                                    }
                                    if ( !empty( $upd ) ) {
                                        foreach ( $upd as $q ) {
                                            $database->query( $q );

                                        }
                                    }
                                    if ( !empty( $ins ) ) {
                                        foreach ( $ins as $q ) {
                                            $database->query( $q );

                                        }
                                    }
                                }
                            }
                        }
                    }

                } else {

                    $success = false;
                    $message = 'Неверный запрос!';
                    $error = true;
                }

            } else {

                $success = false;
                $message = 'Неверный запрос!';
                $error = true;
            }

        } else {

            $success = false;
            $message = 'Неверный запрос!';
            $error = true;

        }

        $this->data[ 'success' ] = $success;
        $this->data[ 'error' ] = $error;
        $this->data[ 'message' ] = $message;
        $this->data[ 'redirect' ] = $redirect;

    }
}