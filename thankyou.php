<?php
session_start();
if( !isset($_SESSION['action_status']) )
    header("location:registration.php");
if( $_SESSION['action_status'] === "success" ):
    ?>
    <!--html comes here-->
    <?php
else:
    header("location:registration.php?msg=failed");
endif;
session_unset();
session_destroy();
?>
