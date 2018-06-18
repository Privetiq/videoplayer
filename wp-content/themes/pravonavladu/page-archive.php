<?php

$current_voting_id = get_field('active_voting', 'options');


get_header(); ?>

<section id="archive">

    <?php
    $args = array(
        'orderby'  => 'id',
        'order'    => 'DESC',
        'hide_empty'   => false
    );
    $terms = get_terms("voting", $args);
    if ( !empty( $terms ) && !is_wp_error( $terms ) ) {

        foreach ( $terms as $term ) {
            $original_term_id = icl_object_id($term->term_id, 'voting', false, 'uk' );
            if($current_voting_id !== $original_term_id && $current_voting_id > $original_term_id) {
                render_episode( $term );
            }
        }
    } ?>


</section>


<?php
get_footer(); ?>