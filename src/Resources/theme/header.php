<?php
/**
 * The header.
 *
 * This is the template that displays all of the <head> section and everything up until main.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?= \Addictic\WordpressFramework\Helpers\Metas::render() ?>
    <?php wp_head(); ?>
</head>
<?php
$page = \Addictic\WordpressFramework\Models\Legacy\PageModel::findActive();
?>
<body
    <?php body_class($page->getValue("background_color") ? "white-header" : ""); ?>
    style="<?= $page ? "--background-color: {$page->getValue("background_color")};" : "" ?><?= $page ? "--text-color: {$page->getValue("text_color")};" : "" ?>"
>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content">
        <?php
        esc_html_e( 'Skip to content', 'twentytwentyone' );
        ?>
    </a>

    <?php get_template_part( 'template-parts/header/site-header' ); ?>

    <div id="content" class="site-content">
        <div id="primary" class="content-area">
            <main id="main" class="site-main">
