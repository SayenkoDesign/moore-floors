<?php
require_once __DIR__.'/bootstrap.php';

global $wpdb;
$twig = $container->get("twig.environment");
$container->setParameter('db.charset', $wpdb->get_charset_collate());
$container->setParameter('estimator.estimates_table', $wpdb->prefix . "est_estimates");// stores estimates

add_action('after_switch_theme', function() use($container) {
    require( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $estimate_table = <<<SQL
    CREATE TABLE {$container->getParameter('estimator.estimates_table')} (
          `id` int(3) NOT NULL AUTO_INCREMENT,
          `date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
          `name` VARCHAR(128) NOT NULL,
          `phone` VARCHAR(128) NOT NULL,
          `email` VARCHAR(128) NOT NULL,
          `state` VARCHAR(64),
          `zip` VARCHAR(64) NOT NULL,
          `how` VARCHAR(255) NOT NULL,
          `service` VARCHAR(255) NOT NULL,
          `price` DECIMAL(13,2) NOT NULL,
          `sq_ft` INT(4) NOT NULL,
          `modifiers` LONGTEXT NOT NULL,
          `removal` LONGTEXT,
          PRIMARY KEY id (id)
        ) {$container->getParameter('db.charset')};
SQL;
    dbDelta($estimate_table);
});

function jsonToCSV($json) {
    $array = json_decode($json, true);
    $string = "";
    foreach($array as $v) {
        $string .= $v['title'] . ": " . $v['value'] . PHP_EOL;
    }
    return $string;
}

add_action('admin_init', function() use($container) {
    global $pagenow, $wpdb;
    if (
        $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == "estimates" &&
        isset($_GET['download_estimates']) && $_GET['download_estimates']
    ) {
        $table = $container->getParameter('estimator.estimates_table');
        $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
        $csv = 'id,date,name,phone,email,service,"square feet",price,options,removal' . PHP_EOL;
        foreach ($results as $estimate) {
            $csv .= '"' . $estimate->id . '",'
                . '"' . date('F jS Y', $estimate->date) . '",'
                . '"' . $estimate->name . '",'
                . '"' . $estimate->phone . '",'
                . '"' . $estimate->email . '",'
                . '"' . $estimate->service . '",'
                . '"' . $estimate->sq_ft . '",'
                . '"' . '$' . number_format($estimate->price, 2, '.', ',') . '",'
                . '"' . jsonToCSV($estimate->modifiers) . '",'
                . '"' . jsonToCSV($estimate->removal) . '"'
                . PHP_EOL;
        }
        $date = date('Y-m-d');
        $filename = 'estimates-' . $date . '-' . time() . '.csv';
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv;
        exit;
    }
});

add_action('admin_menu', function() use($twig, $container) {
    if (function_exists('acf_add_options_page')) {
        add_menu_page(
            'Estimates',
            'Estimates',
            'administrator',
            'estimates',
            function () use ($twig, $container) {
                global $wpdb;
                $table = $container->getParameter('estimator.estimates_table');
                $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
                echo $twig->render('pages/estimate-submissions.html.twig', ["estimates" => $results, "csv" => get_admin_url(null, 'admin.php?page=estimates&download_estimates=1')]);
            },
            'dashicons-editor-table'
        );
        acf_add_options_sub_page([
            'page_title' => 'Estimate Settings',
            'menu_title' => 'Estimate Settings',
            'parent_slug' => 'estimates',
            'capability' => 'administrator',
            'redirect' => false,
        ]);
    }
});