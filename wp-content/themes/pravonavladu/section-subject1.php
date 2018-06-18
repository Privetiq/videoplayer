<?php
/**
 * Created by PhpStorm.
 * User: most53
 * Date: 29.01.15
 * Time: 00:57
 */

?>
<?php

if (is_tax('voting')) {
    $current_voting = get_queried_object();
    $is_second_subject = get_field('is_second_subject', $current_voting);
} else {
    $current_voting = get_term(CURRENT_VOTING_ID, 'voting');
    $is_second_subject = IS_SECOND_SUBJECT;
}

if (!$is_second_subject) {
    return;
}

$guests = get_field('guests_in_studio', $current_voting);
$experts = get_field('experts_in_studio', $current_voting);
?>

<section id="subject1">
    <div class="content">

        <?php if ($guests) { ?>
            <h3><?php _e('Гості програми'); ?></h3>
        <?php foreach ($guests as $guest) {
            render_member($guest);
        } ?>
        <?php } ?>

        <?php if ($experts) { ?>
            <h3><?php _e('Експерти'); ?></h3>
            <?php foreach ($experts as $expert) {
                render_member($expert);
            } ?>
        <?php } ?>

    </div>
</section>