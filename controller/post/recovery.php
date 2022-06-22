<?php

class recoveryController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {

        $success = true;
        $error = false;
        $message = '';
        $redirect = false;

        $mail = Load::model( 'Mail' );
        $helpers_methods = Load::model( 'Helpers' );
        $cargo_owners_model = Load::model( 'CargoOwners' );

        if ( $request->getHeaderLine( 'X-Requested-With' ) === 'XMLHttpRequest' ) {

            $request = $request->getParsedBody();

            if ( isset( $request[ 'email' ] ) &&  !empty( $request[ 'email' ] ) && $helpers_methods->clean( $request[ 'email' ], 'email' ) ) {

                $cargo_owner = $cargo_owners_model->getOwnerByEmail( $database, $request[ 'email' ] );

                if ( isset( $cargo_owner[ 0 ][ 'password' ] ) ) {

                    $new_password = $helpers_methods->generateNewPassword();
  
                    $message = 'На Ваш запроc о сбросе пароля мы сформировали для Вас новый пароль для сайта ' . $_SERVER[ 'SERVER_NAME' ] . ':' . $new_password;
                    
                    $new_password = password_hash($new_password, PASSWORD_DEFAULT );

                    if($cargo_owners_model->updatePassword(  $database, $cargo_owner[ 0 ][ 'owner_id' ], $new_password )) {

                       $mail->send( trim($request[ 'email' ]), FROM, SENDER, 'Сброс пароля для сайта'  . $_SERVER[ 'SERVER_NAME' ], $message );
                       $redirect = '/';

                    } else {
                       $success = false;
                       $message = [ '#input_email', 'Мы не смогли отправить Вам письмо!Обратитесь к администрации сайта!' ];
                       $error = true;
                    }

                    

                } else {

                    $success = false;
                    $message = [ '#input_email', 'Пользватель с таким email не найден!' ];
                    $error = true;
                }

            } else {
                $success = false;
                $message = [ '#input_email', 'Укажите email!' ];
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