<?php
// PHP part of the plugin
// echo "<br>kbgeocode_main.php file connected!<br>";


$query = $_GET;
    // replace parameter(s)
    if ($query['search_location'] ?? null) {
        $search_loc_current_value = $query['search_location'] ?? null;
    }

      if (isset($search_loc_current_value) && is_numeric($search_loc_current_value) ) {

           // if current city key is numeric - search city value in DB
            $city_num_url = $search_loc_current_value;
            
            global $table_prefix, $wpdb;
            $kbgeocodesTable = $table_prefix . 'kbgeocodes';

            $result = $wpdb->get_results ( "SELECT * FROM $kbgeocodesTable WHERE geocode_num = $city_num_url" );

            if ($result) {
                foreach ( $result as $db_city ) {
                        $db_city_num = $db_city->geocode_num;
                        $db_city_txt = $db_city->geocode_txt;
                        // echo '$db_city_txt - '.$db_city_txt.'<br>';
                        // echo '$db_city_num - '.$db_city_num.'<br>';
                    }
                // echo 'Success! DB have values for this number!';
            }
            else {
                // echo 'Fail. DB dont contain values for this number';
                $db_city_dont_contain_value = '';
            }

        } 

      else {
          // echo '<br> current value is a text - don\'t need request to DB: '.$search_loc_current_value.'<br>';
        }
?>
<script>
var currentURL=window.location.href,url=new URL(currentURL),search_params=url.searchParams,search_location_value=search_params.get("search_location");function containsNumber(a){return/[0-9]/.test(a)}if(containsNumber(search_location_value)){var a="<?php if (isset($db_city_txt)) echo $db_city_txt; ?>",t="<?php if (isset($db_city_dont_contain_value)) echo $db_city_dont_contain_value; ?>";0!==a.length?search_params.set("search_location",a):search_params.set("search_location",t),url.search=search_params.toString(),window.location.href=url.toString()}
</script>  