<?php
/**
 * Created by PhpStorm.
 * User: dmr5bq
 * Date: 10/18/16
 * Time: 1:59 PM
*/

function get_word_counts_array($string) {
    $array_of_words = preg_split('/[ ,_.]+/', $string);

    $unique_words = get_unique_words_array($array_of_words);

    $return_array = generate_empty_counts_array($unique_words);

    $len = count($return_array);

    foreach ($array_of_words as $word) {
        for ($i = 0 ; $i < $len ; $i++ ) {

            if (strcmp($return_array[$i][0], $word) == 0) {
                $return_array[$i][1] += 1;
            }
        }
    }
    return $return_array;
}

function get_unique_words_array($array_of_words) {

    $unique_words = array();
    foreach ($array_of_words as $word):
        $word = rtrim($word);

        if (!in_array($word, $unique_words))  {
            $unique_words[] = $word;
        }
    endforeach;

    return $unique_words;
}

function generate_empty_counts_array($unique_words) {

    $return_array = array();

    foreach ($unique_words as $unique_word) {
        $entry = array($unique_word, 0);
        $return_array[] = $entry;
    }

    return $return_array;
}

function get_similarity($string1, $string2) {

    $to_array1 = preg_split('/[ ,_.]+/', $string1);
    $to_array2 = preg_split('/[ ,_.]+/', $string2);

    $counts_array1 = get_word_counts_array($string1);
    $counts_array2 = get_word_counts_array($string2);

    $total_words = count($to_array1) + count($to_array2);

    $shared_words = 0;

    foreach($counts_array1 as $item1) {
        foreach($counts_array2 as $item2) {
            if (strcmp($item2[0], $item1[0]) == 0) {
                $shared_words += min ($item1[1], $item2[1]);
                break;
            }
        }
    }

    return 2 * $shared_words / $total_words;
}
