<?php

class notFoundController extends Controller
 {

    function __construct( $database, $access, $headers, $session, $request ) {
        
        $this->header = 'common/header.twig';
        $this->footer = 'common/footer.twig';

        $this->template = 'common/not_found.twig';
        $this->data = [
            'time' => TIME,
            'header' => $this->header,
            'footer' => $this->footer,
            'fonts'  => $this->fonts,
            'js' => $this->js,
            'css'  => $this->css,

        ];
    }
}