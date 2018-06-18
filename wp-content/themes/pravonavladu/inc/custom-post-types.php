<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 02.09.14
 * Time: 11:54
 */


add_action('init', 'create_post_types');

function create_post_types()
{

    register_post_type('contestant',
        array(
            'labels' => array(
                'name' => __('Учасники'),
                'all_items' => __('Учасники'),
                'menu_name' => __('Голосування'),
                'singular_name' => __('Учасник'),
                'add_new' => __('Додати учасника'),
                'add_new_item' => __('Додати учасника'),
                'search_items' => __('Пошук учасникiв'),
                'edit_item' => __('Редагувати учасника'),
            ),
            'menu_position' => 5,
            'public' => true,
            'has_archive' => false,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields')
        )
    );

    register_taxonomy('voting', 'contestant', array(
        'hierarchical' => false,
        'rewrite' => array('slug' => 'archive', 'with_front' => true),
        'labels' => array(
            'name' => __('Епізоди'),
            'singular_name' => __('Епізод'),
            'add_new_item' => __('Додати новий епізод')
        ),
        'show_ui'               => true,
        'show_admin_column'     => true
    ));



	register_post_type('question',
		array(
			'labels' => array(
				'name' => __('Запитання'),
				'all_items' => __('Запитання'),
				'menu_name' => __('Запитання'),
				'singular_name' => __('Запитання'),
				'add_new' => __('Нове запитання'),
				'add_new_item' => __('Додати запитання'),
				'search_items' => __('Пошук запитання'),
				'edit_item' => __('Редагувати запитання'),
			),
			'menu_position' => 6,
			'public' => true,
			'has_archive' => false,
			'supports' => array('title', 'editor', 'custom-fields')
		)
	);

    register_post_type('moments',
        array(
            'labels' => array(
                'name' => __('Момент'),
                'all_items' => __('Моменти'),
                'menu_name' => __('Моменти'),
                'singular_name' => __('Момент'),
                'add_new' => __('Новий момент'),
                'add_new_item' => __('Додати момент'),
                'search_items' => __('Пошук моменту'),
                'edit_item' => __('Редагувати момент'),
            ),
            'menu_position' => 7,
            'public' => true,
            'has_archive' => false,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields')
        )
    );
}


add_filter( 'manage_moments_posts_columns', 'set_custom_moments_columns' );
add_action( 'manage_moments_posts_custom_column' , 'custom_moments_column', 1, 2 );

function set_custom_moments_columns($columns) {
    $result = array();
    $i = 0;

    foreach ($columns as $k => $c) {
        if ($i++ == 2) {
            $result['episode'] = 'Епiзод';
        }
        $result[$k] = $c;
    }
    return $result;
}

function custom_moments_column( $column, $post_id ) {
    switch ( $column ) {
        case 'episode' :
            $term = get_term(get_field('episode', $post_id), 'voting');
            echo $term->name;
            break;
    }
}

add_filter( 'manage_question_posts_columns', 'set_custom_question_columns' );
add_action( 'manage_question_posts_custom_column' , 'custom_question_column', 1, 2 );

function set_custom_question_columns($columns) {
    $result = array();
    $i = 0;

    foreach ($columns as $k => $c) {
        if ($i++ == 2) {
            $result['question'] = 'Питання';
        }

        if ($i == 3) {
            $result['city'] = 'Мiсто';
            $result['question_to'] = 'Питання до';
            $result['episode'] = 'Епiзод';
        }
        $result[$k] = $c;
    }
    return $result;
}


function custom_question_column( $column, $post_id ) {
    switch ( $column ) {
        case 'question' :
            echo get_the_content($post_id);
            break;
        case 'city' :
            echo get_field('city', $post_id);
            break;
        case 'question_to' :
            $question_to_guest = get_field('question_to_guest', $post_id);

            if ($question_to_guest) {
                $episode = get_post_meta($post_id, 'episod', true);

                $voting = get_term($episode, 'voting');


                $guests1 = (array)get_field('guests_in_studio', $voting);
                $guests2 = (array)get_field('second_guests_in_studio', $voting);
                $guests = array_merge($guests1, $guests2);

                foreach ($guests as $guest) {
                    if ($guest['id'] == $question_to_guest) {
                        echo $guest['name_to'];
                        return;
                    }
                }

            } else {
                $question_to = get_field('question_to', $post_id);

                if ($question_to) {
                    echo get_the_title($question_to);
                    return;
                }
            }

            echo 'До всiх';
            break;
        case 'episode' :
            $term = get_term(get_field('episod', $post_id), 'voting');
            echo $term->name;
            break;
    }
}
