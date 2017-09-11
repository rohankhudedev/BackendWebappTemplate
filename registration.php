<?php
require("includes/DB.php");
$db              = new DB();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Bootstrap Example</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- Start Date Picker-->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            #loader {
                position: fixed;
                z-index: 100;
                width: 100%;
                height: 100%;
                top: 0px;
                left: 0px;
                background-color:rgba(255, 255, 255, 0.9);
                z-index: 99991;
            }
        </style>
    </head>
    <body>
        <div id="loader" style="display:none">
            <div style="width:100%; height:100vh; display: table;">
                <div style="text-align: center; vertical-align: middle; display: table-cell;" class="black_text">
                    <div> <img src="cube-loading.gif" alt="Processing"> </div>
                    <div class="padd_tb_15">Processing</div>
                </div>
            </div>
        </div>

        <div class="container">
            <h2>REGISTRATION FORM</h2>
            <form method="POST" id="regForm" action="includes/operations.php">
                <div class="form-group">
                    <label class="control-label" for="full_name">Name</label>
                    <input type="text" class="form-control" name="full_name" id="full_name" data-validation-regexp="^[a-zA-Z ]{3,75}$" data-validation-error-msg="Please enter a valid name" data-validation="custom required">
                </div>
                <div class="form-group">
                    <label class="control-label">Gender</label>
                    <label class="control-label radio-inline">
                        <input type="radio" name="gender" value="Male" data-validation="required" data-validation-error-msg="Please select your gender" checked>Male</label>
                    <label class="control-label radio-inline">
                        <input type="radio" name="gender" value="Female">Female</label>
                </div>
                <div class="form-group">
                    <label for="email_id">Email</label>
                    <input type="email" class="form-control" id="email_id" placeholder="Enter email" name="email_id" data-validation-url="includes/operations.php" data-validation="server required">
                </div>
                <div class="form-group">
                    <label class="control-label" for="dob">Date Of Birth</label>
                    <input type="text" class="form-control" name="dob" id="dob" data-validation="required" data-validation-error-msg="Please choose your date of birth" readonly="readonly">
                </div>
                <div class="form-group">
                    <label class="control-label" for="mobile_no">Mobile Number (10 digit)</label>
                    <input type="text" class="form-control" name="mobile_no" id="mobile_no" data-validation-regexp="^[6-9]\d{9}$"  data-validation-error-msg="Please enter a valid 10-digit mobile number" data-validation-url="includes/operations.php" data-validation="custom server required">
                </div>
                <div class="form-group">
                    <label for="country">Country:</label>
                    <select name="country" onchange="getStates( this.value )" data-validation="required" data-validation-error-msg="Please select your country">
                        <option value="" selected disabled></option>
                        <?php
                        $country_extract = $db->getRows("countries");
                        foreach( $country_extract as $cek )
                        {
                            echo '<option value="' . $cek['id'] . '">' . $cek['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="state">State:</label>
                    <select name="state" id="state" onchange="getCities( this.value )" data-validation="required" data-validation-error-msg="Please select your state">
                        <option value="" selected disabled>Select State</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="City">City:</label>
                    <select name="city" id="city" data-validation="required" data-validation-error-msg="Please select your city">
                        <option value="" selected disabled>Select City</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label" for="pincode">Pincode</label>
                    <input type="text" class="form-control" name="pincode" id="pincode" placeholder="Enter your Pincode" data-validation-length="6-6"  data-validation-error-msg="Please enter a valid Pincode" data-validation="length number required">
                </div>
                <div class="form-group">
                    <label for="agreement">
                        <input type="checkbox" name="agreement" id="agreement" checked data-validation="required" data-validation-error-msg="You must agree to terms">
                        I agree to terms</label>
                </div>
                <input type="submit" class="btn btn-form" name="reg_submit" value="Submit" />
            </form>
        </div>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script> 
        <!-- Start Date Picker-->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!--        <script src="js/custom_script.js"></script>-->
        <script>
                function getStates( country_id )
                {
                    $.ajax( {
                        url: "includes/operations.php",
                        data: {
                            'ajax_country_id': country_id
                        },
                        type: 'POST',
                        success: function( data ) {
                            $( '#state' ).html( data );
                        }
                    } )
                }
                function getCities( state_id )
                {
                    $.ajax( {
                        url: "includes/operations.php",
                        data: {
                            'ajax_state_id': state_id
                        },
                        type: 'POST',
                        success: function( data ) {
                            $( '#city' ).html( data );
                        }
                    } )
                }
                $( function() {
                    $( "#dob" ).datepicker( {
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'dd-mm-yy',
                        yearRange: "-47:+0",
                        showMonthAfterYear: true,
                        maxDate: "-1D"
                    } );
                    /*Form validation intialization*/
                    $.validate( {
                        modules: 'security'
                    } );

                    $( '#regForm' ).submit( function() {
                        $( "#loader" ).fadeIn();
                    } );
                } );
        </script>
    </body>
</html>