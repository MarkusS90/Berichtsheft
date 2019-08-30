<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
$dayNames = array(
    "Monday" => array(
        "mo",
        "Montag",
    ),
    "Tuesday" => array(
        "di",
        "Dienstag",
    ),
    "Wednesday" => array(
        "mi",
        "Mittwoch",
    ),
    "Thursday" => array(
        "do",
        "Donnerstag",
    ),
    "Friday" => array(
        "fr",
        "Freitag",
    ),
);

function getStringDate($date)
{
    return date("d.m.Y", strtotime($date));
}

function getFormDate($date)
{
    return date("Y-m-d", strtotime($date));
}

function getValue($input)
{
    return $input == null ? "0" : $input;
}

function getCaptionValue($input)
{
    return $input == null ? "" : $input;
}

function generateParamDays($dayNames)
{
    $result = array();
    foreach ($dayNames as $key => $value) {
        $day = array();
        $day["DayOfWeek"] = $key;
        $activities = array();
        for ($a = 1; $a <= 4; $a++) {
            array_push(
                $activities,
                array(
                    "Caption" => getCaptionValue(
                        $_POST[$value[0] . "_t" . $a]
                    ),
                    "Duration" => getValue(
                        $_POST[$value[0] . "_d" . $a]
                    ),
                )
            );
        }

        $day["Activities"] = $activities;
        array_push($result, $day);
    }

    return $result;
}
