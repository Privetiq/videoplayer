<?php

add_action('after_setup_theme', function () {
    if (is_user_logged_in()) {
        $is_placeholder = false;
    } else {
        $is_placeholder = get_field('is_placeholder', 'options');
    }

    $current_voting_id = get_field('active_voting', 'options');
    $is_poll = get_field('is_poll', 'voting_' . $current_voting_id);
    $is_second_subject = get_field('is_second_subject', 'voting_' . $current_voting_id);

    define('IS_PLACEHOLDER', $is_placeholder);
    define('IS_POLL', $is_poll);
    define('CURRENT_VOTING_ID', $current_voting_id);
    define('IS_SECOND_SUBJECT', $is_second_subject);
    define('IS_SPECIAL_EPISODE', (bool)get_field('is_special_episode', 'options'));
});


//JS and CSS scripts included here
require get_template_directory() . '/inc/assets.php';

require get_template_directory() . '/inc/performance.php';

require get_template_directory() . '/inc/fields.php';

//custom post types
require get_template_directory() . '/inc/custom-post-types.php';

//Social Login
require get_template_directory() . '/inc/SocialLogin.php';

require get_template_directory() . '/inc/votes-admin.php';


//theme setup
add_theme_support('post-thumbnails');

function getVotes($contestant_id, $term_id = 0, $weekly = false, $second_poll = false)
{
    global $wpdb;

    $table_name = $wpdb->prefix . "votes";
    if ($weekly) {
        $table_name = $wpdb->prefix . "votes_weekly";
    }

    if ($second_poll) {
        $table_name = $wpdb->prefix . "second_votes";
    }

    $where = '';
    if ($term_id) {
        $where .= ' AND `term_id` = ' . (int)$term_id;
    }

    return $wpdb->get_var("SELECT COUNT(*) FROM " . $table_name . " WHERE `post_id` = '" . esc_sql($contestant_id) . "' $where");
}

function getVotesLabel($votes)
{
    if ($votes == 1 || (substr($votes, -1) == '1' && substr($votes, -2) != '11')) {
        return 'голос';
    }

    if (
        $votes == 2 || (substr($votes, -1) == '2' && substr($votes, -2) != '12') ||
        $votes == 3 || (substr($votes, -1) == '3' && substr($votes, -2) != '13') ||
        $votes == 4 || (substr($votes, -1) == '4' && substr($votes, -2) != '14')

    ) {
        return 'голоса';
    }

    return 'голосiв';
}

function getAlreadyVoted($term_id)
{
    global $wpdb;

    $user = SocialLogin::getLoggedUser();

    $votingStage = get_field('voting_stage', 'voting_' . $term_id);

    $table_name = $wpdb->prefix . "votes";
    if ($votingStage == 'voting_finished') {
        $table_name = $wpdb->prefix . "votes_weekly";
    }

    if (!$user) {
        return array();
    }
    $profile = $user->getUserProfile();
    if (!$profile) {
        return array();
    }

    return $wpdb->get_col("
        SELECT post_id
        FROM " . $table_name . "
        WHERE `term_id` = '" . $wpdb->escape($term_id) . "'
            AND `user_id` = '" . $wpdb->escape($profile->identifier) . "'
            AND `network` = '" . $wpdb->escape($user->id) . "'");

}


add_action('wp_ajax_submit_question', 'handle_submit_question');
add_action('wp_ajax_nopriv_submit_question', 'handle_submit_question');

function handle_submit_question()
{

    if (isset($_POST['question'])) {

        $result = '';
        if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action'])) {

            // Do some minor form validation to make sure there is content
            if (!empty ($_POST['name'])) {
                $title = $_POST['name'];
            } else {
                $result .= '#name';
            }
            if (!empty ($_POST['city'])) {
                $city = $_POST['city'];
            } else {
                $result .= ',#city';
            }
            if (!empty ($_POST['question'])) {
                $question = $_POST['question'];
            } else {
                $result .= ',#question';
            }

            $question_to = $_POST['for-who'];
            $episod = $_POST['episod'];


            if ($result == '') {
                // Add the content of the form to $post as an array
                $post = array(
                    'post_title' => $title,
                    'post_content' => $question,
                    'post_status' => 'pending', // Choose: publish, preview, future, etc.
                    'post_type' => 'question' // Use a custom post type if you want to
                );
                $the_post_id = wp_insert_post($post); // Pass  the value of $post to WordPress the insert function

                update_post_meta($the_post_id, 'city', $city);

                if (IS_SECOND_SUBJECT) {
                    update_post_meta($the_post_id, 'question_to_guest', $question_to);
                }

                update_post_meta($the_post_id, 'question_to', $question_to);
                update_post_meta($the_post_id, 'episod', $episod);

                $result = 'success';
            }

        } // end IF

        echo $result;
    }

    exit;
}


add_action('wp_ajax_next_questions', 'get_next_questions');
add_action('wp_ajax_nopriv_next_questions', 'get_next_questions');

add_action('wp_ajax_next_moments', 'get_next_moments');
add_action('wp_ajax_nopriv_next_moments', 'get_next_moments');

add_action('wp_ajax_search_for', 'get_search_for');
add_action('wp_ajax_nopriv_search_for', 'get_search_for');


function render_question($question)
{

    ?>
    <div class="mosaic-content" data-questionid="<?php echo $question->ID; ?>">

        <h4><?php echo $question->post_title; ?></h4>
        <small><?php echo get_field('city', $question->ID); ?></small>
        <?php

        $questionToId = get_post_meta($question->ID, 'question_to', true);
        $questionToGuest = get_post_meta($question->ID, 'question_to_guest', true);

        $name = __(' всіх');

        if ($questionToId) {
            if ($questionToGuest) {

                $episode = get_post_meta($question->ID, 'episod', true);

                $voting = get_term($episode, 'voting');


                $guests1 = (array)get_field('guests_in_studio', $voting);
                $guests2 = (array)get_field('second_guests_in_studio', $voting);
                $guests = array_merge($guests1, $guests2);

                foreach ($guests as $guest) {
                    if ($guest['id'] == $questionToGuest) {
                        $name = ' ' . $guest['name_to'];
                    }
                }
            } else {
                $original_questionToId = icl_object_id($questionToId, 'contestant', false, ICL_LANGUAGE_CODE);
                $guestName = get_field('name_to', $original_questionToId);

                if ($guestName !== null) {
                    $name = ' ' . $guestName;
                }
            }
        }
        ?>
        <strong><?php _e("Питання до"); ?><?php echo $name ?>:</strong>

        <p>
            <?php echo $question->post_content; ?>
        </p>
    </div>
    <?php
}

function render_moment($moment)
{

    $originalMomentId = icl_object_id($moment->ID, 'moments', false, 'uk'); ?>
    <div class="moment-content" data-momentid="<?php echo $moment->ID; ?>">

        <!--            $thumb_url = 'http://img.youtube.com/vi/'. get_field('video', $originalMomentId) .'/hqdefault.jpg';-->
        <?php $thumb_url = wp_get_attachment_url(get_post_thumbnail_id($originalMomentId)); ?>

        <figure class="moment-thumb">
            <img width="100%" src="<?php echo $thumb_url; ?>" alt=""/>
            <?php if (get_field('video', $originalMomentId)) { ?><span class="play-button"></span><?php } ?>

            <div class="share-buttons">
                <div class="social-links">
                    <a href="#" onclick="share('Facebook', '<?php echo get_permalink($moment->ID); ?>');"
                       class="link-fb" target="_blank"></a>
                    <!-- <a href="#" onclick="share('Vkontakte', '<?php echo get_permalink($moment->ID); ?>');"
                       class="link-vk" target="_blank"></a> -->
                </div>
            </div>
        </figure>


        <h5>
            <?php the_field('description', $moment->ID); ?>
        </h5>

        <div
            class="moment-description-holder <?php if (get_field('video', $originalMomentId) || get_field('video_ovva', $originalMomentId)) { ?>video-popup<?php } ?>">
            <div class="popup-shadow hidden"></div>
            <div class="moment-description clearfix">
                <a href="#" class="close-btn"></a>

                <div class="single-moment-description clearfix hidden" data-momentid="<?php echo $moment->ID; ?>">
                    <?php
                    if (get_field('video_ovva', $originalMomentId)) {
                        ?>
                         <iframe allowfullscreen frameborder="0" width="530" height="450" src="https://ovva.tv/video/embed/<?php echo get_field('video_ovva', $originalMomentId); ?>?autoplay=0&l=ua&logo=tsn"></iframe>
                    <?php
                    } elseif (get_field('video', $originalMomentId)) {
                        ?>
                        <iframe
                            src='https://tsn.ua/bin/player/iframe/<?php echo get_field('video', $originalMomentId); ?>'
                            width='530' height='450' frameborder="0" allowfullscreen></iframe>
                        <?php
                    } else {
                        $thumb_url = wp_get_attachment_url(get_post_thumbnail_id($originalMomentId)); ?>
                        <figure class="moment-thumb fl ">
                            <img src="<?php echo $thumb_url; ?>" alt=""/>
                        </figure>
                    <?php } ?>

                    <p>
                        <strong>
                            <?php the_field('description', $moment->ID); ?>
                        </strong> <br/>
                        <?php echo $moment->post_content; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
}


function get_next_questions()
{

    $skip = isset($_GET['skip']) ? $_GET['skip'] : 0;

    $current_voting_id = isset($_GET['episode']) ? $_GET['episode'] : 0;
    $current_voting = get_term($current_voting_id, 'voting');

    $questions = get_posts(array(
        'posts_per_page' => 8,
        'offset' => $skip,
        'post_type' => 'question',
        'post_status' => 'publish',
        'meta_key' => 'episod',
        'meta_value' => $current_voting->term_id
    ));

    if (!$questions) {
        echo 'no more questions';
        exit;
    }

    foreach ($questions as $question) {
        render_question($question);
    }

    exit;
}

function get_next_moments()
{

    $skip = isset($_GET['skip']) ? $_GET['skip'] : 0;

    $current_voting_id = isset($_GET['episode']) ? $_GET['episode'] : 0;
    $current_voting = get_term($current_voting_id, 'voting');

    $moments = get_posts(array(
        'posts_per_page' => 4,
        'offset' => $skip,
        'post_type' => 'moments',
        'post_status' => 'publish',
        'meta_key' => 'episode',
        'meta_value' => $current_voting->term_id,
        'suppress_filters' => 0
    ));

    if (!$moments) {
        echo 'no more moments';
        exit;
    }

    foreach ($moments as $moment) {
        render_moment($moment);
    }

    exit;
}

function render_episode($term)
{

    //dirty fix to get original term in uk
    remove_all_filters('get_term');
    $original_term_id = icl_object_id($term->term_id, 'voting', false, 'uk');
    $original_term = get_term($original_term_id, 'voting');

    $term_permalink = get_home_url();
    if (substr($term_permalink, -1) != '/') $term_permalink .= '/';
    $term_permalink = 'archive/' . $original_term->slug . '/';

    $is_second_subject = get_field('is_second_subject', $term);
    $second_subject = '';
    if ($is_second_subject) {
        $second_subject = get_field('second_subject', $term);
    }

    if ($second_subject) {
        $second_subject = '<br/>' . $second_subject;
    }
    ?>
    <div class="episode-holder clearfix">
        <figure class="fl">
            <?php $thumbnail = get_field('thumbnail', $term); ?>
            <a href="<?php echo $term_permalink; ?>"><img src="<?php echo $thumbnail['url']; ?>" alt=""/></a>
        </figure>

        <div class="episode-description fr">
            <h3>
                <a href="<?php echo $term_permalink; ?>"><?php echo get_field('fancy_name', $term); ?><?php echo $second_subject; ?></a>
            </h3>

            <p class="date"><?php _e("Випуск");
                echo ' ';
                the_field('date', $term); ?></p>

            <p class="little-title"> <?php _e("Гості ефіру:"); ?></p>
            <?php
            if ($is_second_subject) {
                $guests = (array)get_field('guests_in_studio', $term);
                $guests2 = (array)get_field('second_guests_in_studio', $term);
                $experts = (array)get_field('experts_in_studio', $term);
                $experts2 = (array)get_field('second_experts_in_studio', $term);

                $all_guests = array_merge($guests, $guests2);
                $all_experts = array_merge($experts, $experts2);
                ?>
                <ul class="guests">
                    <?php foreach ($all_guests as $guest) { ?>
                        <li>
                            <strong><?php echo $guest['name']; ?></strong>
                            - <?php echo $guest['job']; ?>
                        </li>
                    <?php } ?>
                </ul>

                <p class="little-title"> <?php _e("Експерти:"); ?> </p>
                <ul class="guests">
                    <?php foreach ($all_experts as $expert) { ?>
                        <li>
                            <strong><?php echo $expert['name']; ?></strong>
                            - <?php echo $expert['job']; ?>
                        </li>
                    <?php } ?>
                </ul>


                <?php

            } else {
                $contestants = get_posts(array(
                    'posts_per_page' => 100,
                    'post_type' => 'contestant',
                    'post_status' => 'publish',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'voting',
                            'field' => 'id',
                            'terms' => (int)$term->term_id
                        )
                    ),
                    'meta_key' => 'sort_order',
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC'
                ));

                ?>

                <?php if ($contestants) { ?>

                    <ul class="guests">
                        <?php foreach ($contestants as $contestant) { ?>
                            <li>
                                <strong><?php echo $contestant->post_title; ?></strong>
                                - <?php the_field('occupation', $contestant->ID); ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>

                    <?php $guests = str_replace(",", ",</br>", str_replace(array('(', ')'), array('<span>', '</span>'), get_field('guests', $term))); ?>
                    <strong class="experts"><?php echo $guests; ?></strong>
                    <br/><br/><br/>
                <?php } ?>

                <p class="little-title"> <?php _e("Експерти:"); ?> <br/>
                    <?php $experts = str_replace(",", ",</br>", str_replace(array('(', ')'), array('<span>', '</span>'), get_field('experts', $term))); ?>
                    <strong class="experts"><?php echo $experts; ?></strong>
                </p>
            <?php } ?>
        </div>
    </div>
    <?php
}

function get_search_for()
{

    $searchFor = isset($_GET['search']) ? $_GET['search'] : '';
    $searchFor = mb_strtolower($searchFor, 'UTF-8');

    $current_voting_id = get_field('active_voting', 'options');
    $episodes = get_terms("voting", [
        'hide_empty' => false
    ]);

    $filtered = [];

    foreach ($episodes as $episode) {

        if ($current_voting_id !== $episode->term_id) {

            $filtered[] = $episode;

            $tags = mb_strtolower(get_field('episode_tags', $episode), 'UTF-8');

            if (mb_strpos($tags, $searchFor, 0, 'UTF-8') !== FALSE) {
                $filtered[] = $episode;
                continue;
            }

            $date = get_field('date', $episode);
            if (mb_strpos($date, $searchFor, 0, 'UTF-8') !== false) {
                $filtered[] = $episode;
                continue;
            }


            $fancy_name = mb_strtolower(get_field('fancy_name', $episode), 'UTF-8');
            if (mb_strpos($fancy_name, $searchFor, 0, 'UTF-8') !== false) {
                $filtered[] = $episode;
                continue;
            }


            $guests = mb_strtolower(get_field('guests', $episode), 'UTF-8');
            if (mb_strpos($guests, $searchFor, 0, 'UTF-8') !== false) {
                $filtered[] = $episode;
                continue;
            }


            $experts = mb_strtolower(get_field('experts', $episode), 'UTF-8');
            if (mb_strpos($experts, $searchFor, 0, 'UTF-8') !== false) {
                $filtered[] = $episode;
                continue;
            }


            $aGuests = get_field('guests_in_studio', $episode);
            if ($aGuests && is_array($aGuests)) {
                foreach ($aGuests as $g) {

                    $name = mb_strtolower($g['name'], 'UTF-8');
                    if (mb_strpos($name, $searchFor, 0, 'UTF-8') !== false) {
                        $filtered[] = $episode;
                        continue(2);
                    }
                }
            }


            $aExperts = get_field('experts_in_studio', $episode);
            if ($aExperts && is_array($aExperts)) {
                foreach ($aExperts as $e) {

                    $name = mb_strtolower($e['name'], 'UTF-8');
                    if (mb_strpos($name, $searchFor, 0, 'UTF-8') !== false) {
                        $filtered[] = $episode;
                        continue(2);
                    }
                }
            }


            $aGuests = get_field('second_guests_in_studio', $episode);
            if ($aGuests && is_array($aGuests)) {
                foreach ($aGuests as $g) {

                    $name = mb_strtolower($g['name'], 'UTF-8');
                    if (mb_strpos($name, $searchFor, 0, 'UTF-8') !== false) {
                        $filtered[] = $episode;
                        continue(2);
                    }
                }
            }

            $aExperts = get_field('second_experts_in_studio', $episode);
            if ($aExperts && is_array($aExperts)) {
                foreach ($aExperts as $e) {

                    $name = mb_strtolower($e['name'], 'UTF-8');
                    if (mb_strpos($name, $searchFor, 0, 'UTF-8') !== false) {
                        $filtered[] = $episode;
                        continue(2);
                    }
                }
            }

            $contestants = get_posts([
                's' => $searchFor,
                'post_type' => 'contestant',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'voting',
                        'field' => 'id',
                        'terms' => $episode->term_id,
                        'include_children' => false
                    )
                )
            ]);


            if ($contestants && is_array($contestants)) {
                foreach ($contestants as $c) {

                    $name = mb_strtolower($c->post_title, 'UTF-8');
                    if (mb_substr($name, $searchFor, 0, 'UTF-8') !== false) {
                        $filtered[] = $episode;
                        continue(2);
                    }
                }
            }
        }

    }

    if (!$filtered) {
        echo 'no results';
        exit;
    }

    foreach ($filtered as $episode) {
        render_episode($episode);
    }

    exit;
}



define('_PLAYER_ONLINE_ID_MEDIA_', 189931);
//define('_PLAYER_ONLINE_ID_MEDIA_', 361665);

function GetOnlineStream($options = false)
{

    $defaults = array(
        'string.var1' => 'Ct8v/qTbm/QMDQxbU6fn1d0ic2Cvy86jzympfUe+Czo=',
        'string.var2' => '6E4zfFROW7RvqAdoIRNabnowo8TeeBjq6nYUXoRawGY=',
        'string.var3' => 'DC1wIBU6pnoMR+DPbhPzCKGFuQqkZk0eDPb09rKPeCY=',
        'string.var4' => 'CyEsGdCy6pzA7mIB0WoTjTz2ixKpxJ8YJtbPB5wGukY=',
        'string.var5' => 'cV4oYvjEu5kjaskYpLZ3p2DqVssutMCiL3LWlycPpm8=',
        'int.id_media' => _PLAYER_ONLINE_ID_MEDIA_,
        'string.url' => 'https://grandcentral.1plus1.ua/lb/live',
        'string.user_agent' => '',
        //'string.user_agent'=>getenv('HTTP_USER_AGENT')
    );

    $settings = array_merge($defaults, $options);
    $str = time() . $settings['int.id_media'] . $settings['string.user_agent'] . $settings['string.var1'] . $settings['string.var3'];
    $hash = md5($str);
    $url = $settings['string.url'] . '/' . $settings['int.id_media'] . '/' . $hash;

    $ch = curl_init();
    $ip = $_SERVER['REMOTE_ADDR'];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $settings['string.user_agent']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X_HTTP_RQ: ' . $ip, 'REMOTE_ADDR: ' . $ip, 'HTTP_X_FORWARDED_FOR: ' . $ip));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    curl_close($ch);

    return explode('=', $response);
}

function getIpLocation($ip)
{

    if ($ip == '127.0.0.1') {
        return false;
    }

    $results = file_get_contents('https://geoip.maxmind.com/geoip/v2.1/city/' . $ip, false, stream_context_create(array(
        'http' => array(
            'header' => "Authorization: Basic " . base64_encode("63839:15so3qXbPzNi")
        )
    )));

    $json = json_decode($results, true);

    if ($json) {
        return $json;
    }

    return false;
}

if (isset($_GET['set_geo'])) {
    $result = $wpdb->get_results("SELECT * FROM  wp_votes WHERE geo_continent = '' LIMIT 25");

    foreach ($result as $vote) {
        $geo = getIpLocation($vote->ip);

        $wpdb->update('wp_votes', array(
            'geo_continent' => $geo['continent']['names']['en'],
            'geo_country' => $geo['country']['names']['en'],
            'geo_region' => isset($geo['subdivisions'][0]['names']['ru']) ? $geo['subdivisions'][0]['names']['ru'] : $geo['subdivisions'][0]['names']['en'],
            'geo_city' => isset($geo['city']['names']['ru']) ? $geo['city']['names']['ru'] : $geo['city']['names']['en']
        ), array('id' => $vote->id));
    }
}

function render_member($member)
{
    ?>
    <div class="member-container">
        <div class="member-photo"
             style="background-image: url('<?php echo $member['photo']['sizes']['thumbnail']; ?>');"></div>

        <div class="member-name">
            <?php echo $member['name']; ?>
            <div class="member-job"><?php echo $member['job']; ?></div>
        </div>

        <a href="#" class="member-link"><?php _e('Досьє'); ?></a>

        <div class="clear"></div>


        <div class="member-info">
            <div class="member-more-arrow"></div>
            <a href="#" class="close-popup-btn"></a>

            <p>
                <?php echo $member['info']; ?>
            </p>

        </div>
    </div>
    <?php
}
