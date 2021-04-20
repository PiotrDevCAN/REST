<?php

use itdq\BlueMail;

if(!empty($_POST['feedback'])){
    BlueMail::send_mail(array('piotr.tajanowicz@ibm.com'), 'REST Question', $_POST['feedback'],$_POST['sender']);
}
ob_clean();

