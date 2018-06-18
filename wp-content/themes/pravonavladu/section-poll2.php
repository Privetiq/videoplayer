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
} else {
    $current_voting = get_term(CURRENT_VOTING_ID, 'voting');
}

$answers = get_field('second_answers', $current_voting);

if (!$answers) {
    return;
}

//current iteration
$votingStage = get_field('voting_stage', $current_voting);


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
?>



<section id="poll2">
            <h2><?php _e('Голосування'); ?></h2>

<div class="content">
    <h3><?php the_field('second_question', $current_voting); ?></h3>


    <?php if (!$showVotingResults) { ?>
        <?php foreach ($answers as $answer) { ?>
            <a href="#"
               class="<?php if ($votingNotStarted || $votingIsFinished) { ?>disabled<?php } ?> poll-answer vote-btn second-poll"
               data-contestantid="<?php echo $answer['id']; ?>"><?php echo $answer['answer']; ?></a>
        <?php } ?>
    <?php } else { ?>
        <?php foreach ($answers as $answer) { ?>
            <div class="poll-answer-row clearfix">
                <label><?php echo $answer['answer']; ?></label>

                <div class="poll-answer-holder">
                    <div style="width: <?php echo $answer['result']; ?>%;" class="poll-answer-result"></div>
                </div>
                <div class="poll-answer-perc"><?php echo $answer['result']; ?>%</div>
            </div>
        <?php } ?>
    <?php } ?>
</div>
</section>