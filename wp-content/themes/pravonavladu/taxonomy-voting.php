<?php
/**
 * Template Name: Archives
 * @package freymut
 */

$current_voting_id = get_queried_object()->term_id;
$current_voting = get_term($current_voting_id, 'voting');
$is_poll = get_field('is_poll', $current_voting);
$is_second_subject = get_field('is_second_subject', $current_voting);

if (!$current_voting_id) {
    status_header(404);
    nocache_headers();
    include(get_404_template());
    exit;
}

if ($current_voting) {
    $contestants = get_posts(array(
        'posts_per_page' => 100,
        'post_type' => 'contestant',
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'voting',
                'field' => 'id',
                'terms' => $current_voting->term_id
            )
        ),
        'meta_key' => 'sort_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    ));

    $alreadyVoted = getAlreadyVoted($current_voting_id);
}

get_header(); ?>

<input id="current_voting_id" type="hidden" value="<?php echo $current_voting_id; ?>"/>

<section id="home">
    <div class="home-text">
        <div class="content" data-0="top: 0px; opacity: 1" data-top-center="top: 350px; opacity: 0"
             data-anchor-target="#home">

            <iframe class="video-holder" width="720" height="540"
                    src="//www.youtube.com/embed/<?php echo get_field('video', $current_voting); ?>" frameborder="0"
                    allowfullscreen></iframe>

        </div>
    </div>


</section>

<?php if ($is_second_subject) { ?>
    <?php if ($is_second_subject) { ?>
        <h2><?php echo get_field('fancy_name', $current_voting); ?>

            <?php $second_subject = get_field('second_subject', $current_voting); ?>
            <?php if ($second_subject) { ?>
                <br/><?php the_field('second_subject', $current_voting); ?>
            <?php } ?>

            <br/>
            <small><?php _e('Випуск');
                echo ' ' . get_field('date', $current_voting); ?></small>
        </h2>
    <?php } ?>


    <section class="title">
        <h2><?php the_field('fancy_name', $current_voting); ?></h2>
    </section>
    <?php


    get_template_part('section', 'poll1');

    get_template_part('section', 'subject1');

    $second_subject = get_field('second_subject', $current_voting);
    if ($second_subject) {
        ?>
        <section class="title">
            <h2><?php echo $second_subject; ?></h2>
        </section>
    <?php
    }

    get_template_part('section', 'poll2');

    get_template_part('section', 'subject2');


    ?>
<?php } else { ?>
    <section id="vote">

        <h2><?php echo get_field('fancy_name', $current_voting); ?>
            <br/>
            <small><?php _e('Випуск');
                echo ' ' . get_field('date', $current_voting); ?></small>
        </h2>

        <div id="contestants" class="clearfix">
            <?php if ($is_poll) { ?>
                <h2><?php _e("Гості програми"); ?></h2>
            <?php } ?>
            <?php
            $total_votes = 0;
            $total_votes_weekly = 0;
            foreach ($contestants as &$c_) {
                $c_->original_id = icl_object_id($c_->ID, 'contestant', false, 'uk');
                $c_->votes = getVotes($c_->original_id);
                $c_->votes_weekly = getVotes($c_->original_id, true);
                $c_->votes_from_admin = (int)get_field('votes_results', $c_->original_id);
                $total_votes += $c_->votes;
                $total_votes_weekly += $c_->votes_weekly;
            }
            ?>

            <?php
            $total_perc = 0;
            $total_perc_weekly = 0;
            ?>
            <?php foreach ($contestants as $i => $contestant) {
                if ($total_votes == 0) {
                    $perc = 0;
                } else {
                    $perc = $contestant->votes_from_admin !== '' ? (int)$contestant->votes_from_admin : (($contestant->votes / $total_votes) * 100);
                }

                if ($total_votes_weekly == 0) {
                    $perc_weekly = 0;
                } else {
                    $perc_weekly = ($contestant->votes_weekly / $total_votes_weekly) * 100;
                }

                if (strpos($perc, '.') !== false) {
                    $perc = round($perc);
                }

                if (strpos($perc_weekly, '.') !== false) {
                    $perc_weekly = round($perc_weekly);
                }

                $total_perc += $perc;
                $total_perc_weekly += $perc_weekly;

                if ($i + 1 == count($contestants)) {
                    if ($total_perc == 101) {
                        $perc--;
                    }

                    if ($total_perc_weekly == 101) {
                        $perc_weekly--;
                    }

                    if ($total_perc == 99) {
                        $perc++;
                    }

                    if ($total_perc_weekly == 99) {
                        $perc_weekly++;
                    }
                }

                $thumb_url = wp_get_attachment_url(get_post_thumbnail_id($contestant->ID));
                ?>

                <div class="contestant" data-contestantid="<?php echo $contestant->original_id; ?>">
                    <figure class="contestant-photo" style="background-image: url('<?php echo $thumb_url; ?>');">
                        <div class="info">
                            <strong class="contestant-name"><?php echo $contestant->post_title; ?></strong>

                            <div class="contestant-place"><?php the_field('occupation', $contestant->ID); ?></div>
                        </div>

                        <span class="arrow"></span>
                    </figure>
                    <?php if (!$is_poll) { ?>
                        <div class="contestant-votes"><?php echo $perc; ?>%</div>
                        <?php if ($total_votes_weekly > 0) { ?>
                            <!--                <div class="weekly-votes">--><?php //echo $perc_weekly; ?><!--%</div>-->
                        <?php } ?>
                    <?php } ?>

                </div>

            <?php } ?>

            <div class="clear"></div>

            <div class="contestant-description-holder">
                <div class="contestant-description">
                    <a href="#" class="close-btn"></a>

                    <?php foreach ($contestants as $contestant) { ?>
                        <p class="hidden" data-contestantid="<?php echo $contestant->original_id; ?>">
                            <?php echo $contestant->post_content; ?>
                        </p>
                    <?php } ?>

                </div>
            </div>

        </div>
    </section>
    <?php

    if ($is_poll) {
        $experts = get_field('experts_in_studio', 'voting_' . $current_voting->term_id);

        if ($experts) {
            ?>
            <section id="experts">

                <h2><?php _e("Експерти"); ?></h2>

                <div id="experts-holder" class="clearfix">

                    <?php foreach ($experts as $i => $expert) {

                        $thumb_url = $expert['photo']['url'];
                        ?>

                        <div class="contestant" data-expertid="<?php echo $i; ?>">

                            <figure class="contestant-photo"
                                    style="background-image: url('<?php echo $thumb_url; ?>');">
                                <div class="info">
                                    <strong class="contestant-name"><?php echo $expert['name']; ?></strong>

                                    <div class="contestant-place"><?php echo $expert['job']; ?></div>
                                </div>

                                <span class="arrow"></span>
                            </figure>

                        </div>

                    <?php } ?>

                    <div class="clear"></div>

                    <div class="contestant-description-holder">
                        <div class="contestant-description">
                            <a href="#" class="close-btn"></a>

                            <?php foreach ($experts as $i => $expert) { ?>
                                <p class="hidden" data-expertid="<?php echo $i; ?>">
                                    <?php echo $expert['info']; ?>
                                </p>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </section>
        <?php } ?>

        <section id="poll">
            <h2><?php _e('Голосування'); ?></h2>

            <div class="content">
                <h3><?php the_field('question', 'voting_' . $current_voting->term_id); ?></h3>
                <?php $answers = get_field('answers', 'voting_' . $current_voting->term_id); ?>

                <?php foreach ($answers as $answer) { ?>
                    <div class="poll-answer-row clearfix">
                        <label><?php echo $answer['answer']; ?></label>

                        <div class="poll-answer-holder">
                            <div style="width: <?php echo $answer['result']; ?>%;" class="poll-answer-result"></div>
                        </div>
                        <div class="poll-answer-perc"><?php echo $answer['result']; ?>%</div>
                    </div>
                <?php } ?>

            </div>
        </section>
    <?php } ?>

<?php } ?>
<?php
$moments = get_posts(array(
    'posts_per_page' => 4,
    'post_type' => 'moments',
    'post_status' => 'publish',
    'meta_key' => 'episode',
    'meta_value' => (int)$current_voting->term_id,
    'suppress_filters' => 0
));

?>
<?php if ($moments) { ?>
    <section id="best">
        <header class="content">
            <h2><?php _e("Найкращі моменти програми"); ?></h2>

            <p><?php _e("Відео-цитати та фото найцікавіших епізодів програми."); ?></p>
        </header>


        <div id="moments-holder" data-episode="<?php echo $current_voting->term_id; ?>">
            <?php if ($moments) {
                foreach ($moments as $moment) {
                    render_moment($moment);
                }
            } ?>
        </div>

        <a class="next-moments" href="#"></a>

        <div class="clear"></div>
    </section>
<?php } ?>

<?php get_footer(); ?>
