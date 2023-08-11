<h1 class="alebooking_title"><?php esc_html_e('Booking Settingsaa','number-of-views-imedia'); ?></h1>
<?php settings_errors(); ?>
<div class="alebooking_content">
    <?php 
    ?>
    <form method="post" action="options.php" name='myform' enctype='multipart/form-data'>
        <?php 
            settings_fields('number_of_views_settings');
            do_settings_sections('numberofviews_settings');
            submit_button();
        ?>
    </form>
</div>
