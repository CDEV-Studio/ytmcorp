<?php

class Mail
 {

    function send( $to, $from, $sender, $subject, $text, $html = false, $reply_to = false, $attachments = [], $parameter = false ) {

        if ( is_array( $to ) ) {
            $to = implode( ',', $this->to );
        }

        $boundary = '----=_NextPart_' . md5( time() );

        $header  = 'MIME-Version: 1.0' . PHP_EOL;
        $header .= 'Date: ' . date( 'D, d M Y H:i:s O' ) . PHP_EOL;
        $header .= 'From: =?UTF-8?B?' . base64_encode( $sender ) . '?= <' . $from . '>' . PHP_EOL;

        if ( !$reply_to ) {
            $header .= 'Reply-To: =?UTF-8?B?' . base64_encode( $sender ) . '?= <' . $from . '>' . PHP_EOL;
        } else {
            $header .= 'Reply-To: =?UTF-8?B?' . base64_encode( $reply_to ) . '?= <' . $reply_to . '>' . PHP_EOL;
        }

        $header .= 'Return-Path: ' . $from . PHP_EOL;
        $header .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
        $header .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . PHP_EOL . PHP_EOL;

        if ( !$html ) {
            $message  = '--' . $boundary . PHP_EOL;
            $message .= 'Content-Type: text/plain; charset="utf-8"' . PHP_EOL;
            $message .= 'Content-Transfer-Encoding: base64' . PHP_EOL . PHP_EOL;
            $message .= base64_encode( $text ) . PHP_EOL;
        } else {
            $message  = '--' . $boundary . PHP_EOL;
            $message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . PHP_EOL . PHP_EOL;
            $message .= '--' . $boundary . '_alt' . PHP_EOL;
            $message .= 'Content-Type: text/plain; charset="utf-8"' . PHP_EOL;
            $message .= 'Content-Transfer-Encoding: base64' . PHP_EOL . PHP_EOL;

            if ( $text ) {
                $message .= base64_encode( $text ) . PHP_EOL;
            } else {
                $message .= base64_encode( 'This is a HTML email and your email client software does not support HTML email!' ) . PHP_EOL;
            }

            $message .= '--' . $boundary . '_alt' . PHP_EOL;
            $message .= 'Content-Type: text/html; charset="utf-8"' . PHP_EOL;
            $message .= 'Content-Transfer-Encoding: base64' . PHP_EOL . PHP_EOL;
            $message .= base64_encode( $html ) . PHP_EOL;
            $message .= '--' . $boundary . '_alt--' . PHP_EOL;
        }

        foreach ( $attachments as $attachment ) {
            if ( file_exists( $attachment ) ) {
                $handle = fopen( $attachment, 'r' );

                $content = fread( $handle, filesize( $attachment ) );

                fclose( $handle );

                $message .= '--' . $boundary . PHP_EOL;
                $message .= 'Content-Type: application/octet-stream; name="' . basename( $attachment ) . '"' . PHP_EOL;
                $message .= 'Content-Transfer-Encoding: base64' . PHP_EOL;
                $message .= 'Content-Disposition: attachment; filename="' . basename( $attachment ) . '"' . PHP_EOL;
                $message .= 'Content-ID: <' . urlencode( basename( $attachment ) ) . '>' . PHP_EOL;
                $message .= 'X-Attachment-Id: ' . urlencode( basename( $attachment ) ) . PHP_EOL . PHP_EOL;
                $message .= chunk_split( base64_encode( $content ) );
            }
        }

        $message .= '--' . $boundary . '--' . PHP_EOL;

        ini_set( 'sendmail_from', $from );

        if ( $parameter ) {
            mail( $to, '=?UTF-8?B?' . base64_encode( $subject ) . '?=', $message, $header, $parameter );
        } else {
            mail( $to, '=?UTF-8?B?' . base64_encode( $subject ) . '?=', $message, $header );
        }

    }

}