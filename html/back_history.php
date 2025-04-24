<?php
include_once 'utilities.php';
//print_calling_stack();

$back_url = pop_calling_url();

send_redirect($back_url);
exit();
?>