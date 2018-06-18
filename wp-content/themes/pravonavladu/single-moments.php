<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package freymut
 */

get_header(); ?>

<article id="archive">
    <div class="single-moment clearfix">
        <div class="center">
            <?php

            $originalID = icl_object_id(get_the_ID(), 'moments', false, 'uk');
            $video = get_field('video', $originalID);
            $video_ovva = get_field('video_ovva', $originalID);

            if ($video_ovva) {
                ?>
                <div class="moment-video">
                    <iframe allowfullscreen frameborder="0" width="530" height="450" src="https://ovva.tv/video/embed/<?php echo $video_ovva ?>?autoplay=0&l=ua&logo=tsn"></iframe>
                </div>
            <?php
            } elseif ($video) {
                ?>
                <div class="moment-video">
                    <iframe src='https://tsn.ua/bin/player/iframe/<?php echo $video; ?>'
                        width='530' height='450' frameborder="0" allowfullscreen></iframe>
                </div>
            <?php
            } else {
                $thumb_url = wp_get_attachment_url(get_post_thumbnail_id($originalID)); ?>
                <figure class="moment-thumb " style="margin-bottom: 20px;">
                    <img src="<?php echo $thumb_url; ?>" alt=""/>
                </figure>
            <?php } ?>
        </div>

        <h1><?php echo get_the_title(); ?></h1>

        <p>
            <strong>
                <?php the_field('description', get_the_ID()); ?>
            </strong> <br/>
            <?php the_content(); ?>
        </p>
    </div>

</article>

<?php get_footer(); ?>
