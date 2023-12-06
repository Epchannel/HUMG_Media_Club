<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_SETTINGS')) {
    exit('Stop!!!');
}

$errormess = $lang_module['plugin_info'];
$pattern_plugin = '/^([a-zA-Z0-9\_]+)\.php$/';
$checkss = md5(NV_CHECK_SESSION . '_' . $module_name . '_' . $op . '_' . $admin_info['userid']);

$plugin_file = $nv_Request->get_title('plugin_file', 'post,get');
if ($checkss == $nv_Request->get_string('checkss', 'post') and $nv_Request->isset_request('plugin_file', 'post')) {
    $config_plugin = [];
    if (preg_match($pattern_plugin, $plugin_file) and nv_is_file(NV_BASE_SITEURL . 'includes/plugin/' . $plugin_file, 'includes/plugin')) {
        $plugin_area = $nv_Request->get_int('plugin_area', 'post');
        if ($nv_Request->isset_request('delete', 'post')) {
            $sth = $db->prepare('SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_plugin WHERE plugin_file=:plugin_file');
            $sth->bindParam(':plugin_file', $plugin_file, PDO::PARAM_STR, strlen($title));
            $sth->execute();
            $count = $sth->fetchColumn();
            if (empty($count)) {
                nv_deletefile(NV_ROOTDIR . '/includes/plugin/' . $plugin_file);
            }
        } elseif (!empty($plugin_area)) {
            $_sql = 'SELECT max(weight) FROM ' . $db_config['prefix'] . '_plugin WHERE plugin_area=' . $plugin_area;
            $weight = $db->query($_sql)->fetchColumn();
            $weight = (int) $weight + 1;

            try {
                $sth = $db->prepare('INSERT INTO ' . $db_config['prefix'] . '_plugin (plugin_file, plugin_area, weight) VALUES (:plugin_file, :plugin_area, :weight)');
                $sth->bindParam(':plugin_file', $plugin_file, PDO::PARAM_STR);
                $sth->bindParam(':plugin_area', $plugin_area, PDO::PARAM_INT);
                $sth->bindParam(':weight', $weight, PDO::PARAM_INT);
                $sth->execute();

                nv_save_file_config_global();
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass());
    }
}
if ($nv_Request->isset_request('dpid', 'get')) {
    $dpid = $nv_Request->get_int('dpid', 'get');
    $checkss = $nv_Request->get_title('checkss', 'get');
    if ($dpid > 0 and $checkss == md5($dpid . '-' . NV_CHECK_SESSION)) {
        $row = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_plugin WHERE pid=' . $dpid)->fetch();
        if (!empty($row) and $db->exec('DELETE FROM ' . $db_config['prefix'] . '_plugin WHERE pid = ' . $dpid)) {
            $weight = (int) ($row['weight']);
            $_query = $db->query('SELECT pid FROM ' . $db_config['prefix'] . '_plugin WHERE plugin_area=' . $row['plugin_area'] . ' AND weight > ' . $weight . ' ORDER BY weight ASC');
            while (list($pid) = $_query->fetch(3)) {
                $db->query('UPDATE ' . $db_config['prefix'] . '_plugin SET weight = ' . $weight++ . ' WHERE pid=' . $pid);
            }

            nv_save_file_config_global();
        }
    }
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass());
} elseif ($nv_Request->isset_request('pid', 'get') and $nv_Request->isset_request('weight', 'get')) {
    $pid = $nv_Request->get_int('pid', 'get');
    $row = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_plugin WHERE pid=' . $pid)->fetch();
    if (!empty($row)) {
        $new = $nv_Request->get_int('weight', 'get');

        $weight = 0;
        $_query = $db->query('SELECT pid FROM ' . $db_config['prefix'] . '_plugin WHERE plugin_area=' . $row['plugin_area'] . ' AND pid != ' . $pid . ' ORDER BY weight ASC');
        while (list($pid_i) = $_query->fetch(3)) {
            ++$weight;
            if ($weight == $new) {
                ++$weight;
            }
            $db->query('UPDATE ' . $db_config['prefix'] . '_plugin SET weight = ' . $weight . ' WHERE pid=' . $pid_i);
        }
        $db->query('UPDATE ' . $db_config['prefix'] . '_plugin SET weight = ' . $new . ' WHERE pid=' . $pid);

        nv_save_file_config_global();
    }
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass());
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('OP', $op);
$xtpl->assign('CHECKSS', $checkss);

$plugin_new = [];
$plugin_all = nv_scandir(NV_ROOTDIR . '/includes/plugin', $pattern_plugin);

$nv_plugin_array = [];
$_nv_plugin_area = [];
$_sql = 'SELECT * FROM ' . $db_config['prefix'] . '_plugin ORDER BY plugin_area ASC, weight ASC';
$_query = $db->query($_sql);
while ($row = $_query->fetch()) {
    $_nv_plugin_area[$row['plugin_area']][] = $row;
    $nv_plugin_array[] = $row['plugin_file'];
}

foreach ($_nv_plugin_area as $area => $nv_plugin_area_i) {
    $_sizeof = sizeof($nv_plugin_area_i);
    foreach ($nv_plugin_area_i as $row) {
        $row['plugin_area'] = ($row['weight'] == 1) ? $lang_module['plugin_area_' . $row['plugin_area']] : '';
        $row['plugin_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;dpid=' . $row['pid'] . '&amp;checkss=' . md5($row['pid'] . '-' . NV_CHECK_SESSION);
        $xtpl->assign('DATA', $row);
        for ($i = 1; $i <= $_sizeof; ++$i) {
            $xtpl->assign('WEIGHT_SELECTED', ($i == $row['weight']) ? ' selected="selected"' : '');
            $xtpl->assign('WEIGHT', $i);
            $xtpl->parse('main.loop.weight');
        }
        $xtpl->parse('main.loop');
    }
}

foreach ($plugin_all as $_file) {
    if (!in_array($_file, $nv_plugin_array, true)) {
        $plugin_new[] = $_file;
    }
}

if ($errormess != '') {
    $xtpl->assign('ERROR', $errormess);
    $xtpl->parse('main.error');
}

if (!empty($plugin_new)) {
    foreach ($plugin_new as $_file) {
        $xtpl->assign('PLUGIN_FILE', $_file);
        $xtpl->assign('PLUGIN_SELECTED', $_file == $plugin_file ? 'selected="selected"' : '');
        $xtpl->parse('main.add.file');
    }

    $array_plugin_position = [];
    if (preg_match($pattern_plugin, $plugin_file, $_m) and nv_is_file(NV_BASE_SITEURL . 'includes/plugin/' . $plugin_file, 'includes/plugin')) {
        if (file_exists(NV_ROOTDIR . '/includes/plugin/' . $_m[1] . '.ini')) {
            if ($xml = @simplexml_load_file(NV_ROOTDIR . '/includes/plugin/' . $_m[1] . '.ini')) {
                $position = $xml->xpath('positions');
                $positions = $position[0]->position;
                for ($j = 0, $count = sizeof($positions); $j < $count; ++$j) {
                    $_index = $positions[$j]->id;
                    if ($_index >= 1 and $_index <= 5) {
                        $xtpl->assign('AREA_VALUE', $_index);
                        $xtpl->assign('AREA_TEXT', $lang_module['plugin_area_' . $_index]);
                        $xtpl->parse('main.add.area');
                    }
                }
                $info = $xml->xpath('info');
                if (!empty($info[0]->description)) {
                    $xtpl->assign('NAME', $info[0]->name);
                    $xtpl->assign('DESCRIPTION', $info[0]->description);
                    $xtpl->parse('main.add.info');
                }
            }
        }
    }
    $xtpl->parse('main.add');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['plugin'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
