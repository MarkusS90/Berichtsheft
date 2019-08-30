<?php
session_start();
include_once "db.php";
include_once "helper.php";
include_once "soapClient.php";

include_once "apprUpdate.php";

include_once "header.php";
include_once "nav.php";
?>

  <div class="container">
    <br/>
    <div class="list-group">
    <?php
$result = $client->GetApprenticesOfInstructor(array("instructorId" => $_SESSION["userid"]));
$result2 = $result->GetApprenticesOfInstructorResult;
echo "<form action='instructor.php'>";
echo "<select class='form-control' name='apprenticeSelect'>";

foreach ($result2->User as $user) {
    printf("<option id='apprenticeSelect' value='%s'>%s</option>", $user->Id, $user->Name);
}

echo "</select>";
echo "<br/><button class='btn btn-info btn-block'>Berichte einsehen</button><br/>";
echo "</form>";
?>
<br><button class="btn btn-info btn-block" type="button" data-toggle="modal" data-target="#activitiesmodal">Aktivitätenübersicht</button><br>

    <?php
$result2_nv = null;

if (isset($_GET["apprenticeSelect"])) {
    $result_nv = $client->GetReports(array("apprenticeId" => $_GET["apprenticeSelect"]));

    if (isset($result_nv->GetReportsResult)) {
        $result2_nv = $result_nv->GetReportsResult;

        foreach ($result2_nv->Report as $report) {
            printf('<a class="list-group-item">Nachweis vom %s bis %s %s<button data-toggle="modal" data-target="#updatemodal_i%u" class="btn btn-secondary float-sm-right">Prüfen</button><span class="float-sm-right">&nbsp</span></a>', getStringDate($report->Begin), getStringDate($report->End), isset($report->VerifiedBy) ? "&nbsp;<span style='color:lime;font-weight:bold;font-size:16pt'>&#x2713;</span>" : "", $report->Id);
        }

    }
}
?>

<?php
if (isset($result_nv)) {
    foreach ($result2_nv->Report as $report) {
        printf(
            '<div class="modal" id="updatemodal_i%1$u">
            <div class="modal-dialog ">
            <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
            <h4 class="modal-title">Nachweis vom %2$s bis %3$s</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
            <form action="instructor.php" method="post">
            <input type="hidden" value="%1$u" name="reportid"/>
            <input type="hidden" value="%7$u" name="apprenticeid"/>
            <div class="row">
            <div class="col-sm-5">von: <input readonly type="date" class="form-control" id="date_begin" name="date_begin" value="%4$s"></div>
            <div class="col-sm-5">bis: <input readonly type="date" class="form-control" id="date_end" name="date_end" value="%5$s"></div>
            <div class="col-sm-2">Jahr: <input readonly type="number" step="1" class="form-control" id="year" name="year" value="%6$s"></div>',
            $report->Id,
            getStringDate($report->Begin),
            getStringDate($report->End),
            getFormDate($report->Begin),
            getFormDate($report->End),
            $report->Year,
            $report->ApprenticeId
        );

        $days = $report->Days->Day;

        foreach ($days as $d) {
            printf('<div class="col-sm-12">
        <h5>%s</h5>
        </div>', $dayNames[$d->DayOfWeek][1]);

            $act = $d->Activities->Activity;

            for ($a = 0; $a < 4; $a++) {
                printf(
                    '<div class="col-sm-10"><input readonly type="text" class="form-control" id="%2$s_t%1$u" name="%2$s_t%1$u" placeholder="Tätigkeit" value="%3$s"></div>
                <div class="col-sm-2"><input readonly type="number" step="1" class="form-control" id="%2$s_d%1$u" name="%2$s_d%1$u" placeholder="Dauer" value="%4$s"></div>',
                    $a + 1,
                    $dayNames[$d->DayOfWeek][0],
                    $act[$a]->Caption,
                    $act[$a]->Duration
                );
            }
        }

        echo "<div class='col-sm-10'></div><div class='col-sm-2'>Gesamt:&nbsp;" . $client->GetTotalDurationOfActivities(array("reportId" => $report->Id))->GetTotalDurationOfActivitiesResult . "</div>";
        echo "<br/>Kommentar: ";
        printf("<textarea class='form-control' name='comment'>%s</textarea>", $report->Comment);
        printf("Bestätigt:&nbsp;<input type='checkbox' name='verified' %s />", isset($report->VerifiedBy) ? "checked" : "");

        printf('</div>
    </div>
    <!-- Modal footer -->
    <div class="modal-footer">
    <button name="appr_update_i" type="submit" class="btn btn-primary">Speichern</button>
    </div>
    </form>
    </div>
    </div>
    </div>');
    }
}

?>
</div>
</div>

<div class="modal" id="activitiesmodal">
    <div class="modal-dialog ">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Übersicht</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <?php

if (isset($_GET["apprenticeSelect"])) {
    $activities = $client->GroupActivitiesByCaption(array("apprenticeId" => $_GET["apprenticeSelect"]));
    $activities2 = $activities->GroupActivitiesByCaptionResult;

    echo "<table style='width: 100%' border='1'>";
    echo "<thead>";
    echo "<tr>
              <td><b>Aktivität</b></td>
              <td><b>Gesamtdauer</b></td>
              </tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($activities2->Activity as $act) {
        echo "<tr>";
        echo "<td>";
        echo $act->Caption;
        echo "</td>";
        echo "<td style='text-align:right'>";
        echo $act->Duration;
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}
?>
        </div>

        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>

</body>
</html>