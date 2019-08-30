<?php
session_start();
include_once "db.php";
include_once "helper.php";
include_once "soapClient.php";

include_once "apprInsert.php";
include_once "apprUpdate.php";

include_once "header.php";
include_once "nav.php";
?>

  <div class="container">
    <br><button class="btn btn-info btn-block" data-toggle="modal" data-target="#insertmodal">Neuer Nachweis</button><br>
    <br><button class="btn btn-info btn-block" data-toggle="modal" data-target="#activitiesmodal">Aktivitätenübersicht</button><br>
    <div class="list-group">
      <?php
$result = $client->GetReports(array("apprenticeId" => $_SESSION['userid']));
$result2 = $result->GetReportsResult;
foreach ($result2->Report as $report) {
    printf('<a class="list-group-item">Nachweis vom %s bis %s %s <span %s>%s</span> <button data-toggle="modal" data-target="#updatemodal%u" class="btn btn-secondary float-sm-right">Bearbeiten</button><span class="float-sm-right">&nbsp</span></a>', getStringDate($report->Begin), getStringDate($report->End), isset($report->VerifiedBy) ? "&nbsp;<span style='color:lime;font-weight:bold;font-size:16pt'>&#x2713;</span>" : "", (isset($report->Comment) && $report->Comment != "") ? "data-toggle='tooltip' title='" . $report->Comment . "'" : "", (isset($report->Comment) && $report->Comment != "") ? "&nbsp;&#x1f4ac;" : "", $report->Id);
}
?>
    </div>

  </div>
  <?php
foreach ($result2->Report as $report) {
    printf(
        '<div class="modal" id="updatemodal%1$u">
    <div class="modal-dialog ">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Nachweis vom %2$s bis %3$s</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form action="apprentice.php" method="post">
          <input type="hidden" name="comment" value="%7$s" />
          <input type="hidden" value="%1$u" name="reportid">
            <div class="row">
              <div class="col-sm-5">von: <input type="date" class="form-control" id="date_begin" name="date_begin" value="%4$s" required></div>
              <div class="col-sm-5">bis: <input type="date" class="form-control" id="date_end" name="date_end" readonly value="%5$s"></div>
              <div class="col-sm-2">Jahr: <input type="number" step="1" class="form-control" id="year" name="year" value="%6$s" required></div>',
        $report->Id,
        getStringDate($report->Begin),
        getStringDate($report->End),
        getFormDate($report->Begin),
        getFormDate($report->End),
        $report->Year,
        $report->Comment
    );

    $days = $report->Days->Day;

    foreach ($days as $d) {
        printf('<div class="col-sm-12">
          <h5>%s</h5>
        </div>', $dayNames[$d->DayOfWeek][1]);

        $act = $d->Activities->Activity;

        for ($a = 0; $a < 4; $a++) {
            printf(
                '<div class="col-sm-10"><input type="text" class="form-control" id="%2$s_t%1$u" name="%2$s_t%1$u" placeholder="Tätigkeit" value="%3$s"></div>
        <div class="col-sm-2"><input type="number" step="1" class="form-control" id="%2$s_d%1$u" name="%2$s_d%1$u" placeholder="Dauer" value="%4$s"></div>',
                $a + 1,
                $dayNames[$d->DayOfWeek][0],
                $act[$a]->Caption,
                $act[$a]->Duration
            );
        }
    }

    printf("Kommentar: <textarea readonly class='form-control' name='comment'>%s</textarea>", $report->Comment);
    printf("Bestätigt:&nbsp;<input disabled type='checkbox' name='verified' %s />", isset($report->VerifiedBy) ? "checked" : "");

    printf('</div>
    </div>
    <!-- Modal footer -->
    <div class="modal-footer">
    <button name="appr_update" type="submit" class="btn btn-primary">Speichern</button>
    </div>
    </form>
    </div>
    </div>
    </div>');
}
?>

  <!-- The Modal -->
  <div class="modal" id="insertmodal">
    <div class="modal-dialog ">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Neuer Nachweis</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form action="apprentice.php" method="post">
            <div class="row">
              <div class="col-sm-10">von: <input type="date" class="form-control" id="date_begin" name="date_begin" required></div>
              <!-- <div class="col-sm-5">bis: <input type="date" class="form-control" id="date_end" name="date_end"></div> -->
              <div class="col-sm-2">Jahr: <input type="number" step="1" class="form-control" id="year" name="year" required></div>

<?php
foreach ($dayNames as $dayName) {
    printf('<div class="col-sm-12">
    <h5>%s</h5>
  </div>', $dayName[1]);

    for ($a = 0; $a < 4; $a++) {
        printf(
            '<div class="col-sm-10"><input type="text" class="form-control" id="%2$s_t%1$u" name="%2$s_t%1$u" placeholder="Tätigkeit"></div>
            <div class="col-sm-2"><input type="number" step="1" class="form-control" id="%2$s_d%1$u" name="%2$s_d%1$u" placeholder="Dauer"></div>',
            $a + 1,
            $dayName[0]
        );
    }
}
?>
            </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
            <button name="appr_insert" type="submit" class="btn btn-primary">Speichern</button>
        </form>
    </div>
    </div>
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
$activities = $client->GroupActivitiesByCaption(array("apprenticeId" => $_SESSION["userid"]));
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

  <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
</body>

</html>