<?php
include_once "helper.php";
include_once "soapClient.php";

if (isset($_POST['appr_insert'])) {
    $params = array("report" => array(
        "ApprenticeId" => $_SESSION['userid'],
        "Begin" => $_POST['date_begin'],
        "Comment" => "",
        "VerifiedBy" => null,
        "Year" => $_POST['year'],
        "Days" => generateParamDays($dayNames),
    ));

    $client->InsertReport($params);

    header('location: .');
}
