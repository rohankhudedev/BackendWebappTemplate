<?php

ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", "my-errors.log");

define('G_DB_HOST', "localhost");
define('G_DB_USER', "root");
define('G_DB_NAME', "other");
define('G_DB_PASSWORD', "");

define('G_REG_TABLE', 'basic_user_info');
//aw server
//define('G_DB_HOST',"localhost");
//define('G_DB_USER',"autumn_rohan");
//define('G_DB_NAME',"autumn_economic_times");
//define('G_DB_PASSWORD',"cta4cta4");

define("G_REG_SUCCESS_MSG", "Thank you for registering with us. A verification link has been sent to your email. <br><br>Please also check your spam/junk folder for this email.<br><br>Once your email id has been verified, you will get an email with the Phase 1 Test link.");
define("G_CONTACT_US_SUB", "We have received your message");

//SMTP
//define("G_SMTP_HOST", "smtp.autumnworldwide.com");
//define("G_SMTP_PORT", "2525");
//define("G_SMTP_USER", "rkhude@autumnworldwide.com");
//define("G_SMTP_PWD", "rrr@1234");
//define("G_SMTP_FROM", "no-reply@timesgroup.com");
?>
