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

//current episode
$current_voting_id = CURRENT_VOTING_ID;

//current episode term
$current_voting = get_term($current_voting_id, 'voting');

//current iteration
$votingStage = get_field('voting_stage', 'voting_' . $current_voting_id);

//current voting subject
$votingFancyName = get_field('fancy_name', $current_voting);

global $showVotingResults, $votingNotStarted, $votingIsFinished;

$votingIsFinished = false;
$votingNotStarted = false;
$showVotingResults = false;
switch ($votingStage) {
    case 'voting':
        break;
    case 'voting_finished':
        $votingIsFinished = true;
        break;
    case 'voting_results':
        $showVotingResults = true;
        $votingIsFinished = true;
        break;
    default:
        $votingNotStarted = true;
        break;
}


if (!IS_SPECIAL_EPISODE && !IS_SECOND_SUBJECT && $current_voting) {
    //list of contestant for voting
    $contestants = get_posts(array(
        'posts_per_page' => 4,
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


    //if user is logged in via social network get his votes
    $alreadyVoted = array();
}

get_header(); ?>

<?php
if (IS_PLACEHOLDER) {
    get_template_part('placeholder');
}
?>

<?php if (!IS_PLACEHOLDER) { ?>
    <input id="current_voting_id" type="hidden" value="<?php echo $current_voting_id; ?>"/>
    <div class="popup-overlay"></div>

    <!-- LOGIN POPUP -->
    <div class="popup" id="login-popup">
        <a href="#" class="close-popup-btn"></a>

        <div class="clear"></div>

        <div class="social-login">
            <h2><?php _e("УВІЙТИ ЧЕРЕЗ:"); ?></h2>

            <div class="error"></div>
            <a href="#" class="login-vk" data-network="Vkontakte"></a>
            <a href="#" class="login-fb" data-network="Facebook"></a>
        </div>
    </div>
    <!-- LOGIN POPUP -->

    <!-- MESSAGE POPUP -->
    <div class="popup" id="msg-popup">
        <a href="#" class="close-popup-btn"></a>

        <div class="clear"></div>
        <p></p>
    </div>
    <!-- MESSAGE POPUP -->


    <section id="home">
        <div class="home-text">

            <h2 data-0="top: 0px; opacity: 1" data-top-center="top: 400px; opacity: 0"
                data-anchor-target="#home">
                <?php echo $votingFancyName; ?>
                <?php if (IS_SECOND_SUBJECT) { ?>
                    <?php $second_subject = get_field('second_subject', $current_voting); ?>
                    <?php if ($second_subject) { ?>
                        <br/><?php the_field('second_subject', $current_voting); ?>
                    <?php } ?>
                <?php } ?>
            </h2>


            <div class="content" data-0="top: 0px; opacity: 1" data-top-center="top: 400px; opacity: 0"
                 data-anchor-target="#home">

                <?php
                $_STREAM = GetOnlineStream(array('int.id_media' => _PLAYER_ONLINE_ID_MEDIA_));

                if ($votingIsFinished == true && get_field('video', $current_voting)) {
                    ?>
                    <iframe class="video-holder" width="720" height="540"
                            src="//www.youtube.com/embed/<?php echo get_field('video', $current_voting); ?>"
                            frameborder="0" allowfullscreen></iframe>
                <?php
                }
                else {
                $_STREAM = GetOnlineStream(array('int.id_media' => _PLAYER_ONLINE_ID_MEDIA_));

                $_PLAYER_ONLINE_WIDTH = 720;
                $_PLAYER_ONLINE_HEIGHT = 568;
                $_PLAYER_ONLINE_IMG = '/player.png';
                $_PLAYER_ONLINE_ID_MEDIA = _PLAYER_ONLINE_ID_MEDIA_;
                ?>

                    <div class="video-holder" id="video-holder"></div>
                    <script type="text/javascript">
                        jQuery(document).ready(function ($) {
                            $("#video-holder").on('click', function () {

                                $(this).html('<iframe src="https://ovva.tv/tvguide/embed/1" width="640" height="556" noborder ></iframe>');

                                $(this).css('background', 'transparent');
                                $('#video-holder').off('click');
                                /*
                                player.online('video-holder', <?php echo $_PLAYER_ONLINE_ID_MEDIA; ?>, '1', '<?php echo $_PLAYER_ONLINE_IMG; ?>', <?php echo $_PLAYER_ONLINE_WIDTH; ?>, <?php echo $_PLAYER_ONLINE_HEIGHT; ?>, <?=time();?>, '<?=$_STREAM[0]?>', '<?=$_STREAM[1]?>');
                            */
                            });
                        });
                    </script>
                <?php } ?>

            </div>
        </div>
    </section>

    <?php if ($contestants) { ?>
        <section id="vote">

            <?php if (!IS_POLL) { ?>
                <h2><?php _e("Кому з політиків ви довіряєте?"); ?></h2>
            <?php } else { ?>
                <h2><?php _e("Гості програми"); ?></h2>
            <?php } ?>

            <div id="contestants" class="clearfix">
                <?php

                $total_votes = 0;
                foreach ($contestants as &$c_) {
                    $c_->original_id = icl_object_id($c_->ID, 'contestant', false, 'uk');
                    $c_->votes = getVotes($c_->original_id);
                    $c_->votes_from_admin = get_field('votes_results', $c_->original_id);
                    $total_votes += $c_->votes;
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

                    if (strpos($perc, '.') !== false) {
                        $perc = round($perc);
                    }

                    $total_perc += $perc;

                    if ($i + 1 == count($contestants)) {
                        if ($total_perc == 101) {
                            $perc--;
                        }

                        if ($total_perc_weekly == 101) {
                            $perc_weekly--;
                        }
                    }

                    $thumb_url = wp_get_attachment_url(get_post_thumbnail_id($contestant->ID));

                    if ($votingNotStarted || $votingIsFinished) {
                        $add_class = 'disabled';
                    } elseif ($alreadyVoted && in_array($contestant->original_id, $alreadyVoted)) {
                        $add_class = 'voted';
                    } else {
                        $add_class = '';
                    }

                    ?>

                    <div class="contestant" data-contestantid="<?php echo $contestant->original_id; ?>">
                        <?php if (!IS_POLL) { ?>
                            <a class="vote-btn <?php echo $add_class; ?>"><?php _e("Голосувати"); ?></a>
                        <?php } ?>

                        <figure class="contestant-photo" style="background-image: url('<?php echo $thumb_url; ?>');">
                            <div class="info">
                                <strong class="contestant-name"><?php echo $contestant->post_title; ?></strong>

                                <div class="contestant-place"><?php the_field('occupation', $contestant->ID); ?></div>
                            </div>

                            <span class="arrow"></span>
                        </figure>
                        <?php if ($showVotingResults && !IS_POLL) { ?>
                            <div class="contestant-votes"
                                 title="<?php _e('Результати голосування в прямому ефірі') ?>"><?php echo $perc; ?>%
                            </div>
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
    <?php } ?>


    <?php if (IS_POLL) {
        if (IS_SECOND_SUBJECT) {

            ?>

            <section class="title">
                <h2><?php the_field('fancy_name', $current_voting); ?></h2>
            </section>
            <?php

            get_template_part('section', 'poll1');

            get_template_part('section', 'subject1');


        } else {
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

            <?php get_template_part('section', 'poll1'); ?>
        <?php } ?>


    <?php } ?>

    <?php
    if (IS_SECOND_SUBJECT) {

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

    } ?>

    <?php
    $moments = get_posts(array(
        'posts_per_page' => 4,
        'post_type' => 'moments',
        'post_status' => 'publish',
        'meta_key' => 'episode',
        'meta_value' => $current_voting->term_id,
        'suppress_filters' => 0
    ));

    if ($moments) {
        ?>
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


    <section id="questions">
        <div class="questions-overlay"></div>

        <?php if (!$votingIsFinished) { ?>
            <header class="content">

                <h2><?php _e("Несправедлива влада нетривала"); ?></h2>

                <p><?php _e("Ви вболіваєте за майбутнє країни. Ви прагнете розібратися в ситуації і зрозуміти політиків.<br/>Ставте запитання і ви отримаєте на них відповіді."); ?></p>

                <a href="#question-form" id="write-question" class="btn"><?php _e("Запитання"); ?></a>

                <div id="question-form" class="hidden">
                    <a href="#" class="close-btn"></a>

                    <form action="<?php echo admin_url('admin-ajax.php'); ?>" id="submit-question-form"
                          data-success="<?php _e("Питання буде опубліковано після перевірки"); ?>">

                        <fieldset class="fl">
                            <label for="name"><?php _e("Ім’я та прізвище:"); ?></label>
                            <input type="text" name="name" id="name"/>

                            <label for="city"><?php _e("Місто:"); ?></label>
                            <input type="text" name="city" id="city"/>

                            <label for="for-who"><strong><?php _e("До кого запитання:"); ?></strong></label>
                            <select name="for-who" id="for-who">
                                <option value="null"><?php _e("Всі"); ?></option>
                                <?php if (IS_SECOND_SUBJECT) {
                                    $guests = get_field('guests_in_studio', $current_voting);
                                    $second_guests = get_field('second_guests_in_studio', $current_voting);

                                    foreach (array_merge($guests, $second_guests) as $i => $guest) { ?>
                                        <option
                                            value="<?php echo $guest['id']; ?>"><?php echo $guest['name_to']; ?></option>
                                    <?php }
                                } else { ?>
                                    <?php foreach ($contestants as $contestant) { ?>
                                        <?php $originalContestantId = icl_object_id($contestant->ID, 'contestant', false, 'uk'); ?>

                                        <option
                                            value="<?php echo $originalContestantId; ?>"><?php echo $contestant->post_title; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>

                            <input type="hidden" name="episod" value="<?php echo $current_voting_id ?>"/>
                        </fieldset>

                        <fieldset class="fl">
                            <label for="question"><?php _e("Ваше запитання:"); ?>
                                <small> <?php _e("(До 250 символів)"); ?></small>
                            </label>
                            <textarea name="question" id="question" cols="40" rows="10" maxlength="540"></textarea>
                        </fieldset>

                        <div class="clear"></div>

                        <span class="success">&nbsp;</span>

                        <input type="submit" class="btn" value="<?php _e("Відправити"); ?>"/>

                    </form>
                </div>

            </header>
        <?php } ?>


        <div class="middle content">
            <h2><?php _e("Запитання до гостей програми"); ?></h2>

            <p>
                <?php _e("Найцікавіші запитання прозвучать в прямому ефірі."); ?>
            </p>
        </div>

        <?php

        $questions = get_posts(array(
            'posts_per_page' => 8,
            'post_type' => 'question',
            'post_status' => 'publish',
            'meta_key' => 'episod',
            'meta_value' => $current_voting_id
        ));

        ?>

        <div id="question-popup">
            <a href="#" class="question-popup-close"></a>
            <div id="question-popup-content"></div>
        </div>
        <footer id="mosaic-holder">

            <?php if ($questions) {
                foreach ($questions as $question) {
                    render_question($question);
                }
            } ?>

        </footer>

        <a class="next-questions" href="#"></a>

        <div class="clear"></div>


        <!--    <div class="copy">© 2014 1+1 LLC. All rights reserved.</div>-->

    </section>

<?php } ?>

<?php get_footer(); ?>
