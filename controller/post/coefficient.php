<?php

class coefficientController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {

        $success = true;
        $error = false;
        $message = '';
        $redirect = false;


        if ( $request->getHeaderLine( 'X-Requested-With' ) === 'XMLHttpRequest' ) {

            $request = $request->getParsedBody();

            if ( isset( $request[ 'coefficient' ] ) &&  !empty( $request[ 'coefficient' ] ) && isset( $request[ 'id' ] ) &&  !empty( $request[ 'id' ] ) ) {

                $coefficients_model = Load::model( 'Coefficients' );
                $coefficients_model->setCoefficient( $database, $request[ 'coefficient' ], $request[ 'id' ] );
 

            } else {

                $success = false;
                $message = 'Заполните все поля!';
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