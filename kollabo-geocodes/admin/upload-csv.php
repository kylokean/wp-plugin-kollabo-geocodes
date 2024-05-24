<?php
?>
<h1>Location Management</h1>
<h4> Kollabo Geocodes plugin. Take Google location codes from URL parameters and turn them into city names </h4>

<?php
  
    global $wpdb;
    $table_name = $wpdb->prefix . 'kbgeocodes';

    if(isset($_POST["Import"])){

      $filextension=$_FILES["file"]["type"];      
      $filename=$_FILES["file"]["tmp_name"];

       if($_FILES["file"]["size"] > 0 && $filextension == 'text/csv') {

          $file = fopen($filename, "r");
          fgets($file); // skips first 'header' line of csv file

            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {

              if ( isset($getData[0]) && isset($getData[1]) ) {
                  if ( !empty($getData[0]) && !empty($getData[1]) ) {
                      $result = $wpdb->insert($table_name, array('geocode_num' => $getData[0], 'geocode_txt' => $getData[1] ));
                      echo "<h3 style='color:green;'>Data pair ".$getData[0]. "-" .$getData[1]. " sent to database table</h3>";
                  }
                }

                else {                  
                  echo "<h3 style='color:red;'>Unexpected Error with " .$getData[0]."-".$getData[1]. " Please check your import file</h3>";
                }

              }  

            fclose($file);          

       }

       else {
          echo "<h3 style='color:red;'>Please upload file with extension CSV</h3>";
       }

    }  
?>
 

<!-- Form to upload file -->
<div class="upload_csv_form">
  <h2>Upload locations into database</h2>

  <form method='post' action='' name='myform' enctype='multipart/form-data'>
      <input type="file" name="file" id="file" class="button button-hero"></th>
      <button type="submit" id="submit" name="Import" data-loading-text="Loading..." class="button update">Import CSV</button>


  </form>
</div>



<?php
   if(isset($_POST["Cleartable"])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kbgeocodes';
    $delete = $wpdb->query("TRUNCATE TABLE `$table_name`");
    $info_cleartable = '<div class="db_clear_msg">Database values cleared</div>';
  }  
?>

<div class="clear_values_form">
<h2>Clear all values from database</h2>
  <!-- Form to clear db values-->
   <form method="post" action=''>
      <input type="submit" name="Cleartable" value="Clear all db values!" class="button update" id="cleardb" /><br/>
   </form>
     <?php 
        if (isset ($info_cleartable)) {
          echo $info_cleartable;
        }
      ?> 
</div> 




<?php

    // Edit single value in db
   if(isset($_POST["Editvalue"])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kbgeocodes';
    $newGeocodeValue = $_POST['newgkvalue'];
    $selctedGeocodeValue = $_POST['geocode_list'];
    $wpdb->update($table_name, array('geocode_txt' => $newGeocodeValue ), array( 'geocode_num' => $selctedGeocodeValue ));
    // UPDATE `wp_kbgeocodes` SET `geocode_txt` = 'Basel-Landschaftggg' WHERE `wp_kbgeocodes`.`geocode_num` = 20130;   
    $info_singlevalue = '<div class="db_edit_msg">Database value updated to: '.$selctedGeocodeValue.' - '.$newGeocodeValue.'</div>';
  }

    // Delete single value in db
   if(isset($_POST["Deletevalue"])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kbgeocodes';
    $selctedGeocodeValue = $_POST['geocode_list'];
    $selctedGeocodeRow = $wpdb->get_row("SELECT geocode_txt FROM $table_name WHERE geocode_num = $selctedGeocodeValue");
    $selctedGeocodeTitle = $selctedGeocodeRow->geocode_txt;
    $wpdb->delete($table_name, array('geocode_num' => $selctedGeocodeValue));
    $info_singlevalue = '<div class="db_edit_msg">Database value '.$selctedGeocodeValue.' - '.$selctedGeocodeTitle.' was deleted</div>';
  }

  ?>

 <!-- Form to edit single value in db table -->
<div class="edit_values_form">
<h2>Edit/delete single value</h2>
  <form method="post" action=''>
    <?php
          global $wpdb;
          $table_name = $wpdb->prefix . 'kbgeocodes';
          $results = $wpdb->get_results(
              "SELECT * FROM $table_name"
          ); 

          echo '<select name="geocode_list">';
          foreach($results as $row) { 
            echo '<option value="' .$row->geocode_num. '">' .$row->geocode_num. '-' .$row->geocode_txt.'</option>'; 
          }
          echo '</select>';
    ?>
    <input type="text" id="new_geocode_txt" name="newgkvalue" />
    <input type="submit" name="Editvalue" value="Update location name" class="button update" id="editvaluedb" />
    <input type="submit" name="Deletevalue" value="Delete location" class="button update" id="deletevaluedb" />
  </form>

  <?php 
      if (isset ($info_singlevalue)) {
        echo $info_singlevalue;
      }
  ?> 

 </div> 


<?php
    // Output current values in the plugin page
    function show_table_values () {
      global $wpdb;
      $table_name = $wpdb->prefix . 'kbgeocodes';
      $results = $wpdb->get_results(
          "SELECT * FROM $table_name"
      );
      // $table_name = $wpdb->prefix . 'kbgeocodes';
      $num_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
      echo '<div class="kollabo_geotable-wrap"><span> Current database table containes '.$num_rows.' values</span><div class="kollabo_geotable">';
      foreach($results as $row)
      {
          echo '<div class="table_row"><div class="table_cell table_cell-id">'.$row->geocode_num.'</div>';
          echo '<div class="table_cell table_cell-txt">'.$row->geocode_txt.'</div></div>';
             // echo '<div class="table_row">'.$row->geocode_num.'</div>';
      }
      echo '</div></div>';
    }

// run the output table function
    show_table_values ();


 