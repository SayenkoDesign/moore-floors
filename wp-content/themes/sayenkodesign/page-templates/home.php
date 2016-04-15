<?php
/**
* Template Name: Home
*/
require_once __DIR__.'/../App/bootstrap.php';

/**
 * @var $twig \Twig_Environment
 */
$twig = $container->get("twig.environment");
$flexibleContent = [];
while (have_rows('content')) {
    the_row();
    switch($layout = get_row_layout()) {
        case 'hero_slider':
            $slides = get_sub_field('slides');
            $data = [];
            foreach ($slides as $slide) {
                $data[] = [
                    'image' => $slide['image'],
                    'title' => $slide['title'],
                    'tagline' => $slide['tagline'],
                    'links' => $slide['links'],
                ];
            }
            $flexibleContent[] = $twig->render('panels/hero-slider.html.twig', ['slides' => $data]);
            break;
        default:
            throw new \Exception("Template does not support a layout for $layout");
            break;
    }
}

echo $twig->render('base.html.twig', ['flexible_content' => $flexibleContent]);