<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$lang_translator['author'] = 'VINADES.,JSC (contact@vinades.vn)';
$lang_translator['createdate'] = '27/01/2014, 00:08';
$lang_translator['copyright'] = '@Copyright (C) 2009-2021 VINADES.,JSC. All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

$lang_module['main'] = 'Trang chính';
$lang_module['config'] = 'Cấu hình';
$lang_module['save'] = 'Lưu cấu hình';

$lang_module['comment'] = 'Quản lý bình luận';
$lang_module['edit'] = 'Sửa';
$lang_module['search'] = 'Tìm kiếm';
$lang_module['search_type'] = 'Tìm kiếm theo';
$lang_module['search_status'] = 'Trạng thái';
$lang_module['search_id'] = 'ID';
$lang_module['search_key'] = 'Từ khóa tìm kiếm';
$lang_module['search_module'] = 'Tìm trong module';
$lang_module['search_module_all'] = 'Tất cả các module';
$lang_module['search_content'] = 'Nội dung bình luận';
$lang_module['search_content_id'] = 'ID bài viết';
$lang_module['search_post_name'] = 'Người đăng';
$lang_module['search_post_email'] = 'Email';
$lang_module['search_per_page'] = 'Số bài viết hiển thị';
$lang_module['from_date'] = 'Từ ngày';
$lang_module['to_date'] = 'Đến ngày';
$lang_module['search_note'] = 'Từ khóa tìm kiếm không ít hơn 2 ký tự, không lớn hơn 64 ký tự, không dùng các mã html';

$lang_module['edit_title'] = 'Sửa bình luận';
$lang_module['edit_active'] = 'Kích hoạt';
$lang_module['edit_delete'] = 'Xóa bình luận';
$lang_module['delete'] = 'Xóa';
$lang_module['funcs'] = 'Chức năng';
$lang_module['email'] = 'Người gửi';
$lang_module['content'] = 'Nội dung';
$lang_module['status'] = 'Trạng thái';
$lang_module['delete_title'] = 'Xóa bình luận';
$lang_module['delete_confirm'] = 'Bạn có chắc muốn xóa bình luận ?';
$lang_module['delete_yes'] = 'Có';
$lang_module['delete_no'] = 'Không';
$lang_module['delete_accept'] = 'Lưu thay đổi';
$lang_module['delete_unsuccess'] = 'Có lỗi trong quá trình xóa dữ liệu';
$lang_module['delete_success'] = 'Xóa dữ liệu thành công';
$lang_module['enable'] = 'Bật';
$lang_module['disable'] = 'Tắt';
$lang_module['checkall'] = 'Chọn tất cả';
$lang_module['uncheckall'] = 'Bỏ chọn tất cả';
$lang_module['nocheck'] = 'Hãy chọn ít nhất 1 bình luận để có thể thực hiện';
$lang_module['update_success'] = 'Cập nhật thành công !';

$lang_module['mod_name'] = 'Tên module';
$lang_module['weight'] = 'STT';
$lang_module['config_mod_name'] = 'Cấu hình bình luận module: %s';
$lang_module['activecomm'] = 'Sử dụng chức năng bình luận';
$lang_module['emailcomm'] = 'Hiển thị email của người bình luận';
$lang_module['setcomm'] = 'Thảo luận mặc định khi tạo bài viết mới';
$lang_module['auto_postcomm'] = 'Kiểm duyệt bình luận';
$lang_module['auto_postcomm_0'] = 'Kiểm duyệt tất cả';
$lang_module['auto_postcomm_1'] = 'Không kiểm duyệt';
$lang_module['auto_postcomm_2'] = 'Kiểm duyệt nếu không là thành viên';
$lang_module['perpagecomm'] = 'Số bình luận hiển thị trên một trang';
$lang_module['perpagecomm_note'] = 'Nhập tối thiểu 1 và không nên quá 100';
$lang_module['timeoutcomm'] = 'Thời gian (giây) tối thiểu giữa hai lần gửi bình luận';
$lang_module['timeoutcomm_note'] = 'Nhập 0 nếu không giới hạn. Lưu ý nên bật captcha nếu chọn giá trị này là 0, giá trị này không tính cho Admin';
$lang_module['allowattachcomm'] = 'Cho phép đính kèm file';
$lang_module['alloweditorcomm'] = 'Cho phép trình soạn thảo';

$lang_module['adminscomm'] = 'Admin quản lý bình luận';
$lang_module['view_comm'] = 'Ai được phép xem bình luận';
$lang_module['allowed_comm'] = 'Ai được phép đăng bình luận';
$lang_module['allowed_comm_item'] = 'Theo cấu hình bài viết';
$lang_module['adminscomm_note'] = 'Chức năng "Admin quản lý bình luận" chỉ áp dụng cho admin Quản lý module, Bạn cần thêm người Quản lý module trước khi phân quyền';

$lang_module['sortcomm'] = 'Sắp xếp bình luận theo';
$lang_module['sortcomm_0'] = 'Mới trước';
$lang_module['sortcomm_1'] = 'Cũ trước';
$lang_module['sortcomm_2'] = 'Like nhiều trước';

$lang_module['siteinfo_queue_comments'] = 'Số bình luận chờ duyệt';
$lang_module['notification_comment_queue'] = 'Kiểm duyệt bình luận gửi bởi %s<br /><em>%s</em>';
$lang_module['attach'] = 'Đính kèm file';
$lang_module['attach_choose'] = 'Chọn';
$lang_module['attach_view'] = 'Truy cập';
$lang_module['attach_download'] = 'Tải đính kèm';
