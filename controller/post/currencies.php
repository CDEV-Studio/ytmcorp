<?php

class currenciesController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {

        $success = true;
        $error = false;
        $message = '';
        $redirect = false;

        $helpers_methods = Load::model( 'Helpers' );

        if ( $request->getHeaderLine( 'X-Requested-With' ) === 'XMLHttpRequest' ) {

            $request = $request->getParsedBody();

            if ( isset( $request[ 'percent' ] )  ) {

                $currencies_model = Load::model( 'Currencies' );
                $currencies_model->setPercent( $database, (int)$request[ 'percent' ] );

                $redirect = '/admin/currencies';

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