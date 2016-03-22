<?php

function db_note_list_delete($user, $query_where) {
  doquery("DELETE FROM {{notes}} WHERE `owner` = {$user['id']} {$query_where};");
}

function db_note_get_id_and_owner($note_id_edit) {
  return doquery("SELECT `id`, `owner` FROM {{notes}} WHERE `id` = {$note_id_edit} LIMIT 1 FOR UPDATE", true);
}

/**
 * @param      $user_id
 * @param bool $sticky
 *
 * @return array|bool|mysqli_result|null
 */
function db_note_list_by_owner($user_id, $sticky = false) {
  $sticky = $sticky ? ' AND `sticky` = 1' : '';
  $extra_sort = $sticky ? ' `galaxy` ASC, `system` ASC, `planet` ASC, `planet_type` ASC,' : '';
  $notes_query = doquery("SELECT * FROM `{{notes}}` WHERE `owner` = {$user_id} {$sticky} ORDER BY `priority` DESC, {$extra_sort} `time` DESC");

  return $notes_query;
}

/**
 * @param $note_priority
 * @param $note_title
 * @param $note_text
 * @param $note_galaxy
 * @param $note_system
 * @param $note_planet
 * @param $note_planet_type
 * @param $note_sticky
 * @param $note_id_edit
 */
function db_note_update_by_id($note_priority, $note_title, $note_text, $note_galaxy, $note_system, $note_planet, $note_planet_type, $note_sticky, $note_id_edit) {
  doquery("UPDATE {{notes}} SET `time` = " . SN_TIME_NOW . ", `priority` = {$note_priority}, `title` = '{$note_title}', `text` = '{$note_text}',
        `galaxy` = {$note_galaxy}, `system` = {$note_system}, `planet` = {$note_planet}, `planet_type` = {$note_planet_type}, `sticky` = {$note_sticky}
        WHERE `id` = {$note_id_edit} LIMIT 1;");
}

/**
 * @param $user
 * @param $note_priority
 * @param $note_title
 * @param $note_text
 * @param $note_galaxy
 * @param $note_system
 * @param $note_planet
 * @param $note_planet_type
 * @param $note_sticky
 */
function db_note_insert($user, $note_priority, $note_title, $note_text, $note_galaxy, $note_system, $note_planet, $note_planet_type, $note_sticky) {
  doquery("INSERT INTO {{notes}} SET `owner` = {$user['id']}, `time` = " . SN_TIME_NOW . ", `priority` = {$note_priority}, `title` = '{$note_title}', `text` = '{$note_text}',
        `galaxy` = {$note_galaxy}, `system` = {$note_system}, `planet` = {$note_planet}, `planet_type` = {$note_planet_type}, `sticky` = {$note_sticky};");
}


