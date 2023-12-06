<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_EXTENSIONS')) {
    exit('Stop!!!');
}

$page_title = $lang_global['mod_extensions'];

$request = [];

// Fixed request
$request['lang'] = NV_LANG_DATA;
$request['basever'] = $global_config['version'];
$request['mode'] = 'detail';

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);

$request['id'] = $nv_Request->get_int('id', 'get', 0);

$NV_Http = new NukeViet\Http\Http($global_config, NV_TEMP_DIR);

// Debug
$args = [
    'headers' => [
        'Referer' => NUKEVIET_STORE_APIURL,
    ],
    'body' => $request
];

$array = $NV_Http->post(NUKEVIET_STORE_APIURL, $args);
$array = (is_array($array) and !empty($array['body'])) ? @unserialize($array['body']) : [];

$error = '';
if (!empty(NukeViet\Http\Http::$error)) {
    $error = nv_http_get_lang(NukeViet\Http\Http::$error);
} elseif (empty($array['status']) or !isset($array['error']) or !isset($array['data']) or !isset($array['pagination']) or !is_array($array['error']) or !is_array($array['data']) or !is_array($array['pagination']) or (!empty($array['error']) and (!isset($array['error']['level']) or empty($array['error']['message'])))) {
    $error = $lang_global['error_valid_response'];
} elseif (!empty($array['error']['message'])) {
    $error = $array['error']['message'];
}

// Show error
if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
} else {
    $array = $array['data'];
    $array_files = $array['files'];
    $array_images = $array['image_demo'];
    unset($array['files'], $array['image_demo']);

    // Change some variable to display value
    $array['updatetime'] = nv_date('H:i d/m/Y', $array['updatetime']);
    $array['view_hits'] = number_format($array['view_hits'], 0, '.', '.');
    $array['download_hits'] = number_format($array['download_hits'], 0, '.', '.');
    $array['rating_text'] = sprintf($lang_module['rating_text_detail'], number_format($array['rating_totals'], 0, '.', '.'), number_format($array['rating_hits'], 0, '.', '.'));
    $array['compatible_class'] = empty($array['compatible']) ? 'text-danger' : 'text-success';
    $array['compatible_title'] = empty($array['compatible']) ? $lang_module['incompatible'] : $lang_module['compatible'];
    $array['install_link'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=install&amp;id=' . $array['id'];
    $array['price'] = $array['price'] ? (preg_replace("/\,0$/", '', number_format($array['price'], 1, ',', '.')) . ' ' . $array['currency']) : $lang_module['free'];

    $xtpl->assign('DATA', $array);

    if (empty($array['documentation'])) {
        $xtpl->parse('main.data.empty_documentation');
    }

    if (!empty($array_images)) {
        foreach ($array_images as $image) {
            $xtpl->assign('IMAGE', $image);
            $xtpl->parse('main.data.demo_images.loop');
        }

        $xtpl->parse('main.data.demo_images');
    } else {
        $xtpl->parse('main.data.empty_images');
    }

    if (!empty($array['compatible']) and ($global_config['extension_setup'] == 2 or $global_config['extension_setup'] == 3)) {
        $xtpl->parse('main.data.install');
    }

    foreach ($array_files as $file) {
        $file['compatible_class'] = empty($file['compatible']) ? 'text-danger' : 'text-success';
        $file['compatible_title'] = empty($file['compatible']) ? $lang_module['incompatible'] : $lang_module['compatible'];
        $file['install_link'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=install&amp;id=' . $array['id'] . '&amp;fid=' . $file['id'];
        $file['price'] = $file['price'] ? (preg_replace("/\,0$/", '', number_format($file['price'], 1, ',', '.')) . ' ' . $file['currency']) : $lang_module['free'];

        $xtpl->assign('FILE', $file);

        if ($file['type'] == 1 and !empty($file['compatible']) and ($global_config['extension_setup'] == 2 or $global_config['extension_setup'] == 3)) {
            $xtpl->parse('main.data.file.install');
        } else {
            $xtpl->parse('main.data.file.download');
        }

        $xtpl->parse('main.data.file');
    }

    $xtpl->parse('main.data');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents, 0);
include NV_ROOTDIR . '/includes/footer.php';
