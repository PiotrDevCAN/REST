<?php

use itdq\BlueMail;

if(!empty($_POST['feedback'])){
    BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'REST Question', $_POST['feedback'],$_POST['sender']);
}
ob_clean();

