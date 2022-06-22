<?php

class wagonController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {

        $success = true;
        $error = false;
        $message = '';
        $redirect = false;


        if ( $request->getHeaderLine( 'X-Requested-With' ) === 'XMLHttpRequest' ) {

            $request = $request->getParsedBody();

            if ( isset( $request[ 'railway_carriage_type' ] ) &&  !empty( $request[ 'railway_carriage_type' ] )) {

                $wagon_model = Load::model( 'Wagon' );
                $wagon_model->addWagon( $database, $request  );

                $redirect = '/admin/wagon-list';
 

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