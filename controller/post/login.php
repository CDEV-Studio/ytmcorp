<?php

class loginController extends Controller
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

            if ( isset( $request[ 'email' ] ) &&  !empty( $request[ 'email' ] )  && $helpers_methods->clean( $request[ 'email' ], 'email' ) && isset( $request[ 'password' ] ) &&  !empty( $request[ 'password' ] ) && $helpers_methods->clean( $request[ 'password' ], 'password' ) ) {

                $cargo_owner = $cargo_owners_model->getOwnerByEmail( $database, $request[ 'email' ] );

                if ( isset( $cargo_owner[ 0 ][ 'password' ] ) ) {

                    if ( password_verify( $request[ 'password' ], $cargo_owner[ 0 ][ 'password' ] ) ) {

                        $session->set( 'session_id', $cargo_owner[ 0 ][ 'owner_id' ] );
                        $redirect = '/calculation';

                    } else {

                        $success = false;
                        $message = [ '#input_password', 'Пароль не верный!' ];
                        $error = true;

                    }

                } else {

                    $success = false;
                    $message = [ '#input_email', 'Пользватель с таким email не найден!' ];
                    $error = true;
                }

            } else {
                $success = false;
                $message = [ '#input_email,#input_password', 'Заполните все поля!' ];
                $error = true;
            }

            $this->data = [
                'success'   => $success,
                'error'     => $error,
                'message'   => $message,
                'redirect'  => $redirect
            ];

        }

    }
}