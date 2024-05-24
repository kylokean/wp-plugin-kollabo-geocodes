<h1><?php esc_html_e('Select Content', 'kollabogeolocation'); ?> </h1>
<h4> Kollabo Geocodes plugin. Take Google location codes from URL parameters and turn them into city names </h4>

<div class="content select_pages_admin">

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php 
            settings_fields('kollabogeolocation_settings'); // ID of register settings
            do_settings_sections('kollabogeolocation_settings'); // settings page here!
            submit_button(__( 'Update page list', 'kollabogeolocation' ), 'update' );
        ?>
    </form>

</div> 