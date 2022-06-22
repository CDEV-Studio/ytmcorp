<?php

class CargoOwners
 {

    function getOwners( $database, $where ) {

        return $database->select( 'cargo_owners', '*', $where );

    }

    function getCountOwners( $database, $where ) {

        return $database->count( 'cargo_owners', $where );

    }

    function getOwner ( $database, $owner_id ) {

        return $database->select( 'cargo_owners', [ '[>]registrants' => 'registrant_id' ], '*', [ 'owner_id' => $owner_id, 'LIMIT' => 1 ] );

    }

    function getOwnerByEmail( $database, $email ) {

        return $database->select( 'cargo_owners', [ 'owner_id', 'password' ], [ 'email' => $email, 'LIMIT' => 1 ] );

    }

    function getOwnerByPhone( $database, $phone ) {

        return $database->select( 'cargo_owners', [ 'owner_id', 'password' ], [ 'phone' => $phone, 'LIMIT' => 1 ] );

    }

    function addOwner( $database, $data ) {

        $database->insert( 'cargo_owners', $data );
        return $database->id();

    }

    function editOwner( $database, $data, $owner_id ) {

        $database->update( 'cargo_owners', $data, [ 'owner_id' => $owner_id ] );
       
    }

    function updatePassword( $database,  $owner_id, $password ) {

        $response = $database->update( 'cargo_owners', [ 'password' =>  $password ], [ 'owner_id' => $owner_id ] );
        return $response->rowCount();

    }

}