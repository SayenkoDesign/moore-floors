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
        case 'services':
            $services = get_sub_field('warranty_image');
            $data = [
                'warranty_image' => get_sub_field('warranty_image'),
                'warranty_text' => get_sub_field('warranty_text'),
                'title_1' => get_sub_field('title_line_1'),
                'title_2' => get_sub_field('title_line_2'),
                'content' => get_sub_field('content'),
                'boxes' => get_sub_field('boxes'),
            ];
            $flexibleContent[] = $twig->render('partials/services.html.twig', $data);
            break;
        case 'why':
            $accordion = $twig->render('panels/accordion.html.twig', ['accordion' => get_sub_field('accordion')]);
            $data = [
                'icon' => get_sub_field('icon'),
                'title_1' => get_sub_field('title_line_1'),
                'title_2' => get_sub_field('title_line_2'),
                'accordion' => $accordion,
                'testimonial_quote' => get_sub_field('testimony_text'),
                'testimonial_author' => get_sub_field('testimony_author'),
                'background' => get_sub_field('background_image'),
            ];
            $flexibleContent[] = $twig->render('partials/why.html.twig', $data);
            break;
        default:
            throw new \Exception("Template does not support a layout for $layout");
            break;
    }
}

echo $twig->render('base.html.twig', ['flexible_content' => $flexibleContent]);