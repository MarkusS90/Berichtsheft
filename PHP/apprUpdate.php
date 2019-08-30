<?php
include_once "helper.php";
include_once "soapClient.php";

if (isset($_POST['appr_update'])) {
    $params = array("report" => array(
        "Id" => $_POST["reportid"],
        "ApprenticeId" => $_SESSION['userid'],
        "Begin" => $_POST['date_begin'],
        "Comment" => $_POST['comment'],
        "VerifiedBy" => null,
        "Year" => $_POST['year'],
        "Days" => generateParamDays($dayNames),
    ));

    $client->UpdateReport($params);

    header('location: .');
}
