<?php
require_once __DIR__.'/App/bootstrap.php';

$twig = $container->get("twig.environment");

$cases = [];
while(have_posts()) {
    the_post();
    $cases[] = $twig->render('partials/cases-teaser.html.twig', []);
}
wp_reset_postdata();
the_post();

echo $twig->render('pages/archive-cases.html.twig', ['cases' => $cases]);