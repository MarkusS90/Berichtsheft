<?php
$options = array(
    'cache_wsdl' => 0,
    'trace' => 1,
    'stream_context' => stream_context_create(array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ),
    )),
);

$client = new SoapClient("http://localhost:65485/ReportService.svc?singleWsdl", $options);
