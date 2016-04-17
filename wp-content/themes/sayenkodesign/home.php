<?php
require_once __DIR__.'/App/bootstrap.php';

$twig = $container->get("twig.environment");

$blogs = [];
while(have_posts()) {
    the_post();
    $blogs[] = $twig->render('partials/blog-teaser.html.twig', []);
}
wp_reset_postdata();
the_post();

echo $twig->render('pages/post.html.twig', ['blogs' => $blogs]);