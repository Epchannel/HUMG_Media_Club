<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_THEMES')) {
    exit('Stop!!!');
}

$bid = $nv_Request->get_int('bid', 'post');
$checkss = $nv_Request->get_string('checkss', 'post');
list($bid, $theme, $position) = $db->query('SELECT bid, theme, position FROM ' . NV_BLOCKS_TABLE . '_groups WHERE bid=' . $bid)->fetch(3);

if ($bid > 0 and (md5($theme . NV_CHECK_SESSION) == $checkss or md5(NV_CHECK_SESSION . '_' . $bid) == $checkss)) {
    $db->query('DELETE FROM ' . NV_BLOCKS_TABLE . '_groups WHERE bid=' . $bid);
    $db->query('DELETE FROM ' . NV_BLOCKS_TABLE . '_weight WHERE bid=' . $bid);

    // reupdate
    $weight = 0;
    $sth = $db->prepare('SELECT bid FROM ' . NV_BLOCKS_TABLE . '_groups WHERE theme=:theme AND position=:position ORDER BY weight ASC');
    $sth->bindParam(':theme', $theme, PDO::PARAM_STR);
    $sth->bindParam(':position', $position, PDO::PARAM_STR);
    $sth->execute();
    while (list($bid_i) = $sth->fetch(3)) {
        ++$weight;
        $db->query('UPDATE ' . NV_BLOCKS_TABLE . '_groups SET weight=' . $weight . ' WHERE bid=' . $bid_i);
    }

    $func_id_old = $weight = 0;
    $sth = $db->prepare('SELECT t1.bid, t1.func_id FROM ' . NV_BLOCKS_TABLE . '_weight t1 INNER JOIN ' . NV_BLOCKS_TABLE . '_groups t2
		ON t1.bid = t2.bid
		WHERE t2.theme=:theme AND t2.position=:position ORDER BY t1.func_id ASC, t1.weight ASC');
    $sth->bindParam(':theme', $theme, PDO::PARAM_STR);
    $sth->bindParam(':position', $position, PDO::PARAM_STR);
    $sth->execute();
    while (list($bid_i, $func_id_i) = $sth->fetch(3)) {
        if ($func_id_i == $func_id_old) {
            ++$weight;
        } else {
            $weight = 1;
            $func_id_old = $func_id_i;
        }

        $db->query('UPDATE ' . NV_BLOCKS_TABLE . '_weight SET weight=' . $weight . ' WHERE bid=' . $bid_i . ' AND func_id=' . $func_id_i);
    }

    $nv_Cache->delMod('themes');

    $db->query('OPTIMIZE TABLE ' . NV_BLOCKS_TABLE . '_groups');
    $db->query('OPTIMIZE TABLE ' . NV_BLOCKS_TABLE . '_weight');

    echo $lang_module['block_delete_success'];
} else {
    echo $lang_module['block_front_delete_error'];
}
