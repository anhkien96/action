<?php

namespace Control\Admin;

class SanPham extends \Control\Admin\Base {

    public function index($request) {

        // var_dump($request->getPage());
        $t = microtime(true);
        $this->view->products = \Reg::db()->table('category')->get();
        echo microtime(true) - $t;

        echo '<br/>';
        echo 'Admin San Pham';
        echo '<br/>';
        echo $this->view->kien;
    }
}

// có cần dùng cơ chế đổi service thành use case không
// control action đặt theo tên use case có được không

// tạo cơ chế xác thực token, đăng nhập thì dùng luôn token truyền theo, không dùng cookie, session nữa ?
// cookie lưu token để check xác thực, chú ý time, luôn call db check xác thực, time có thể mặc định hoặc do người dùng muốn bao lâu
// phân quền sẽ reflect quét hết các action, control -> gắn control theo group hoặc action theo từng permission, ...
// gắn theo group có nhóm control, action, permission
// except pass 1 quyền nào đó hoặc ID nào đó
// group system for all

// các loại user như của Odoo