<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 02.09.14
 * Time: 1:30
 */

session_start();

//Hybrid Auth
require get_template_directory() . '/inc/hybridauth/Hybrid/Auth.php';


class SocialLogin
{
    static function getInstance() {
        $config = require get_template_directory() . '/inc/hybridauth/config.php';
        return new Hybrid_Auth($config);

    }

    //return adapter for logged in user
    static function getLoggedUser() {
        $hybridauth = self::getInstance();

        $connectedAccounts = $hybridauth->getConnectedProviders();

        if (!$connectedAccounts) {
            return false;
        }

        $network = $connectedAccounts[0];
        return $hybridauth->authenticate($network);
    }

    static function getUser($network)
    {
        $hybridauth = self::getInstance();

        $adapter = $hybridauth->authenticate($network);
        $profile = $adapter->getUserProfile();

        $id = $profile->identifier;

        return $id;

    }

    static function vote($contestant_id, $network, $user_id, $second_poll = false)
    {
        global $wpdb;

        $ip = $_SERVER['REMOTE_ADDR'];

        /*
        $terms = wp_get_post_terms($contestant_id, 'voting');
        $term_id = $terms[0]->term_id;
        */
        $term_id = get_field('active_voting', 'options');

        $votingStage = get_field('voting_stage', 'voting_' . $term_id);

        $table_name = $wpdb->prefix . "votes";
        if ($votingStage == 'voting_finished') {
            $table_name = $wpdb->prefix . "votes_weekly";
            return false;
        }

        if ($second_poll) {
            $table_name = $wpdb->prefix . "second_votes";
        }

        $alreadyVoted = $wpdb->get_results("
            SELECT *
            FROM " . $table_name . "
            WHERE `network` = '" . $wpdb->escape($network) . "'
                AND `user_id` = '" . $wpdb->escape($user_id) . "'
                AND `term_id` = '" . $wpdb->escape($term_id) . "'
        ");

        if ($alreadyVoted) {
            setcookie("voted" . ($second_poll ? '2' : ''), $alreadyVoted[0]->post_id, time()+(3600*3), '/');
            return 'already_' . $alreadyVoted[0]->post_id;
        }

        $geo = getIpLocation($ip);


        $wpdb->insert($table_name, array(
                'post_id' => $contestant_id,
                'term_id' => $term_id,
                'network' => $network,
                'user_id' => $user_id,
                'ip' => $ip,
                'geo_continent' => $geo['continent']['names']['en'],
                'geo_country' => $geo['country']['names']['en'],
                'geo_region' => isset($geo['subdivisions'][0]['names']['ru']) ? $geo['subdivisions'][0]['names']['ru'] : $geo['subdivisions'][0]['names']['en'],
                'geo_city' => isset($geo['city']['names']['ru']) ? $geo['city']['names']['ru'] : $geo['city']['names']['en']
        ));

        return $contestant_id;
    }

    static function preVote() {
        $user = self::getLoggedUser();

        if (!$user) {
            echo 'login_needed';
        } else {
            $profile = $user->getUserProfile();
            if (!$profile) {
                echo 'login_needed';
                die();
            }

            $second_poll = false;
            if (isset($_POST['second_poll'])) {
                $second_poll = (bool)$_POST['second_poll'];
            }

            $contestant_id = self::vote($_POST['contestant_id'], $user->id, $profile->identifier, $second_poll);
            echo $contestant_id;

        }

        die();
    }
}

if (isset($_REQUEST['hauth_start']) || isset($_REQUEST['hauth_done'])) {
    require get_template_directory() . '/inc/hybridauth/Hybrid/Endpoint.php';
    Hybrid_Endpoint::process();
}

if (isset($_GET['action']) && $_GET['action'] == 'login' && isset($_GET['network']) && in_array($_GET['network'], array('Facebook', 'Vkontakte', 'Odnoklassniki')) && isset($_GET['contestant_id']) && is_numeric($_GET['contestant_id'])) {
    $user_id = SocialLogin::getUser($_GET['network']);
    ?>
    <script>
        window.opener.voteCallback(<?php echo $_GET['contestant_id']; ?>, '<?php echo $_GET['network']; ?>');
        window.close();
    </script>
    <?php
    die();
}

add_action( 'wp_ajax_preVote', array('SocialLogin', 'preVote') );
add_action( 'wp_ajax_nopriv_preVote', array('SocialLogin', 'preVote') );