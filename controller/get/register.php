<?php

class registerController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $args ) {

        $this->header = 'common/header.twig';
        $this->footer = 'common/footer.twig';
        $this->fonts[] =  '/view/assets/fonts/font_59.css';
        $this->js[] = '/view/assets/js/jquery-ui.min.js';
        $this->css[] = '/view/assets/css/jquery-ui.css';

        if (count($args) > 1) {

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

        //$session->set( 'register', false );

        $step = $session->get( 'register' );

        // Step 1
        if ( !$step ) {

            $countries_model = Load::model( 'Countries' );
            $helpers_methods = Load::model( 'Helpers' );

            $countries = $countries_model->getCountries( $database );
            $countries_phones = $countries_model->getCountriesPhones( $database );

            $_countries_phones = [];

            foreach ( $countries_phones as $country_phone ) {

                $_countries_phones[] = [
                    'country_id' => $country_phone[ 'country_id' ],
                    'mask' => $country_phone[ 'mask' ],
                    'name' => $country_phone[ 'name' ],
                    'prefix' => explode( ' ', $country_phone[ 'mask' ] )[ 0 ],
                    'icon' => $helpers_methods->getImage( $country_phone[ 'icon' ] ),
                ];

            }

            $this->template = 'register/59-registration_1step.twig';
            $this->data = [
                'time'   => TIME,
                'header' => $this->header,
                'footer' => $this->footer,
                'fonts'  => $this->fonts,
                'js' => $this->js,
                'css'  => $this->css,

                'privacy_policy' => PRIVACY_POLICY,
                'platform_rules' => PLATFORM_RULES,
                'countries' => $countries,
                'countries_phones' => $_countries_phones

            ];

            // Step 2 or 3
        } else {

            if ( is_string( $step ) ) {

                $this->template = 'register/59-registration_2step.twig';
                $this->data = [
                    'time'   => TIME,
                    'header' => $this->header,
                    'footer' => $this->footer,
                    'fonts'  => $this->fonts,
                    'js' => $this->js,
                    'css'  => $this->css,

                ];

            } else {

                $this->template = 'register/59-registration_end.twig';
                $this->data = [
                    'time'   => TIME,
                    'header' => $this->header,
                    'footer' => $this->footer,
                    'fonts'  => $this->fonts,
                    'js' => $this->js,
                    'css'  => $this->css,

                ];

            }

        }

    }

}