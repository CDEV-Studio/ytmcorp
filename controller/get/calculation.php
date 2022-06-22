<?php

class calculationController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $args ) {

        $this->header = 'common/header.twig';
        $this->footer = 'common/footer.twig';
        $this->fonts[] = '/view/assets/fonts/font_9.css';
        $this->js[] = '/view/assets/js/jquery-ui.min.js';
        $this->css[] = '/view/assets/css/jquery-ui.css';

        if ( count( $args ) > 2 ) {

            $this->template = 'common/not_found.twig';
            $this->data = [
                'time' => TIME,
                'header' => $this->header,
                'footer' => $this->footer,
                'fonts'  => $this->fonts,
                'js' => $this->js,
                'css'  => $this->css,

            ];
            return;

        }

        if ( isset( $args[ 1 ] ) ) {
            $route = explode( '_', $args[ 1 ] );
        } else {
            $route = [ '0' => 'default' ];
        }

        switch ( $route[ 0 ] ):

        case 'result':

        $calculation = [];
        $currencies = [];

        $this->template = 'common/not_found.twig';

        if ( isset( $route[ 1 ] ) && ( int )$route[ 1 ] > 0 ) {

            $calculation_model = Load::model( 'Calculation' );
            $calculation  = $calculation_model->getCalculation( $database, [ 'calculation_id' => ( int )$route[ 1 ], 'LIMIT' => 1 ] );

            if ( $calculation ) {

                $this->data = $calculation_model->tariffCalculation( $database, $calculation, ( int )$route[ 1 ] );

                $this->css[] = '/view/assets/fonts/font_10.css';
                $this->template = 'calculation/10-calculation_result.twig';

            }

        }

        $this->data[ 'time' ]   = TIME;
        $this->data[ 'header' ] = $this->header;
        $this->data[ 'footer' ] = $this->footer;
        $this->data[ 'fonts' ]  = $this->fonts;
        $this->data[ 'js' ] = $this->js;
        $this->data[ 'css' ]  = $this->css;

        break;

        case 'results':

        $this->template = 'common/not_found.twig';

        $calculations  =  [];

        if ( !isset( $route[ 1 ] ) ) {

            $calculation_model = Load::model( 'Calculation' );
            $calculations  = $calculation_model->getCalculations( $database, [ 'owner_id' => 12, 'ORDER' => [ 'calculation_id' =>  'DESC' ] ] );
            /////////////////////////////////// взять из сессии?

            $this->css[] = '/view/assets/fonts/font_8.css';
            $this->template = 'calculation/8-calculation_history.twig';

        }

        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,
            'calculations' => $calculations
        ];

        break;

        default:

        $helpers_methods = Load::model( 'Helpers' );

        $settings = $helpers_methods->getSettings( $database );

        $railway_model = Load::model( 'Railway' );
        $railway_carriage = $railway_model->getRailwayCarriage( $database, [] );

        $this->template = 'calculation/9-calculation_new.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,
            'max_weight'  => $settings[ 'max_weight' ],
            'railway_carriage' => $railway_carriage

        ];

        endswitch;

    }
}