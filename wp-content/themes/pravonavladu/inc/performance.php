<?php
/**
 * Created by PhpStorm.
 * User: most53
 * Date: 9/26/14
 * Time: 8:49 PM
 */

//remove not used features
add_action('after_setup_theme', function () {
    remove_theme_support('custom-background');
    remove_theme_support('custom-header');
    remove_theme_support('automatic-feed-links');
    remove_theme_support('menus');
});


//reduce queries to db
foreach (array('widget_pages', 'widget_calendar', 'widget_tag_cloud', 'widget_nav_menu', 'widget_icl_lang_sel_widget', 'widget_text_icl') as $option_name) {
    add_filter('pre_option_' . $option_name, function () {
        return '';
    });
}


//shows all DB queries
function dump_sql() {
    global $wpdb;
    foreach($wpdb->queries as $i => $q) {
        echo '<div style="margin: 5px auto 10px; width: 900px; color: #fff;">' . $i . '. (' . $q[1] . '): ' . $q[0] . '</div>';
    }
}



function clear_page_cache() {
    if (function_exists('wp_cache_clear_cache')) {
        $GLOBALS["super_cache_enabled"]=1;
        wp_cache_clear_cache();
    }
}
add_action('edit_voting', 'clear_page_cache');
add_action('acf/save_post', 'clear_page_cache', 1);