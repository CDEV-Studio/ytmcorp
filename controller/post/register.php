<?php

class registerController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {

        $success = true;
        $error = false;
        $message = '';
        $redirect = false;

        $helpers_methods = Load::model( 'Helpers' );
        $cargo_owners_model = Load::model( 'CargoOwners' );

        if ( $request->getHeaderLine( 'X-Requested-With' ) === 'XMLHttpRequest' ) {

            $request = $request->getParsedBody();

            if ( isset( $request[ 'step' ] ) &&  $request[ 'step' ]  == 1 ) {

                if ( isset( $request[ 'password_2' ] ) && !empty( $request[ 'password_2' ] ) && $helpers_methods->clean( $request[ 'password_2' ], 'password' ) ) {

                    if ( isset( $request[ 'password_1' ] ) && !empty( $request[ 'password_1' ] ) && $helpers_methods->clean( $request[ 'password_1' ], 'password' ) && $request[ 'password_2' ] !== $request[ 'password_1' ] ) {
                        $success = false;
                        $message = [ '#password_2', 'Пароли не совпадают!' ];
                        $error = true;
                    }

                } else {
                    $success = false;
                    $message = [ '#password_1,#password_2', 'Укажите пароль не менее 8 символов латинского алфавита!' ];
                    $error = true;
                }

                if ( isset( $request[ 'password_1' ] ) && !empty( $request[ 'password_1' ] ) && $helpers_methods->clean( $request[ 'password_1' ], 'password' ) ) {

                } else {
                    $success = false;
                    $message = [ '#password_1,#password_2', 'Укажите пароль не менее 8 символов латинского алфавита!' ];
                    $error = true;
                }

                if ( isset( $request[ 'country_id' ] ) && !empty( $request[ 'country_id' ] ) && ( int )$request[ 'country_id' ] ) {

                } else {
                    $success = false;
                    $message = [ '#country_select', 'Выберите страну резиденства!' ];
                    $error = true;
                }

                if ( isset( $request[ 'email' ] ) && !empty( $request[ 'email' ] ) && $helpers_methods->clean( $request[ 'email' ], 'email' ) ) {

                    $cargo_owner = $cargo_owners_model->getOwnerByEmail( $database, $request[ 'email' ] );

                    if ( isset( $cargo_owner[ 0 ][ 'password' ] ) ) {
                        $success = false;
                        $message = [ '#registration__mail', 'Пользователь с таким email уже зарегистрирован!' ];
                        $error = true;
                    }

                } else {
                    $success = false;
                    $message = [ '#registration__mail', 'Укажите email!' ];
                    $error = true;
                }

                if ( isset( $request[ 'phone' ] ) && !empty( $request[ 'phone' ] ) && $helpers_methods->clean( $request[ 'phone' ], 'phone' ) ) {
                    
                    $phone = $request[ 'phone' ];
                    $phone = str_replace( [ '(', ')', ' ' ], '', $phone );

                    $cargo_owner = $cargo_owners_model->getOwnerByPhone( $database, $request[ 'phone' ] );

                    if ( isset( $cargo_owner[ 0 ][ 'password' ] ) ) {
                        $success = false;
                        $message = [ '.registration__tel', 'Пользователь с таким телефоном уже зарегистрирован!' ];
                        $error = true;
                        $phone = false;
                    }
 
                } else {
                    $success = false;
                    $phone = false;
                    $message = [ '.registration__tel', 'Укажите номер телефона в указанном формате!' ];
                    $error = true;
                }

                if ( $success && $phone ) {

                    require_once DOCUMENT_ROOT .  '/system/smsc_api.php';

                    $code = rand( 100000, 999999 );

                    $code = 'Код для регистрации на сайте ' . $_SERVER[ 'SERVER_NAME' ] .': ' . $code;

                    //$sms_response = send_sms( $phone, $code );

                    //////////////////////////////////////////// код для теста

                    $code = 123456;
                    $sms_response = array( '1' => 2 );

                    ///////////////////////////////////////////

                    if ( $sms_response[ 1 ] > 0 ) {

                        $success = true;
                        $message = '';
                        //'СМС сообщение отправлено на номер:' . $request[ 'phone' ];
                        $redirect = '/register';

                        $data = [
                            'code' => $code,
                            'registration_date' => time(),
                            'phone' => trim( $request[ 'phone' ] ),
                            'email' => trim( $request[ 'email' ] ),
                            'residence_country_id' => $request[ 'country_id' ],
                            'password' => password_hash( $request[ 'password_1' ], PASSWORD_DEFAULT )
                        ];

                        $data = json_encode( $data );
                        $session->set( 'register', $data );

                    } else {

                        $success = false;
                        $message = [ '.registration__tel', 'СМС сообщение не отправлено. Код ошибки:' . $sms_response[ 1 ] ];
                        $error = true;

                    }

                }

                $this->data = [
                    'success'   => $success,
                    'error'     => $error,
                    'message'   => $message,
                    'redirect'  => $redirect,
                ];

            } elseif ( isset( $request[ 'step' ] ) &&  $request[ 'step' ] == 2 ) {

                if ( $session->get( 'register' ) ) {

                    $data = json_decode( $session->get( 'register' ), true );

                    if ( isset( $data[ 'code' ] ) && !empty( $data[ 'code' ] ) && isset( $request[ 'code' ] ) && $request[ 'code' ] == $data[ 'code' ] ) {

                        $success = true;
                        $message = '';
                        //'Код из СМС введён верно!';
                        $redirect = '/register';

                        unset( $data[ 'code' ] );
                        $owner_id = $cargo_owners_model->addOwner( $database, $data );

                        if ( $owner_id ) {
                            $session->set( 'session_id', $owner_id );
                            $session->set( 'register', true );
                        }

                    } else {

                        $message =  [ '#code', 'Код из СМС введён не верно!' ];
                        $error = true;

                    }
                }

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