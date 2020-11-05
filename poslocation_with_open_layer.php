<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/css/ol.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/build/ol.js"></script>
    <link rel='icon' href='mappin.png' type='image/x-icon'/ >

    <meta charset="utf-8"/>
    <title>POS Location</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="poslocation.css">

  </head>
  <body>
    <div id="map" class="map"></div>

    <?php
            //Set your file path here
            $filePath = 'PosLocation_info_with_location.csv';
             
            // define two arrays for storing values
            $keys = array();
            $newArray = array();
             
            //PHP Function to convert CSV into array
            function convertCsvToArray($file, $delimiter) { 
              if (($handle = fopen($file, 'r')) !== FALSE) { 
                $i = 0; 
                while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) { 
                  for ($j = 0; $j < count($lineArray); $j++) { 
                    $arr[$i][$j] = $lineArray[$j]; 
                  } 
                  $i++; 
                } 
                fclose($handle); 
              } 
              return $arr; 
            } 
            // Call the function convert csv To Array
            $data = convertCsvToArray($filePath, ',');
             
            // Set number of elements (minus 1 because we shift off the first row)
            $count = count($data) - 1;
               
            //First row for label or name
            $labels = array_shift($data);  
            foreach ($labels as $label) {
              $keys[] = $label;
            }
             
            // assign keys value to ids, we add new parameter id here
            $keys[] = 'id';
            for ($i = 0; $i < $count; $i++) {
              $data[$i][] = $i;
            }
               
            // combine both array
            for ($j = 0; $j < $count; $j++) {
              $d = array_combine($keys, $data[$j]);
              $newArray[$j] = $d;
            }
             
            // convert array to json php using the json_encode()
            $arrayToJson = json_encode($newArray);
            // print converted csv value to json
            ;
            ?>
    <input hidden="hidden" type="text" name="poslocation-csv-data" id="poslocation-csv-data" value="<?php echo(htmlspecialchars($arrayToJson))?>">
        <div id="table-div" style="overflow-y: auto;overflow-x: hidden;height: 50vh">
            <br/>
            <br/>
            <input type="text" id="myInput" onkeyup="searchFromTableRow()" placeholder="Search for agent name..." title="Type in a name">
            <table id="myTable" class="table">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th style="width:20%;">Agent Name</th>
                        <th style="width:20%;">Owner Name</th>
                        <th style="width:30%;">Address</th>
                        <th style="width:15%;">Service Provider</th>
                        <th style="width:10%;">Contact</th>
                    </tr>
                </thead>
                <?php
                    for($i=0;$i<sizeof($newArray);$i++){
                        echo "<tr id='row-".$newArray[$i]['S/l']."'>";
                        echo "<td>".$newArray[$i]['S/l']."</td>";
                        echo "<td>".$newArray[$i]['Agent Name']."</td>";
                        echo "<td>".$newArray[$i]['Owner Name']."</td>";
                        echo "<td>".$newArray[$i]['Address']."</td>";
                        echo "<td>".$newArray[$i]['Service Provider']."</td>";
                        echo "<td>".$newArray[$i]['Contact No.']."</td>";
                        echo "</tr>";
                    }
                    ?>
            </table>
        </div>

    <script type="text/javascript">
      var map = new ol.Map({
        target: 'map',
        layers: [
          new ol.layer.Tile({
            source: new ol.source.OSM()
          })
        ],
        view: new ol.View({
          center: ol.proj.fromLonLat([90.4187231, 23.8284026]),
          zoom: 16
        })
      });

      var poses_with_lat_long = []
      function extract_pos_data() {
        var pos_info_json = $("#poslocation-csv-data").val();
        
        var poses = JSON.parse(pos_info_json)
        while(poses.length>0) {
          var pos = poses[0];
          if (pos['Latitude'] && pos['Longitude']) {
            if (!isNaN(pos['Latitude'])) {
                pos['Latitude'] = Number(pos['Latitude']);
            }
            if (!isNaN(pos['Longitude'])) {
                pos['Longitude'] = Number(pos['Longitude']);
            }
            poses_with_lat_long.push(pos);
          }
          poses.splice(0, 1);
        }
      }






      var layer = new ol.layer.Vector({
           source: new ol.source.Vector({
               features: [
                   new ol.Feature({
                       geometry: new ol.geom.Point(ol.proj.fromLonLat([90.4187231, 23.8284026]))
                   })
               ]
           })
       });
       map.addLayer(layer);



      function searchFromTableRow() {
             var input, filter, table, tr, td, i, txtValue;
             input = document.getElementById("myInput");
             filter = input.value.toUpperCase();
             table = document.getElementById("myTable");
             tr = table.getElementsByTagName("tr");
             for (i = 0; i < tr.length; i++) {
               td = tr[i].getElementsByTagName("td");
               if (td.length>0) {
                var matched = false;
                 for (var j=1; j < td.length; j++) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (!matched && txtValue.toUpperCase().indexOf(filter) > -1) {
                        matched = true
                    }
                 }
                 if (matched) {
                   tr[i].style.display = "";
                 } else {
                   tr[i].style.display = "none";
                 }
               }       
             }
           }
    </script>
  </body>
</html>

