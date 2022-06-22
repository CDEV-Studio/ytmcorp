<?php

class loginController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $args ) {

        $this->header = 'common/header.twig';
        $this->footer = 'common/footer.twig';
        $this->fonts[] = '/view/assets/fonts/font_59.css';

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

        $this->template = 'register/59-login.twig';
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