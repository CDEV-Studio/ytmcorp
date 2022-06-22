<?php

class adminController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $args ) {

        $this->header = 'common/header.twig';
        $this->footer = 'common/footer.twig';

        if ( count( $args ) !== 2 ) {

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

        $route = explode( '_', $args[ 1 ] );

        switch ( $route[ 0 ] ):

        case 'not-verified-users':

        $this->fonts[] = '/view/assets/fonts/font_67.css';
        $this->template = 'admin/67-users-not-verified.twig';
        $this->js[] = '/view/assets/js/jquery-ui.min.js';
        $this->css[] = '/view/assets/css/jquery-ui.css';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        $cargo_owners_model = Load::model( 'CargoOwners' );
        $time_methods = Load::model( 'Timer' );

        $filters = [
            'AND' => [
                'verified' => 0,
            ],
            'ORDER' => [ 'owner_id' => 'ASC' ]
        ];

        $cargo_owners_count = $cargo_owners_model->getCountOwners( $database, $filters );

        $this->data[ 'pages' ] = round( $cargo_owners_count / CARGO_OWNERS_LIMIT );

        if ( !$this->data[ 'pages' ] ) {
            $this->data[ 'pages' ] = 1;
        }

        $filters[ 'LIMIT' ] =  [ 0, CARGO_OWNERS_LIMIT ];

        $cargo_owners = $cargo_owners_model->getOwners( $database, $filters );

        if ( count( $cargo_owners ) < 10 ) {
            $this->data[ 'css_table_fix_class' ] = true;
        } else {
            $this->data[ 'css_table_fix_class' ] = false;

        }

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

            $this->data[ 'cargo_owners_filters' ][ 'owner_id' ][ $cargo_owner[ 'owner_id' ] ] = $cargo_owner[ 'owner_id' ];
            $this->data[ 'cargo_owners_filters' ][ 'registration_date' ][ $time_methods->getTime( $cargo_owner[ 'registration_date' ] ) ] = $time_methods->getTime( $cargo_owner[ 'registration_date' ] );
            $this->data[ 'cargo_owners_filters' ][ 'last_visit_date' ][ $time_methods->getTime( $cargo_owner[ 'last_visit_date' ] ) ] = $time_methods->getTime( $cargo_owner[ 'last_visit_date' ] );
            $this->data[ 'cargo_owners_filters' ][ 'email' ][ $cargo_owner[ 'email' ] ] = $cargo_owner[ 'email' ];
            $this->data[ 'cargo_owners_filters' ][ 'phone' ][ $cargo_owner[ 'phone' ] ] = $cargo_owner[ 'phone' ];
            $this->data[ 'cargo_owners_filters' ][ 'verification_request_date' ][ $time_methods->getTime( $cargo_owner[ 'verification_request_date' ] ) ] = $time_methods->getTime( $cargo_owner[ 'verification_request_date' ] );
            $this->data[ 'cargo_owners_filters' ][ 'rejection_reason' ][ $cargo_owner[ 'rejection_reason' ] ] = $cargo_owner[ 'rejection_reason' ];

        }

        break;

        case 'owner':

        $this->fonts[] = '/view/assets/fonts/font_71.css';
        $this->template = 'admin/69-user-information.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        if ( isset( $route[ 1 ] ) ) {

            $time_methods = Load::model( 'Timer' );

            $cargo_owners_model = Load::model( 'CargoOwners' );
            $cargo_owner = $cargo_owners_model->getOwner( $database, $route[ 1 ] );

        } else {
            $cargo_owner = false;
        }

        if ( $cargo_owner ) {

            $countries_model = Load::model( 'Countries' );
            $this->data[ 'countries' ] = $countries_model->getCountries( $database );

            $country_name = '';

            foreach ( $this->data[ 'countries' ] as $country ) {
                if ( $country[ 'country_id' ] == $cargo_owner[ 0 ][ 'residence_country_id' ] ) {
                    $country_name = $country[ 'name' ];
                }
            }
            $this->data[ 'cargo_owner' ] = [

                'name' => $cargo_owner[ 0 ][ 'name' ],
                'owner_id' => $cargo_owner[ 0 ][ 'owner_id' ],
                'registration_date' => $time_methods->getTime( $cargo_owner[ 0 ][ 'registration_date' ] ),
                'subscribe' => false,
                'expeditor' => false,
                'verification_request_date' => $time_methods->getTime( $cargo_owner[ 0 ][ 'verification_request_date' ] ),
                'rating' => false,
                'balance' => false,
                'bin_inn' => $cargo_owner[ 0 ][ 'bin_inn' ],
                'country_name' => $country_name,
                'country_id' => $cargo_owner[ 0 ][ 'residence_country_id' ],
                'phone' => $cargo_owner[ 0 ][ 'phone' ],
                'mask' => trim( str_replace( [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ], '9', $cargo_owner[ 0 ][ 'phone' ] ) ),
                'site' => $cargo_owner[ 0 ][ 'site' ],
                'legal_address' => $cargo_owner[ 0 ][ 'legal_address' ],
                'mailing_address' => $cargo_owner[ 0 ][ 'mailing_address' ],
                'registration_certificate' => $cargo_owner[ 0 ][ 'registration_certificate' ],
                'agreement' => $cargo_owner[ 0 ][ 'agreement' ],
                'identification' => $cargo_owner[ 0 ][ 'identification' ],
                'registrant_name' => $cargo_owner[ 0 ][ 'registrant_name' ],
                'registrant_phone' => $cargo_owner[ 0 ][ 'registrant_phone' ],
                'registrant_telegram' => $cargo_owner[ 0 ][ 'registrant_telegram' ],
                'registrant_whatsapp' => $cargo_owner[ 0 ][ 'registrant_whatsapp' ],
                'registrant_email' => $cargo_owner[ 0 ][ 'registrant_email' ],
                'collaborators' => false,
            ];

        } else {

            $this->template = 'common/not_found.twig';

        }

        break;

        case 'countries':

        $this->fonts[] = '/view/assets/fonts/font_80.css';
        $this->template = 'admin/80-countries.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        $countries_model = Load::model( 'Countries' );
        $images = Load::model( 'Images' );
        $this->data[ 'countries' ] = $countries_model-> getCountriesPhones( $database, false, [ 'country_id' => 'DESC' ] );

        foreach ( $this->data[ 'countries' ] as $x=>$country ) {

            $this->data[ 'countries' ][ $x ][ 'icon' ] = $images->getImageLink( $country[ 'icon' ] );
        }

        break;

        case 'calculations':

        $this->fonts[] = '/view/assets/fonts/font_71.css';
        $this->template = 'admin/74-calculations.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        $calculation_model = Load::model( 'Calculation' );
        $timer = Load::model( 'Timer' );

        $this->data[ 'calculations' ]  = $calculation_model->getCalculations( $database, [ 'ORDER' => [ 'calculation_id' =>  'DESC' ] ] );
        ///////// paginations ??

        foreach ( $this->data[ 'calculations' ] as $x=>$calculation ) {

            $this->data[ 'calculations' ][ $x ][ 'calculation_date' ] =  $timer->getTime( $calculation[ 'calculation_date' ] );

            $loading_station_name = [];

            if ( !empty( $calculation[ 'loading_station_name' ] ) ) {
                $loading_station_name[] = $calculation[ 'loading_station_name' ];
            }

            if ( !empty( $calculation[ 'loading_station_department' ] ) ) {
                $loading_station_name[] = $calculation[ 'loading_station_department' ];
            }

            if ( !empty( $calculation[ 'loading_station_railway' ] ) ) {
                $loading_station_name[] = $calculation[ 'loading_station_railway' ];
            }

            if ( !empty( $calculation[ 'loading_station_country' ] ) ) {
                $loading_station_name[] = $calculation[ 'loading_station_country' ];
            }

            $this->data[ 'calculations' ][ $x ][ 'loading_station_name' ] = join( ',', $loading_station_name );

            $unloading_station_name = [];

            if ( !empty( $calculation[ 'unloading_station_name' ] ) ) {
                $unloading_station_name[] = $calculation[ 'unloading_station_name' ];
            }

            if ( !empty( $calculation[ 'unloading_station_department' ] ) ) {
                $unloading_station_name[] = $calculation[ 'unloading_station_department' ];
            }

            if ( !empty( $calculation[ 'unloading_station_railway' ] ) ) {
                $unloading_station_name[] = $calculation[ 'unloading_station_railway' ];
            }

            if ( !empty( $calculation[ 'unloading_station_country' ] ) ) {
                $unloading_station_name[] = $calculation[ 'unloading_station_country' ];
            }

            $this->data[ 'calculations' ][ $x ][ 'unloading_station_name' ] = join( ',', $unloading_station_name );
        }

        break;

        case 'calculation':

        $this->fonts[] = '/view/assets/fonts/font_67.css';
        $this->template = 'admin/75-request_info.twig';

        if ( isset( $route[ 1 ] ) ) {

            $calculation_model = Load::model( 'Calculation' );
            $calculation =  $calculation_model->getCalculation( $database, [ 'calculation_id' => ( int )$route[ 1 ] ] );

        } else {

            $calculation = false;
        }

        if ( $calculation ) {

            $this->data = $calculation_model->tariffCalculation( $database, $calculation, ( int )$route[ 1 ] );

        } else {

            $this->template = 'common/not_found.twig';

        }

        $this->data[ 'time' ]   = TIME;
        $this->data[ 'header' ] = $this->header;
        $this->data[ 'footer' ] = $this->footer;
        $this->data[ 'fonts' ]  = $this->fonts;
        $this->data[ 'js' ] = $this->js;
        $this->data[ 'css' ]  = $this->css;

        break;

        case 'import':

        $this->fonts[] = '/view/assets/fonts/font_67.css';
        $this->template = 'admin/81-matrix_import.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        $railway_model = Load::model( 'Railway' );
        $this->data[ 'railway_carriage' ] = $railway_model->getRailwayCarriage( $database, [] );

        break;

        case 'coefficients':

        $this->fonts[] = '/view/assets/fonts/font_67.css';
        $this->template = 'admin/90-coefficients.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        $coefficients_model = Load::model( 'Coefficients' );
        $this->data[ 'coefficients' ] = $coefficients_model->getCoefficients( $database );

        break;

        case 'wagon-list':

        $this->fonts[] = '/view/assets/fonts/font_67.css';
        $this->template = 'admin/84-wagon-list-card.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        $railway_model = Load::model( 'Railway' );
        $this->data[ 'railway_carriage' ] = $railway_model->getRailwayCarriage( $database, [] );

        break;

        case 'create-wagon':

        $this->fonts[] = '/view/assets/fonts/font_67.css';
        $this->template = 'admin/84-wagon-list-create.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        break;

        case 'currencies':

        $this->fonts[] = '/view/assets/fonts/font_67.css';
        $this->template = 'admin/83-currencies_rates.twig';
        $this->data = [
            'time'   => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        $currencies_model = Load::model( 'Currencies' );

        //$currencies_model->getCurrenciesFromAPI( $database );

        $this->data[ 'currencies' ] = $currencies_model->getCurrencies( $database, false );
        $this->data[ 'percent' ] = $currencies_model->getPercent( $database );

        break;

        default:

        $this->template = 'common/not_found.twig';
        $this->data = [
            'time' => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];

        endswitch;

    }
}