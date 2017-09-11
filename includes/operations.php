<?php

require("DB.php");
require("resources.php");

$db = new DB();
if( $_SERVER['REQUEST_METHOD'] === "POST" )
{
    if( isset($_POST['ajax_country_id']) )
    {
        $state_extract = $db->getRows("states", array( 'where' => array( 'country_id' => $_POST['ajax_country_id'] ) ));
        foreach( $state_extract as $sek )
        {
            echo '<option value="' . $sek['id'] . '">' . $sek['name'] . '</option>';
        }
    }
    else if( isset($_POST['ajax_state_id']) )
    {
        $city_extract = $db->getRows("cities", array( 'where' => array( 'state_id' => $_POST['ajax_state_id'] ) ));
        foreach( $city_extract as $cek )
        {
            echo '<option value="' . $cek['id'] . '">' . $cek['name'] . '</option>';
        }
    }
    else if( isset($_POST['reg_submit']) )
    {
        header("location:asdf");
    }
    else if( isset($_POST['mobile_no']) )
    {
        $exist = $db->getRows(G_REG_TABLE, array( "where" => array( "mobile_no" => $_POST['mobile_no'] ), "return_type" => "count" ));
        if( $exist > 0 )
        {
            // User name is registered
            $response = array( 'valid' => false, 'message' => 'This mobile number is already registered.' );
        }
        else
        {
            // User name is available
            $response = array( 'valid' => true );
        }
        echo json_encode($response);
    }
    else if( isset($_POST['email_id']) )
    {
        $exist = $db->getRows(G_REG_TABLE, array( "where" => array( "email_id" => $_POST['email_id'] ), "return_type" => "count" ));
        if( $exist > 0 )
        {
            // User name is registered
            $response = array( 'valid' => false, 'message' => 'This email is already registered.' );
        }
        else
        {
            // User name is available
            $response = array( 'valid' => true );
        }
        echo json_encode($response);
    }
}