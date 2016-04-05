<?php
require_once __DIR__.'/App/bootstrap.php';

use Sayenko\Stylesheet;
use Sayenko\Script;

// stylesheets
$montserrat = new Stylesheet('montserrat', 'https://fonts.googleapis.com/css?family=Montserrat:400,700');
$fontawesome = new Stylesheet('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
$stylesheet = new Stylesheet('stylesheet', get_stylesheet_directory_uri() . '/web/stylesheets/app.css', [
    $montserrat->getHandle(),
    $fontawesome->getHandle()
]);
$stylesheet->alwaysUpdate();
$montserrat->register();
$fontawesome->register();
$stylesheet->register();

$script = new Script('script', get_stylesheet_directory_uri() . '/web/scripts-min/app.min.js');
$script->register();

// menus
register_nav_menus([
    'main' => 'Main menu',
    'secondary' => 'Secondary menu',
    'footer_left' => 'Left footer menu',
    'footer_right' => 'Right footer menu',
]);

// options page for ACF
if (function_exists('acf_add_options_page')) {
    $parent = acf_add_options_page([
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false,
        'icon_url'      => 'dashicons-sayenko',
    ]);

    acf_add_options_sub_page([
        'page_title'   => 'Social',
        'menu_title'   => 'Social',
        'parent_slug'  => $parent['menu_slug'],
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Call to Action',
        'menu_title'  => 'Call to Action',
        'parent_slug' => $parent['menu_slug'],
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Footer',
        'menu_title'  => 'Footer',
        'parent_slug' => $parent['menu_slug'],
    ]);
}

// logo for ACF options page
add_action('admin_head', function () {
    $rootURI = get_template_directory_uri();
    echo <<<HTML
    <style type="text/css">
        .dashicons-sayenko {
            background-image: url('$rootURI/options-icon.png');
            background-size: 18px;
            background-position: 10px center;
            background-repeat: no-repeat;
        }
    </style>
HTML;
});

// login logo
add_action('login_head', function () {
    $rootURI = get_template_directory_uri();
    echo <<<HTML
    <style type="text/css">
        h1 a {
            background-image: url('$rootURI/logo.png') !important;
            background-size: contain !important;
            width: 320px !important;
            height: 120px !important;
       }
    </style>
HTML;
});


// referral widget
add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'referral_dashboard_widget',
        'RECEIVE $500 in CASH FOR A WEBSITE REFERRAL!!',
        function () {
            echo <<<HTML
                <a href='http://www.sayenkodesign.com'>
                    <img alt='Seattle Web Design' src='http://www.sayenkodesign.com/wp-content/uploads/2014/08/Sayenko-Design-WP-Referral-Bonus-460.jpg' width='100%'>
                </a>
                </br>
                </br>
                Simply introduce us via email along with the prospects phone number.
                Email introductions can be sent to
                <a href='mailto:mike@sayenkodesign.com'>mike@sayenkodesign.com</a>
HTML;
        }
    );
});
