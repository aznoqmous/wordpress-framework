<?php

namespace App\Controller;

use Roots\WPConfig\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route("/")]
    public function index(): Response
    {
        define( 'WP_USE_THEMES', true );

        ob_start();
        // Set up the WordPress query.
        wp();

        include ABSPATH . WPINC . '/template-loader.php';

        $content = ob_get_clean();

        return new Response($content);
    }

    #[Route("/{path}")]
    public function path($path): Response
    {
        define( 'WP_USE_THEMES', true );

        ob_start();
        // Set up the WordPress query.
        wp();

        include ABSPATH . WPINC . '/template-loader.php';

        $content = ob_get_clean();

        return new Response($content);
    }
}
