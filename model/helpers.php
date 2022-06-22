<?php

use Respect\Validation\Validator as validator;
use Psr\Http\Message\UploadedFileInterface;
use \Gumlet\ImageResize;

class Helpers
 {

    public function getSettings( $database ) {

        return $database->select( 'settings', '*' )[ 0 ];

    }

    public function format( $number ) {

        if ( $number ) {

            //$number = round( $number, 1 );
            //$number = number_format( $number, 1, ',', ' ' );
 $number = number_format( $number,1,","," ");
            $_number = explode( ',', $number );
            $_number = array_pop( $_number );

            if ( $_number == '0' ) {

               // $number = trim( $number, ',0' );

            }

        }

        return $number;

    }

    public function getImage( $image ) {

        return '/data/img/' .  $image;

    }

    // Очистка пользовательского ввода
    public static function clean( $data, $type = false ) {

        if ( !validator::stringType()->notEmpty()->validate( $data ) ) {
            $data = false;
            $type = false;
        }

        if ( $type ) {

            switch ( $type ):

            case 'phone':

            $additionalChars = ' +1234567890()- _';

            if ( !validator::alnum( $additionalChars )->validate( $data ) ) {
                $data = false;
            }

            break;

            case 'password':

            $additionalChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

            if ( !validator::alnum( $additionalChars )->validate( $data ) || !validator::stringType()->length( 8, null )->validate( $data ) ) {
                $data = false;
            }

            break;

            case 'image':

            if ( !validator::image()->validate( $data ) ) {
                $data = false;
            }

            if ( $data && !validator::extension( 'png' )->validate( $data ) && !validator::extension( 'jpg' )->validate( $data ) ) {
                $data = false;
            }

            break;

            case 'email':

            if ( !validator::email()->validate( $data ) ) {
                $data = false;
            }

            break;

            case 'json':

            if ( !validator::json()->validate( $data ) ) {
                $data = false;
            }

            break;

            endswitch;
        }

        return $data;
    }

    function generateNewPassword( $length = 10 ) {

        $password = '';

        $arr = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
        );

        for ( $i = 0; $i < $length; $i++ ) {
            $password .= $arr[ random_int( 0, count( $arr ) - 1 ) ];
        }

        return $password;

    }

    function moveUploadedFile( string $directory, UploadedFileInterface $uploadedFile, $long_size ) {

        $extension = pathinfo( $uploadedFile->getClientFilename(), PATHINFO_EXTENSION );

        // see http://php.net/manual/en/function.random-bytes.php
        $basename = bin2hex( random_bytes( 8 ) );
        $filename = sprintf( '%s.%0.8s', $basename, $extension );

        $uploadedFile->moveTo( $directory . DIRECTORY_SEPARATOR . $filename );

        if ( $long_size > 0 ) {

            $image = new ImageResize( $directory . DIRECTORY_SEPARATOR . $filename );
            $image->resizeToLongSide( $long_size );
            $image->save( $directory . DIRECTORY_SEPARATOR . $filename );

        }

        return $filename;
    }
}