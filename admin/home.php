<?php include 'includes/session.php'; ?>
<?php include 'includes/slugify.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <?php
        $settings = get_election_settings($conn);
        $statusLabel = array('draft' => 'Not Started', 'open' => 'Open', 'closed' => 'Closed');
        $statusClass = ($settings['status'] == 'open') ? 'callout-success' : (($settings['status'] == 'draft') ? 'callout-info' : 'callout-warning');
        $totalVotersQuery = $conn->query("SELECT COUNT(*) AS total FROM voters");
        $totalVoters = (int) $totalVotersQuery->fetch_assoc()['total'];
        $votersVotedQuery = $conn->query("SELECT COUNT(DISTINCT voters_id) AS total FROM votes");
        $votersVoted = (int) $votersVotedQuery->fetch_assoc()['total'];
        $turnout = $totalVoters > 0 ? round(($votersVoted / $totalVoters) * 100, 1) : 0;
        $recentQuery = $conn->query("SELECT COUNT(DISTINCT voters_id) AS total FROM votes WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $recentVoters = (int) $recentQuery->fetch_assoc()['total'];
      ?>
      <div class="callout <?php echo $statusClass; ?>">
        <h4><?php echo e($settings['title']); ?></h4>
        <p>Status: <b><?php echo e($statusLabel[$settings['status']] ?? 'Closed'); ?></b>
        <?php if(!empty($settings['start_at'])){ echo ' | Starts: '.e(date('M d, Y h:i A', strtotime($settings['start_at']))); } ?>
        <?php if(!empty($settings['end_at'])){ echo ' | Ends: '.e(date('M d, Y h:i A', strtotime($settings['end_at']))); } ?>
        <span class="pull-right"><a href="#config" data-toggle="modal" class="btn btn-default btn-sm btn-flat"><i class="fa fa-cog"></i> Settings</a></span></p>
        <form method="POST" action="election_status.php" class="form-inline">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-success btn-sm btn-flat" name="set_status" value="1" onclick="this.form.status.value='open';"><i class="fa fa-play"></i> Open Election</button>
          <button type="submit" class="btn btn-warning btn-sm btn-flat" name="set_status" value="1" onclick="this.form.status.value='closed';"><i class="fa fa-lock"></i> Close Election</button>
          <button type="submit" class="btn btn-info btn-sm btn-flat" name="set_status" value="1" onclick="this.form.status.value='draft';"><i class="fa fa-pause"></i> Not Started</button>
          <a href="print.php" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-print"></i> Print Results</a>
          <input type="hidden" name="status" value="<?php echo e($settings['status']); ?>">
        </form>
      </div>
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <?php
                $sql = "SELECT * FROM positions";
                $query = $conn->query($sql);

                echo "<h3>".$query->num_rows."</h3>";
              ?>

              <p>Leadership Positions</p>
            </div>
            <div class="icon">
              <i class="fa fa-tasks"></i>
            </div>
            <a href="positions.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <?php
                $sql = "SELECT * FROM candidates";
                $query = $conn->query($sql);

                echo "<h3>".$query->num_rows."</h3>";
              ?>
          
              <p>Candidate Students</p>
            </div>
            <div class="icon">
              <i class="fa fa-black-tie"></i>
            </div>
            <a href="candidates.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <?php
                echo "<h3>".$totalVoters."</h3>";
              ?>
             
              <p>Registered Students</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="voters.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <?php
                echo "<h3>".$votersVoted."</h3>";
              ?>

              <p>Students Voted</p>
            </div>
            <div class="icon">
              <i class="fa fa-edit"></i>
            </div>
            <a href="votes.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>

      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?php echo e($turnout); ?><sup style="font-size:20px">%</sup></h3>
              <p>Turnout</p>
            </div>
            <div class="icon">
              <i class="fa fa-line-chart"></i>
            </div>
            <a href="votes.php" class="small-box-footer">View votes <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box <?php echo ($settings['status'] == 'open') ? 'bg-green' : 'bg-gray'; ?>">
            <div class="inner">
              <h3 style="font-size:28px"><?php echo e($statusLabel[$settings['status']] ?? 'Closed'); ?></h3>
              <p>Election Status</p>
            </div>
            <div class="icon">
              <i class="fa fa-power-off"></i>
            </div>
            <a href="#config" data-toggle="modal" class="small-box-footer">Manage status <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-teal">
            <div class="inner">
              <h3><?php echo max($totalVoters - $votersVoted, 0); ?></h3>
              <p>Not Voted</p>
            </div>
            <div class="icon">
              <i class="fa fa-user-times"></i>
            </div>
            <a href="voters.php" class="small-box-footer">View voters <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-maroon">
            <div class="inner">
              <h3><?php echo $recentVoters; ?></h3>
              <p>Voted In Last 24h</p>
            </div>
            <div class="icon">
              <i class="fa fa-clock-o"></i>
            </div>
            <a href="votes.php" class="small-box-footer">View vote log <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-7">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><b>Class And Stream Turnout</b></h3>
            </div>
            <div class="box-body table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Class</th>
                    <th>Stream</th>
                    <th class="text-center">Voters</th>
                    <th class="text-center">Voted</th>
                    <th class="text-center">Turnout</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT COALESCE(NULLIF(voters.class, ''), 'Unassigned') AS class_name, COALESCE(NULLIF(voters.stream, ''), 'Unassigned') AS stream_name, COUNT(voters.id) AS total_voters, COUNT(DISTINCT votes.voters_id) AS voters_voted FROM voters LEFT JOIN votes ON votes.voters_id = voters.id GROUP BY class_name, stream_name ORDER BY class_name ASC, stream_name ASC";
                    $query = $conn->query($sql);
                    if($query->num_rows == 0){
                      echo "<tr><td colspan='5' class='text-center'>No voters found.</td></tr>";
                    }
                    while($row = $query->fetch_assoc()){
                      $classTotal = (int) $row['total_voters'];
                      $classVoted = (int) $row['voters_voted'];
                      $classTurnout = $classTotal > 0 ? round(($classVoted / $classTotal) * 100, 1) : 0;
                      echo "
                        <tr>
                          <td>".e($row['class_name'])."</td>
                          <td>".e($row['stream_name'])."</td>
                          <td class='text-center'>".$classTotal."</td>
                          <td class='text-center'>".$classVoted."</td>
                          <td class='text-center'>".$classTurnout."%</td>
                        </tr>
                      ";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><b>Quick Actions</b></h3>
            </div>
            <div class="box-body">
              <a href="voters.php" class="btn btn-app"><i class="fa fa-users"></i> Students</a>
              <a href="candidates.php" class="btn btn-app"><i class="fa fa-black-tie"></i> Candidates</a>
              <a href="positions.php" class="btn btn-app"><i class="fa fa-tasks"></i> Leadership</a>
              <a href="ballot.php" class="btn btn-app"><i class="fa fa-file-text"></i> Ballot</a>
              <a href="print.php" class="btn btn-app"><i class="fa fa-print"></i> Print</a>
            </div>
          </div>
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><b>Current Leaders</b></h3>
            </div>
            <div class="box-body table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Position</th>
                    <th>Candidate</th>
                    <th class="text-center">Votes</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $positions = $conn->query("SELECT * FROM positions ORDER BY priority ASC");
                    while($position = $positions->fetch_assoc()){
                      $positionId = (int) $position['id'];
                      $stmt = $conn->prepare("SELECT candidates.firstname, candidates.lastname, COUNT(votes.id) AS total_votes FROM candidates LEFT JOIN votes ON votes.candidate_id = candidates.id WHERE candidates.position_id = ? GROUP BY candidates.id ORDER BY total_votes DESC, candidates.lastname ASC LIMIT 1");
                      $stmt->bind_param("i", $positionId);
                      $stmt->execute();
                      $leader = $stmt->get_result()->fetch_assoc();
                      $stmt->close();

                      if($leader){
                        echo "
                          <tr>
                            <td>".e($position['description'])."</td>
                            <td>".e($leader['firstname'].' '.$leader['lastname'])."</td>
                            <td class='text-center'>".(int) $leader['total_votes']."</td>
                          </tr>
                        ";
                      }
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <h3>Rock High School Election Results
            <span class="pull-right">
              <a href="print.php" class="btn btn-success btn-sm btn-flat"><span class="glyphicon glyphicon-print"></span> Print</a>
            </span>
          </h3>
        </div>
      </div>

      <?php
        $sql = "SELECT * FROM positions ORDER BY priority ASC";
        $query = $conn->query($sql);
        $inc = 2;
        while($row = $query->fetch_assoc()){
          $inc = ($inc == 2) ? 1 : $inc+1; 
          if($inc == 1) echo "<div class='row'>";
          echo "
            <div class='col-sm-6'>
              <div class='box box-solid'>
                <div class='box-header with-border'>
                  <h4 class='box-title'><b>".$row['description']."</b></h4>
                </div>
                <div class='box-body'>
                  <div class='chart'>
                    <canvas id='".slugify($row['description'])."' style='height:200px'></canvas>
                  </div>
                </div>
              </div>
            </div>
          ";
          if($inc == 2) echo "</div>";  
        }
        if($inc == 1) echo "<div class='col-sm-6'></div></div>";
      ?>

      </section>
      <!-- right col -->
    </div>
  	<?php include 'includes/footer.php'; ?>

</div>
<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<?php
  $sql = "SELECT * FROM positions ORDER BY priority ASC";
  $query = $conn->query($sql);
  while($row = $query->fetch_assoc()){
    $sql = "SELECT * FROM candidates WHERE position_id = '".$row['id']."'";
    $cquery = $conn->query($sql);
    $carray = array();
    $varray = array();
    while($crow = $cquery->fetch_assoc()){
      array_push($carray, $crow['lastname']);
      $sql = "SELECT * FROM votes WHERE candidate_id = '".$crow['id']."'";
      $vquery = $conn->query($sql);
      array_push($varray, $vquery->num_rows);
    }
    $carray = json_encode($carray);
    $varray = json_encode($varray);
    ?>
    <script>
    $(function(){
      var rowid = '<?php echo $row['id']; ?>';
      var description = '<?php echo slugify($row['description']); ?>';
      var barChartCanvas = $('#'+description).get(0).getContext('2d')
      var barChart = new Chart(barChartCanvas)
      var barChartData = {
        labels  : <?php echo $carray; ?>,
        datasets: [
          {
            label               : 'Votes',
            fillColor           : 'rgba(60,141,188,0.9)',
            strokeColor         : 'rgba(60,141,188,0.8)',
            pointColor          : '#3b8bba',
            pointStrokeColor    : 'rgba(60,141,188,1)',
            pointHighlightFill  : '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data                : <?php echo $varray; ?>
          }
        ]
      }
      var barChartOptions                  = {
        //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
        scaleBeginAtZero        : true,
        //Boolean - Whether grid lines are shown across the chart
        scaleShowGridLines      : true,
        //String - Colour of the grid lines
        scaleGridLineColor      : 'rgba(0,0,0,.05)',
        //Number - Width of the grid lines
        scaleGridLineWidth      : 1,
        //Boolean - Whether to show horizontal lines (except X axis)
        scaleShowHorizontalLines: true,
        //Boolean - Whether to show vertical lines (except Y axis)
        scaleShowVerticalLines  : true,
        //Boolean - If there is a stroke on each bar
        barShowStroke           : true,
        //Number - Pixel width of the bar stroke
        barStrokeWidth          : 2,
        //Number - Spacing between each of the X value sets
        barValueSpacing         : 5,
        //Number - Spacing between data sets within X values
        barDatasetSpacing       : 1,
        //String - A legend template
        legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
        //Boolean - whether to make the chart responsive
        responsive              : true,
        maintainAspectRatio     : true
      }

      barChartOptions.datasetFill = false
      var myChart = barChart.HorizontalBar(barChartData, barChartOptions)
      //document.getElementById('legend_'+rowid).innerHTML = myChart.generateLegend();
    });
    </script>
    <?php
  }
?>
</body>
</html>
