<?php
/*
Plugin Name: Количество просмотров
Description: Выводит количество просмотров каждой записи
Version: 1.0
Author: Kirill Skrypnik
Author URI: https://t.me/taviskaron09
*/

if(!defined('ABSPATH')){
    die;
}

class NumbersOfViews
{
    
    public function register(){
        
        add_action('admin_enqueue_scripts',[$this,'enqueue_admin']);
        add_action('wp_enqueue_scripts',[$this,'enqueue_front']);
        
        //Add menu admin
        add_action('admin_menu', [$this,'add_admin_menu']);
        add_filter('plugin_action_links_'.plugin_basename(__FILE__), [$this,'add_plugin_setting_link']);
        add_action('admin_init',[$this,'settings_init']);
    }
    
    //Register settings
    public function settings_init(){

        register_setting('number_of_views_settings','number_of_views_settings_options');

        add_settings_section('number_of_views_settings_section_title', esc_html__('Settings','number-of-views-imedia'), [$this, 'settings_section_title_html'], 'numberofviews_settings');

        add_settings_field('before_number_of_views', esc_html__('Before number of views ','number-of-views-imedia'), [$this, 'before_number_of_views_html'], 'numberofviews_settings', 'number_of_views_settings_section_title');
        add_settings_field('after_number_of_views', esc_html__('After number of views ','number-of-views-imedia'), [$this, 'after_number_of_views_html'], 'numberofviews_settings', 'number_of_views_settings_section_title');
        // Инпуту для картинок
        add_settings_field('before_number_of_views_image', esc_html__('Before number of views image','number-of-views-imedia'), [$this, 'before_number_of_views_image_html'], 'numberofviews_settings', 'number_of_views_settings_section_title');
        add_settings_field('after_number_of_views_image', esc_html__('After number of views image','number-of-views-imedia'), [$this, 'after_number_of_views_image_html'], 'numberofviews_settings', 'number_of_views_settings_section_title');
    
    }
    
    //Settings fields HTML
    public function settings_section_title_html(){
        echo esc_html__("Полученный Шорт код: [number_of_views_shortcode]", 'number-of-views-imedia');
    }
    
    public function before_number_of_views_html(){
        $options = get_option('number_of_views_settings_options'); ?>

        <input type="text" name="number_of_views_settings_options[before_number_of_views]" value="<?php echo isset($options['before_number_of_views']) ? $options['before_number_of_views'] : "";  ?>" />

    <?php }
    
    public function after_number_of_views_html(){
        $options = get_option('number_of_views_settings_options'); ?>

        <input type="text" name="number_of_views_settings_options[after_number_of_views]" value="<?php echo isset($options['after_number_of_views']) ? $options['after_number_of_views'] : "";  ?>" />

    <?php }
    // Дополнительные input для картинок
    public function before_number_of_views_image_html(){
        $options = get_option('number_of_views_settings_options'); 
        
        ?>
        <input id="background_image_before" type="text" name="number_of_views_settings_options[before_number_of_views_image]" value="<?php echo isset($options['before_number_of_views_image']) ? $options['before_number_of_views_image'] : "";  ?>" />
        <input id="upload_image_button_before" type="button" class="button-primary" value="Insert Image" />
    <?php }
    
    public function after_number_of_views_image_html(){
        $options = get_option('number_of_views_settings_options'); ?>
        <input id="background_image_after" type="text" name="number_of_views_settings_options[after_number_of_views_image]" value="<?php echo isset($options['after_number_of_views_image']) ? $options['after_number_of_views_image'] : "";  ?>" />
        <input id="upload_image_button_after" type="button" class="button-primary" value="Insert Image" />
    <?php }
    
    //Добавить ссылку на настройки на страницу плагина
    public function add_plugin_setting_link($link){
        $custom_link = '<a href="admin.php?page=numberofviews_settings">'.esc_html__('Settings','number-of-views-imedia').'</a>';
        array_push($link, $custom_link);
        return $link;
    }

    //Добавить страницу меню
    public function add_admin_menu(){
        add_menu_page(
            esc_html__( 'Numbers Of Views Settings Page', 'number-of-views-imedia' ),
            esc_html__('Number Of Views','number-of-views-imedia'),
            'manage_options',
            'numberofviews_settings',
            [$this, 'number_of_views_admin_page'],
            'dashicons-visibility',
            100
        );
    }
    
    public function enqueue_admin(){
        wp_enqueue_style('numberOfViewsImediaStyle', plugins_url('/assets/admin/styles.css', __FILE__));
        wp_enqueue_script('numberOfViewsImediaScript', plugins_url('/assets/admin/scripts.js', __FILE__));
        wp_enqueue_media();
    	wp_register_script('media-uploader', plugins_url('/assets/admin/media-uploader.js' , __FILE__ ), array('jquery'));
    	wp_enqueue_script('media-uploader');
    }
    
    public function enqueue_front(){
        wp_enqueue_style('numberOfViewsImediaStyleFront', plugins_url('/assets/front/styles.css', __FILE__));
        wp_enqueue_script('numberOfViewsImediaScriptFront', plugins_url('/assets/front/scripts.js', __FILE__));
    }
    
    //Admin HTML
    public function number_of_views_admin_page(){
        require_once plugin_dir_path(__FILE__).'admin/admin.php';
    }
    
}
    if(class_exists('NumbersOfViews')){
        $numbersOfViews = new NumbersOfViews();
        $numbersOfViews->register();
    }

register_activation_hook(__FILE__, function() {
    global $wpdb;
    // проверяем, что колонка не существует
    $query = "SELECT
                  1
              FROM
                  `information_schema`.`columns`
              WHERE
                  `column_name`='view_count'
                  AND
                  `table_name`='".$wpdb->posts."'";
    $result = $wpdb->get_row($query);
    if (!is_null($result)) {
        return;
    }
    // добавляем новую колонку
    $query = "ALTER TABLE
                  ".$wpdb->posts."
              ADD
                  `view_count` BIGINT UNSIGNED NOT NULL DEFAULT 0
              AFTER
                  `comment_count`";
    $wpdb->query($query);
});



/*
 * При каждом просмотре записи блога обновляем счетчик
 */
add_action('wp_head', function() {

    if (!is_single()) {
        return;
    }

    global $post, $wpdb;
    $views = $post->view_count + 1;
    // выполяем UPDATE-запрос к базе данных
    $wpdb->update(
        $wpdb->posts, // имя таблицы базы данных
        ['view_count' => $views], // какое поле таблицы обновляем
        ['ID' => $post->ID] // условие where для запроса
    );
});

add_shortcode( 'number_of_views_shortcode', 'number_of_views_shortcode_function' );

function number_of_views_shortcode_function(){
    $options = get_option('number_of_views_settings_options');
	global $post;
    $views = $post->view_count;
    echo '<div class="number_of_views_all_wrapper">';
    if(isset( $options['before_number_of_views'])){echo '<div class="before_number_of_views">' . $options['before_number_of_views'] . '</div>';}
    if(isset( $options['before_number_of_views_image'])){echo '<img class="before_number_of_views_image" src="' . $options['before_number_of_views_image'] . '"/>';}
    echo  $views;
    if(isset( $options['after_number_of_views_image'])){echo '<img class="after_number_of_views_image" src="' . $options['after_number_of_views_image'] . '"/>';}
    if(isset( $options['after_number_of_views'])){echo '<div class="after_number_of_views">' . $options['after_number_of_views'] . '</div>';}
    echo '</div>';
}
