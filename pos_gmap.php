<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <link rel='icon' href='mappin.png' type='image/x-icon'/ >
        <title>POS Location</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzYzXCwdJAV9KhpyjWraJLwzTGX9PlwdY&callback=initMap&libraries=&v=weekly" defer></script>
        <link rel="stylesheet" type="text/css" href="poslocation.css">
    </head>
    <body>
        <div id="map"></div>
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
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <script type="text/javascript">
            // The following example creates complex markers to indicate beaches near
            // Sydney, NSW, Australia. Note that the anchor is set to (0,32) to correspond
            // to the base of the flagpole.
            $(document).mousemove(function(e) {
                window.mouseX = e.pageX;
                window.mouseY = e.pageY;
            });
            
            function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
             zoom: 12,
             center: {lat: 23.8283016, lng: 90.4188136}
            });
            
            setMarkers(map);
            }
            
            
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
            
            function setMarkers(map) {
            
            extract_pos_data()
            
            
                 var markers = [];
                 for (var i = 0; i < poses_with_lat_long.length; i++) {
                   var pos = poses_with_lat_long[i]
                   var latlongmap = {};
                   latlongmap['lat'] = pos['Latitude'];
                   latlongmap['lng'] = pos['Longitude'];
                   markers.push(new google.maps.Marker({
                     position: latlongmap,
                     map: map,
                     /*title: pos['Agent Name'] + '\t' + pos['Address'],*/
                     idno: pos['S/l']
                   }));

                    var markerSpan = document.createElement("span");
                    markerSpan.setAttribute("id", "marker-span-" + pos['S/l']);
                    markerSpan.setAttribute("data-toggle", "tooltip");
                    markerSpan.setAttribute("class", "span-tooltip");
                    markerSpan.setAttribute("data-placement", "top");
                    markerSpan.setAttribute("data-html", "true");
                    markerSpan.setAttribute("title", pos['Agent Name'] + '&#013;' + pos['Address'] + '&#013;' + pos['Contact No.']) ;
                    $('body').append(markerSpan);
                 }
            
                 for(var i = 0; i < markers.length; i++) {
                   markers[i].addListener('click', function() {
                       var table = document.getElementById("myTable");
                       for (var i = 0, row; row = table.rows[i]; i++) {
                           row.style.backgroundColor = "#ffffff";
                       }
                       var row = document.getElementById("row-" + this.get('idno'));
                       row.style.backgroundColor = "#aaaaaa";
                       document.getElementById("row-" + this.get('idno')).scrollIntoView();
                   });

                   markers[i].addListener('mouseover', function() {
                        $("#marker-span-"+this.get("idno")).css('position','absolute');
                        $("#marker-span-"+this.get("idno")).css('left', mouseX);
                        $("#marker-span-"+this.get("idno")).css('top', mouseY);
                        $("#marker-span-"+this.get("idno")).tooltip('show');
                   });

                   markers[i].addListener('mouseout', function() {
                        $("#marker-span-"+this.get("idno")).tooltip('hide');
                   });
                 }
            
            }
            
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