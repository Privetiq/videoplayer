<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 11.09.14
 * Time: 16:06
 */

require_once("wp-load.php");

if (isset($_GET['episode']) && $_GET['episode'] ) {
    $current_voting = get_term_by('slug', $_GET['episode'], 'voting');
    $current_voting_id = $current_voting->term_id;
} else {
    $current_voting_id = get_field('active_voting', 'options');
    $current_voting = get_term($current_voting_id, 'voting');
}




$is_poll = get_field('is_poll', 'voting_' . $current_voting_id);

if (!$is_poll) {
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
        )
    ));
} else {
    $answers = get_field((isset($_GET['poll']) && $_GET['poll'] == 2 ? 'second_' : '') . 'answers', 'voting_' . $current_voting_id);
    $contestants = array();

    foreach ($answers as $answer) {
        $contestants[$answer['id']] = new stdClass;
        $contestants[$answer['id']]->ID = $answer['id'];
        $contestants[$answer['id']]->post_title = $answer['answer'];
    }
}

if (!$contestants) {
    die('No data');
}

$contestantsIds = array();
foreach ($contestants as $contestant) {
    $contestantsIds[] = $contestant->ID;
}

header("Content-Type:text/xml");
echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';

if (isset($_GET['geo_stats'])) {
    $sql = "
        SELECT *, COUNT(*) cnt
        FROM " . $wpdb->prefix . (isset($_GET['poll']) && $_GET['poll'] == 2 ? 'second_' : '') . "votes
        WHERE geo_country = 'Ukraine'
            AND (geo_region <> '' OR geo_city <> '')
            AND term_id = " . (int)$current_voting_id . "
        GROUP BY post_id, geo_region
        ORDER BY cnt DESC";
    $results = $wpdb->get_results($sql);

    $stats = array();
    foreach ($results as $result) {
        if (!isset($stats[$result->post_id])) {
            $stats[$result->post_id] = array();
        }

        $result->geo_region = str_replace(array(
            'Киев',
            'Одесса',
        ), array(
            'Киевская область',
            'Одесская область',
        ), $result->geo_region);

        if (!isset($stats[$result->post_id][$result->geo_region])) {
            $stats[$result->post_id][$result->geo_region] = 0;
        }
        $stats[$result->post_id][$result->geo_region] += $result->cnt;
    }
    ?>
    <root>
    <?php foreach ($stats as $id => $stats) { ?>
        <person>
            <num><?php echo $id; ?></num>
            <name><?php echo $is_poll ? $contestants[$id]->post_title : get_the_title($id); ?></name>
            <regions>
                <?php foreach ($stats as $region => $vote) { ?>
                    <region>
                        <name><?php echo $region; ?></name>
                        <vote><?php echo $vote; ?></vote>
                    </region>
                <?php } ?>
            </regions>
        </person>
    <?php } ?>

    </root><?php
    die();
}

$total_votes = 0;
foreach ($contestants as &$p) {
    $p->votes = getVotes($p->ID, $current_voting_id, false, (isset($_GET['poll']) && $_GET['poll'] == 2 ? true : false));
    $total_votes += $p->votes;
}

?>
<root>
<?php foreach ($contestants as $i => $person) { ?>
    <?php
    if ($total_votes > 0) {
        $perc = $person->votes / $total_votes * 100;
        if (strpos($perc, '.') !== false) {
            $perc = number_format($perc, 2, '.', '');
        }
    } else {
        $perc = 0;
    }
    ?>
    <person>
        <num><?php echo $i + 1; ?></num>
        <name><?php echo $person->post_title; ?></name>
        <vote><?php echo $person->votes; ?></vote>

        <pc><?php echo $perc; ?>%</pc>
    </person>
<?php } ?>
</root><?php die(); ?>