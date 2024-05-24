// alert("Script file connected!");
// <!-- Unminified JS part of the plugin -->

var currentURL = window.location.href;
var url = new URL(currentURL);    
var search_params = url.searchParams;
var search_location_value = search_params.get('search_location')

function containsNumber(str) {
  return /[0-9]/.test(str);
}

if ( containsNumber(search_location_value) ) {

var cityNamePHP_txt = "<?php if (isset($db_city_txt)) echo $db_city_txt; ?>"; // get variable from PHP
var cityNamePHP_num = "<?php if (isset($db_city_dont_contain_value)) echo $db_city_dont_contain_value; ?>"; // get variable from PHP


    if (cityNamePHP_txt.length !== 0) {  // we have key-value in DB

          // alert ('var contain number which located in DB');          
            
          // get 'search_location' value from cityNamePHP_txt PHP variable
          search_params.set('search_location', cityNamePHP_txt);

    }

    else { // we don't have key-value in DB

          // alert ('var contain number which is not located in DB');          
            
          // get 'search_location' value from cityNamePHP_num PHP variable
          search_params.set('search_location', cityNamePHP_num);     

    }

    // change the search property of the main url
    url.search = search_params.toString();

    // redirect to the updated url
    window.location.href = url.toString();

}


else {
      // alert ('var do not contain number');
}