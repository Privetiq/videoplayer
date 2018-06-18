<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package freymut
 */
global $votingIsFinished;
$user = SocialLogin::getLoggedUser();
if ($user) {
    $currentNetwork = $user->id;
}

$archive_page = get_page_by_path('archive');
$slug = '';
if (is_page('archive')) {
    global $post;
    $slug = $archive_page->post_name;
}


$current_voting = get_term(CURRENT_VOTING_ID, 'voting');

$is_archive = false;
$is_episode = is_tax('voting');
$base_url = '';
if ($slug == 'archive' || $is_episode || is_singular('moments')) {
    $base_url = get_home_url();

    if (substr($base_url, -1) != '/') {
        $base_url .= '/';
    }
}

if ($slug == 'archive') {
    $is_archive = true;
}

?><!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>

    <meta property="og:type" content="website"/>
    <?php
    //single episode
    if ($is_episode) {
        $term = get_queried_object();
        $title = esc_html(get_field('fancy_name', $term) . ' | ' . get_bloginfo('name'));
        $description = esc_html(get_bloginfo('description'));
        $video = get_field('video', $term);
        $video = $video ? 'http://www.youtube.com/v/' . $video : '';
        ?>
        <title><?php echo $title; ?></title>
        <meta property="og:title" content="<?php echo $title; ?>"/>
        <meta name="description" content="<?php echo $description; ?>"/>
        <meta property="og:description" content="<?php echo $description; ?>"/>
        <?php if ($video) { ?>
            <meta property="og:video" content="<?php echo $video; ?>">
        <?php } ?>
        <?php
        //archive
    } elseif ($is_archive) {
        $title = esc_html(get_the_title($post) . ' | ' . get_bloginfo('name'));
        $description = esc_html(get_bloginfo('description'));
        ?>
        <title><?php echo $title; ?></title>
        <meta property="og:title" content="<?php echo $title; ?>"/>
        <meta name="description" content="<?php echo $description; ?>"/>
        <meta property="og:description" content="<?php echo $description; ?>"/>
        <meta property="og:image" content="http://pravonavladu.tsn.ua/og.png?3"/>
        <?php
        //single moment
    } elseif (is_singular('moments')) {
        the_post();
        $title = esc_html(get_the_title() . ' | ' . get_bloginfo('name'));
        $description = esc_html(get_the_excerpt());
        $originalID = icl_object_id(get_the_ID(), 'moments', false, 'uk');
        $thumb_url = wp_get_attachment_url(get_post_thumbnail_id($originalID));
        ?>
        <title><?php echo $title; ?></title>
        <meta property="og:title" content="<?php echo $title; ?>"/>
        <meta property="og:description" content="<?php echo $description; ?>"/>
        <meta name="description" content="<?php echo $description; ?>"/>
        <meta property="og:image" content="<?php echo $thumb_url; ?>"/>
        <?php
        //homepage, default
    } else {
        ?>
        <title><?php bloginfo('name'); ?></title>
        <meta property="og:description" content="<?php echo esc_html(get_bloginfo('description')); ?>"/>
        <meta name="description" content="<?php echo $description; ?>"/>
        <meta property="og:title" content="<?php echo esc_html(get_bloginfo('name')); ?>"/>
        <meta property="og:image" content="http://pravonavladu.tsn.ua/og.png?3"/>
    <?php } ?>

    <meta property="og:url"
          content="http://<?php echo $_SERVER['HTTP_HOST']; ?><?php echo $_SERVER['REQUEST_URI']; ?>"/>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

    <?php wp_head(); ?>
</head>
<?php
$classes = array();

if (IS_PLACEHOLDER) $classes[] = 'placeholder';
if (IS_SPECIAL_EPISODE) $classes[] = 'special_episode';
if ($is_episode) {
    if (get_field('is_poll', 'voting_' . $term->term_id)) $classes[] = 'poll-episode';
} else {
    if (IS_POLL) $classes[] = 'poll-episode';
}
?>
<body <?php body_class($classes); ?> data-network="" data-language="<?php echo ICL_LANGUAGE_CODE; ?>">
<?php if (!IS_PLACEHOLDER) { ?>
<aside>
    <img class="logo-mobile" src="<?php echo get_template_directory_uri(); ?>/img/logo_mobile.png"/>
    <nav>
        <ul id="menu">
            <li>
                <a href="<?php echo $base_url; ?>#home">
                    <span class="icon-home"></span><span class="label"><?php _e('Головна'); ?></span>
                </a>
                <span class="arrow-down"></span>
            </li>

            <?php if (IS_SECOND_SUBJECT) { ?>
                <li>
                    <a href="<?php echo $base_url; ?>#subject1">
                        <span class="icon-subject1"></span><span
                            class="label">Тема №1</span>
                    </a>
                    <span class="arrow-down"></span>
                </li>
                <?php
                $second_subject = get_field('second_subject', $current_voting);
                if ($second_subject) {
                    ?>
                    <li>
                        <a href="<?php echo $base_url; ?>#subject2">
                            <span class="icon-subject2"></span><span
                                class="label">Тема №2</span>
                        </a>
                        <span class="arrow-down"></span>
                    </li>
                <?php } ?>
            <?php } else { ?>

                <?php if (!IS_SPECIAL_EPISODE) { ?>
                    <li>
                        <a href="<?php echo $base_url; ?>#vote">
                            <span class="icon-vote"></span><span class="label"><?php if (!IS_POLL) {
                                    _e('Голосувати');
                                } else {
                                    _e('Гості');
                                } ?></span>
                        </a>
                        <span class="arrow-down"></span>
                    </li>
                <?php } ?>
                <?php if (IS_POLL) { ?>
                    <li>
                        <a href="<?php echo $base_url; ?>#poll">
                            <span class="icon-best"></span><span class="label"><?php _e('Голосування'); ?></span>
                        </a>
                        <span class="arrow-down"></span>
                    </li>
                <?php } else { ?>
                    <li>
                        <a href="<?php echo $base_url; ?>#best">
                            <span class="icon-best"></span><span class="label"><?php _e('Найкраще'); ?></span>
                        </a>
                        <span class="arrow-down"></span>
                    </li>
                <?php } ?>
            <?php } ?>
            <li>
                <a href="<?php echo $base_url; ?>#questions">
                    <span class="icon-questions"></span><span class="label"><?php _e('Запитання'); ?></span>
                </a>
                <span class="arrow-down"></span>
            </li>
            <li class="<?php echo $is_archive || $is_episode ? 'current' : ''; ?>">
                <a href="<?php echo get_permalink($archive_page) ?>">
                    <span class="icon-archive"></span><span
                        class="label"><?php echo $archive_page->post_title; ?></span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<aside class="right-aside">
    <div class="langs">
        <?php
        $languages = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str');
        if ($is_episode) {
            remove_all_filters('get_term');
            $original_term_id = icl_object_id(get_queried_object()->term_id, 'voting', false, 'uk');
            $original_term = get_term($original_term_id, 'voting');
            $languages['ru']['url'] = get_home_url() . '/ru/archive/' . $original_term->slug;
        }

        ?>
        <?php if (ICL_LANGUAGE_CODE == 'uk') { ?>
            <a class="lang-ua active" href="<?php echo $languages['uk']['url']; ?>">UA</a>
            <a class="lang-ru to-choose hidden" href="<?php echo $languages['ru']['url']; ?>">RU</a>
        <?php } else { ?>
            <a class="lang-ru active" href="<?php echo $languages['ru']['url']; ?>">RU</a>
            <a class="lang-ua to-choose hidden" href="<?php echo $languages['uk']['url']; ?>">UA</a>
        <?php } ?>
    </div>
    <div class="social-links">
        <a href="javascript:share('Facebook');" class="link-fb" target="_blank"></a>
        <?php if (is_home()) { ?>
            <?php if (!get_field('answers', $current_voting) && get_field('second_answers', $current_voting)) { ?>
                <a href="#poll2" class="red-vote-btn"><?php _e('Голосувати'); ?></a>
            <?php } else if (get_field('answers', $current_voting)) { ?>
                <a href="#poll" class="red-vote-btn"><?php _e('Голосувати'); ?></a>
            <?php } ?>
        <?php } ?>
    </div>

    <?php if ($slug == 'archive') { ?>
        <div>
            <a id="search-icon" href="#"></a>

            <form class="hidden" method="get" id="searchform">
                <input type="text" size="100" value="" placeholder="<?php _e('Пошук...'); ?>"/> <br/>
                <span class="error hidden"><?php _e('Вибачте, але за вашим запросом нічого не знайдено'); ?></span>
            </form>
        </div>
    <?php } ?>
</aside>
<?php } ?>
