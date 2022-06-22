<?php

class API
 {

    function getLoadingStation( $filter ) {

        return $this->getData( [ 'path' => 'CalcService.svc/SearchInRef?ref_id=9&search=' . $filter ] );

    }

    function getLoadingETSNG( $filter ) {

        return $this->getData( [ 'path' => 'CalcService.svc/SearchInRef?ref_id=50&search=' . $filter ] );

    }

    function getLoadingGNG( $filter ) {

        return $this->getData( [ 'path' => 'CalcService.svc/SearchInRef?ref_id=3&search=' . $filter ] );

    }

    function getRelated( $filter ) {

        return $this->getData( [ 'path' => 'CalcService.svc/GetRelatedFreights?code=' . $filter ] );

    }

    function getCalculationFromAPI( $loading_station_id, $unloading_station_id, $etsng_id, $gng_id, $weight, $railway_carriage_api_code ) {

        $variables = '';

        if ( !empty( $etsng_id ) && $etsng_id > 0 ) {
            $variables .= '&etsng=' . $etsng_id;
        }

        if ( !empty( $gng_id ) && $gng_id > 0 ) {
            $variables .= '&gng=' . $gng_id;
        }

        if ( !empty( $weight ) && $weight > 0 ) {
            $variables .= '&weight=' . $weight;
        }

        return $this->getData( [ 'path' => 'CalcService.svc/Calc?fs=' . $loading_station_id . '&ts=' . $unloading_station_id . '&carid=' . $railway_carriage_api_code . $variables ] );

    }

    public function getData( $query ) {

        $response = array( 'query' => $query, 'success' =>  false, 'message' => 'Error! Token or path or type not isset!', 'response' => array() );

        $url = 'https://calcservice-ytz.ctm.ru/';
        $token = '299d7863-5le5-4054-0710-b1ae724bdte0';

        if ( isset( $query[ 'path' ] ) ) {

            if ( is_array( $query[ 'path' ] ) && count( $query[ 'path' ] ) > 0 ) {

                foreach ( $query[ 'path' ] as $path=>$parameters ) {

                    $url .= $path . '?';
                    $_parameters = array();

                    foreach ( $parameters as $key=>$value ) {

                        $_parameters[] = $key . '=' . $value;
                    }

                }

                $url .= join( '&', $_parameters );

            } else {

                $url .= $query[ 'path' ];

            }

            $url .= '&token=' . $token;

            $ch = curl_init( $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept:  */*'
            ] );

            if ( isset( $query[ 'type' ] ) && $query[ 'type' ] == 'POST' ) {
                curl_setopt( $ch, CURLOPT_POST, true );
            }

            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 200 );

            $response[ 'response' ] = json_decode( curl_exec( $ch ), true );
            $response[ 'http_code' ] = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            $response[ 'message' ] = 'HTTP:' . curl_getinfo( $ch, CURLINFO_HTTP_CODE );

            if ( $response[ 'http_code' ] == 200 ) {
                $response[ 'success' ] = true;
            }

            if ( curl_errno( $ch ) )  $response[ 'curl_error' ] = curl_error( $ch );

            curl_close( $ch );

        }

        return $response;

    }

}