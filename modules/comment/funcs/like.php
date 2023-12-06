<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_MOD_COMMENT')) {
    exit('Stop!!!');
}

$difftimeout = 360;

$contents = 'ERR_' . $lang_module['comment_unsuccess'];

$cid = $nv_Request->get_int('cid', 'post');
$checkss = $nv_Request->get_string('checkss', 'post');

if ($cid > 0 and $checkss == md5($cid . '_' . NV_CHECK_SESSION)) {
    if ($nv_Request->isset_request($module_data . '_like_' . $cid, 'cookie')) {
        $contents = 'ERR_' . $lang_module['like_unsuccess'];
    } else {
        $nv_Request->set_Cookie($module_data . '_like_' . $cid, 1, 86400);

        $_sql = 'SELECT cid, likes, dislikes FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE cid=' . $cid;
        $row = $db->query($_sql)->fetch();
        if (isset($row['cid'])) {
            $like = $nv_Request->get_int('like', 'post');
            $query = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . ' SET';
            if ($like > 0) {
                $contents = 'OK_like' . $cid . '_' . ($row['likes'] + 1);
                $query .= ' likes=likes+1';
            } else {
                $contents = 'OK_dislike' . $cid . '_' . ($row['dislikes'] + 1);
                $query .= ' dislikes=dislikes+1';
            }
            $query .= ' WHERE cid=' . $cid;
            $db->query($query);
        }
    }
}
include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';
