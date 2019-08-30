<?php
session_start();
include_once "db.php";
include_once "helper.php";
include_once "soapClient.php";

include_once "header.php";
include_once "nav.php";
?>

  <div class="container">
    <br>
      <div class="form-group">
      <label for="sel1">Auszubildender:</label>
      <form action="ihk.php" method="get">
      <select name='apprenticeid' class="form-control" id="azubi">
      <?php
$sql = "SELECT id, username FROM user WHERE type = 'apprentice'";
foreach ($pdo->query($sql) as $row) {
    echo "<option id='apprenticeid' value='" . $row['id'] . "'>" . $row['username'] . "</option>";
}
?>
     </select>
      <br><button class="btn btn-info btn-block" type="submit">Nachweise ansehen</button>
      <br><button class="btn btn-info btn-block" type="button" data-toggle="modal" data-target="#activitiesmodal">Aktivitätenübersicht</button><br>
    </div>
    </form>
    <br>

    <?php
function genListElements($pdo, $sql)
{
    $result = $pdo->query($sql);

    if ($result) {
        foreach ($result as $row) {
            // $checked = isset($row['verifiedby']) ? "&nbsp;&nbsp;&nbsp;<span style='color:lime;font-weight:bold;font-size:16pt'>&#x2713;</span>" : "";
            $checked = isset($row['verifiedby']) ? '&nbsp;&nbsp;&nbsp; Genehmigt durch: ' . $row['username'] . (isset($row['name']) ? (" (" . $row['name'] . ")") : "") : "";
            echo '<div class="list-group">';

            printf('<a class="list-group-item">
        Nachweis vom %s bis %s %s
        <button data-toggle="modal" data-target="#updatemodal%u" class="btn btn-secondary float-sm-right">Ansehen</button>
        <span class="float-sm-right">&nbsp</span>
      </a>', getStringDate($row['begin']), getStringDate($row['end']), $checked, $row['id']);
            echo '</div>';
        }
    }
}

if (isset($_GET['apprenticeid'])) {

    echo "<h5>Ausbildungsjahr 1</h5>";
    $sql = "SELECT user.username, report.id, report.begin, report.end, report.verifiedby, institution.name FROM report
      LEFT JOIN user ON report.verifiedby = user.id
      LEFT JOIN institution ON user.institutionid = institution.id
      WHERE report.verifiedby IS NOT NULL AND report.year = 1 AND report.apprenticeid = " . $_GET['apprenticeid'];
    genListElements($pdo, $sql);

    echo "<h5>Ausbildungsjahr 2</h5>";
    $sql = "SELECT id, [begin], [end], verifiedby FROM report WHERE year = 2 AND apprenticeid = " . $_GET['apprenticeid'];
    genListElements($pdo, $sql);

    echo "<h5>Ausbildungsjahr 3</h5>";
    $sql = "SELECT id, [begin], [end], verifiedby FROM report WHERE year = 3 AND apprenticeid = " . $_GET['apprenticeid'];
    genListElements($pdo, $sql);
}
?>
 </div>

  <?php

$result = $client->GetReports(array(
    "apprenticeId" => $_GET['apprenticeid'],
));
$result2 = $result->GetReportsResult;
?>
</div>

</div>
<?php

foreach ($result2->Report as $report) {
    printf('<div class="modal" id="updatemodal%u">
<div class="modal-dialog ">
<div class="modal-content">

  <!-- Modal Header -->
  <div class="modal-header">
    <h4 class="modal-title">Nachweis vom %s bis %s</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>

  <!-- Modal body -->
  <div class="modal-body">
    <form action="apprentice.php" method="post">
      <div class="row">
        <div class="col-sm-5">von: <input type="date" class="form-control" id="date_begin" name="date_begin" value="%s" readonly></div>
        <div class="col-sm-5">bis: <input type="date" class="form-control" id="date_end" name="date_end" value="%s" readonly></div>
        <div class="col-sm-2">Jahr: <input type="number" step="1" class="form-control" id="year" name="year" value="%s" readonly></div>', $report->Id, getStringDate($report->Begin), getStringDate($report->End), getFormDate($report->Begin), getFormDate($report->End), $report->Year);

    $days = $report->Days->Day;

    foreach ($days as $d) {
        printf('<div class="col-sm-12">
    <h5>%s</h5>
  </div>', $dayNames[$d->DayOfWeek][1]);

        $act = $d->Activities->Activity;

        for ($a = 0; $a < 4; $a++) {
            printf('<div class="col-sm-10"><input type="text" class="form-control" id="%2$s_t%1$u" name="%2$s_t%1$u" placeholder="Tätigkeit" value="%3$s"  readonly></div>
  <div class="col-sm-2"><input type="number" step="1" class="form-control" id="%2$s_d%1$u" name="%2$s_d%1$u" placeholder="Dauer" value="%4$s"  readonly></div>', $a + 1, $dayNames[$d->DayOfWeek][0], $act[$a]->Caption, $act[$a]->Duration);
        }
    }

    echo "<div class='col-sm-10'></div><div class='col-sm-2'>Gesamt:&nbsp;" . $client->GetTotalDurationOfActivities(array("reportId" => $report->Id))->GetTotalDurationOfActivitiesResult . "</div>";
    printf('</div>
</form>
</div>

</div>
<!-- Modal footer -->
<div class="modal-footer">
</div>
</div>
</div>');
}
?>
  <div class="modal" id="activitiesmodal">
    <div class="modal-dialog ">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Übersicht</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <?php
$activities = $client->GroupActivitiesByCaption(array("apprenticeId" => $_GET["apprenticeid"]));
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
?>
        </div>

        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>

</body>

</html>