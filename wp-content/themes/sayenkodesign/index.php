<?php
require_once __DIR__.'/App/bootstrap.php';

$twig = $container->get("twig.environment");
$data = [];

// cases
if (is_post_type_archive('cases')) {
    $template = 'pages/archive.html.twig';
    $data['title'] = 'Case Studies';

    $data['archives'] = [];
    while(have_posts()) {
        the_post();
        $data['archives'][] = $twig->render('partials/cases-teaser.html.twig', []);
    }
    wp_reset_postdata();
    the_post();
}
// blogs, blog archives, search
else if(is_home() || is_archive() || is_search()) {
    $template = 'pages/archive.html.twig';
    if(is_archive()) {
        $data['title'] = get_the_archive_title();
    } else if (is_search()) {
        $data['title'] = "Search Results";
    } else {
        $data['title'] = 'Blogs';
    }

    $data['archives'] = [];
    while(have_posts()) {
        the_post();
        $data['archives'][] = $twig->render('partials/blog-teaser.html.twig', []);
    }
    wp_reset_postdata();
    the_post();
}
// search
else if(is_search()) {
    $template = 'pages/archive.html.twig';
    $data['title'] = "Search Results";

    $data['archives'] = [];
    while(have_posts()) {
        the_post();
        $data['archives'][] = $twig->render('partials/blog-teaser.html.twig', []);
    }
    wp_reset_postdata();
    the_post();
}
// home page
else if(is_page() || get_page_template_slug() == 'page-templates/home.php') {
    $template = 'base.html.twig';
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
                    'testimonial_quote' => get_sub_field('testimonial')[0]['content'],
                    'testimonial_author' => get_sub_field('testimonial')[0]['name'],
                    'background' => get_sub_field('testimonial')[0]['image'],
                ];
                $flexibleContent[] = $twig->render('partials/why.html.twig', $data);
                break;
            case 'generic_content':
                $data = [
                    'content' => get_sub_field('content'),
                ];
                $flexibleContent[] = $twig->render('partials/generic.html.twig', $data);
                break;
            case 'recent_projects':
                $data = [];
                $data['projects'] = [];
                $cases = get_sub_field('cases');
                foreach($cases as $case) {
                    $data['projects'][] = [
                        'title_1' => $case['title_line_1'],
                        'title_2' => $case['title_line_2'],
                        'id' => $case['case_study']->ID,
                        'page' => get_the_permalink($case['case_study']->ID),
                        'image' => get_the_post_thumbnail($case['case_study']->ID),
                        'content' => get_the_excerpt($case['case_study']->ID),
                    ];
                }
                $flexibleContent[] = $twig->render('partials/recent.html.twig', $data);
                break;
            default:
                throw new \Exception("Template does not support a layout for $layout");
                break;
        }
    }
    if(count($flexibleContent)) {
        $data['flexible_content'] = $flexibleContent;
    }
    if(is_front_page()) {
        $data['hero_title'] = false;
    }
}
// everything else
else {
    $template = 'base.html.twig';
}

echo $twig->render($template, $data);
