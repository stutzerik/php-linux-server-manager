<?php
   //Erőforrás statisztika 
   
   session_start();
   if(!isset($_SESSION['username']))
   {
      header('Location: /login');
      exit();
   }
   
  require '../config/components.php';
  require '../src/system.class.php';
   
  $db = new DBconnect();
  $system = new System();

  //Tűzfal aktiváció
  require '../includes/integrated_fw.php';

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Stats']; ?></title>
   </head>
   <body class="bg-light">
      <?php include '../includes/navbar.php'; ?>
      <div class="row">
         <div class="col-sm-3 py-5">
            <?php include '../includes/sidebar.php'; ?>
         </div>
         <div class="col-sm-9 container-fluid py-5">
            <div class="card w-100">
               <div class="card-body py-5">
                  <h3>
                     <i class="fas fa-signal"></i> 
                     <?php echo $lang['Stats']; ?>
                  </h3>
                  <div class="row">
                     <div class="col-sm-4 py-3">
                        <h6 class="text-center py-3"><i class="fas fa-bolt"></i> <?php echo $lang['CPUUsage']; ?></h6>
                        <div id="cpuUsage"></div>
                     </div>
                     <div class="col-sm-4 py-3">
                        <h6 class="text-center py-3"><i class="fas fa-memory"></i> <?php echo $lang['MemUsage']; ?></h6>
                        <div id="memUsage"></div>
                     </div>
                     <div class="col-sm-4 py-3">
                        <h6 class="text-center py-3">
                           <i class="fas fa-hdd"></i> 
                           <?php echo $lang['DiskUsage']; ?>
                        </h6>
                        <div id="diskUsage"></div>
                     </div>
                  </div>
                  <div class="container py-3">
                     <ul class="list-group">
                        <li class="list-group-item">
                          <span class="float-left d-inline">
                          <?php echo $lang['CPUModel']; ?>
                          <br>
                           (<?php
                              $ncpu = 1;
                              if(is_file('/proc/cpuinfo')) 
                              {
                                $cpuinfo = file_get_contents('/proc/cpuinfo');
                                preg_match_all('/^processor/m', $cpuinfo, $matches);
                                $ncpu = count($matches[0]);
                              }
                            
                              echo $ncpu;
                              echo $lang['Cores'];
                            ?>)
                           </span>
                           <span class="float-right d-inline">
                           <?php 
                              $cpu_model = file('/proc/cpuinfo');
                              $proc_details = $cpu_model[4];
                              if (str_contains($proc_details, 'Intel'))
                              {
                                echo "<img src='/theme/img/intel.png'>";
                              }
                              elseif (str_contains($proc_details, 'Amd') OR str_contains($proc_details, 'AMD')) 
                              {
                                echo "<img src='/theme/img/amd.png'>";
                              }
                              else
                              {
                                echo "N/A";
                              }
                              ?>
                           </span>
                        </li>
                        <li class="list-group-item">
                           <span class="float-left d-inline">
                           <?php echo $lang['Uptime']; ?>
                           </span>
                           <span class="float-right d-inline">
                           <?php 
                              $str = file_get_contents('/proc/uptime');
                              $num = floatval($str);
                              $secs = fmod($num, 60); 
                              $num = intdiv($num, 60);
                              $mins = $num % 60;      
                              $num = intdiv($num, 60);
                              $hours = $num % 24;      
                              $num = intdiv($num, 24);
                              $days = $num;
                              echo $days; echo $lang['Days'];
                            ?>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
         var options = {
           series: [<?php $system->cpu_usage($usage); ?>],
           chart: {
           type: 'radialBar',
           offsetY: -20,
           sparkline: {
             enabled: true
           }
         },
         plotOptions: {
           radialBar: {
             startAngle: -90,
             endAngle: 90,
             track: {
               background: "#e7e7e7",
               strokeWidth: '97%',
               margin: 5, 
               dropShadow: {
                 enabled: true,
                 top: 2,
                 left: 0,
                 color: '#999',
                 opacity: 1,
                 blur: 2
               }
             },
             dataLabels: {
               name: {
                 show: false
               },
               value: {
                 offsetY: -2,
                 fontSize: '22px'
               }
             }
           }
         },
         grid: {
           padding: {
             top: -10
           }
         },
         fill: {
           type: 'gradient',
           gradient: {
             shade: 'light',
             shadeIntensity: 0.4,
             inverseColors: false,
             opacityFrom: 1,
             opacityTo: 1,
             stops: [0, 50, 53, 91]
           },
         },
         labels: ['CPU'],
         };
         var chart = new ApexCharts(document.querySelector("#cpuUsage"), options);
         chart.render();
         
         var options = {
           series: [<?php $system->mem_usage($usage); ?>],
           chart: {
           type: 'radialBar',
           offsetY: -20,
           sparkline: {
             enabled: true
           }
         },
         plotOptions: {
           radialBar: {
             startAngle: -90,
             endAngle: 90,
             track: {
               background: "#e7e7e7",
               strokeWidth: '97%',
               margin: 5, 
               dropShadow: {
                 enabled: true,
                 top: 2,
                 left: 0,
                 color: '#999',
                 opacity: 1,
                 blur: 2
               }
             },
             dataLabels: {
               name: {
                 show: false
               },
               value: {
                 offsetY: -2,
                 fontSize: '22px'
               }
             }
           }
         },
         grid: {
           padding: {
             top: -10
           }
         },
         fill: {
           type: 'gradient',
           gradient: {
             shade: 'light',
             shadeIntensity: 0.4,
             inverseColors: false,
             opacityFrom: 1,
             opacityTo: 1,
             stops: [0, 50, 53, 91]
           },
         },
         labels: ['Memória'],
         };
         var chart = new ApexCharts(document.querySelector("#memUsage"), options);
         chart.render();
         
         var options = {
           series: [<?php $system->disk_usage($usage); ?>, 100-<?php $system->cpu_usage($usage); ?>],
           chart: {
           width: 300,
           type: 'pie',
         },
         labels: ['Foglalt', 'Szabad'],
         responsive: [{
           breakpoint: 480,
           options: {
             chart: {
               width: 200
             },
             legend: {
               position: 'bottom'
             }
           }
         }]
         };
         
         var chart = new ApexCharts(document.querySelector("#diskUsage"), options);
         chart.render();
      </script>
      </script>
   </body>
</html>
<?php $db->close(); ?>