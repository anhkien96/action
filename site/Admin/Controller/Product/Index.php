<?php

namespace site\admin\Controller\Product;
use site\admin\Base\Controller;

class Index extends Controller {

    public function index() {
        echo 'Admin San Pham';
        echo '<br/>';
        echo $this->view->kien;

        $user = \Loader::user();
        var_dump($user);
    }

    public function update($id) {
        // $validator = $this->validator();
        return $id;
    }

    public function kien() {
        // var_dump($this->req->inputList('kien'));
        var_dump($this->req->fileList('kien'));

        echo '<form method="post" enctype="multipart/form-data">
            <input type="file" name="kien[]"><br/>
            <button type="submit">Submit</button>
        </form>';
    }

    public function xac_thuc__get() {
        $validator = \Factory::validator();
        $rules = [
            'user.name' => ['required', 'text', 'min:6', 'in' => ['Kie2n'], function($value, $validator) {
                // var_dump($validator->getValue('user.name'));
                echo '<br/>';
                $validator->addError('user.name', 'custom');
            }],
        ];

        $data = [
            'user' => [
                'name' => 'Kien'
            ]
        ];

        $validator->check($rules, $data);
        var_dump($validator->getErrors());
    }

    public function scanAction() {
        $actions = \Repo\Action::instance()->scanAction();
        var_dump($actions);

        // position de sap xep, keo tha, tien quan tri
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

// dynamic role, permission, rel, res_id dùng class

// rule tạo ra để củng cố hoặc pass permission

// có cần Admin View system các block, widget không?


// ___________

// Automatic system workflow / Udemy

// Tạo một repo chứa danh sách condition, rồi lại liên kết đến ref

// Cây cấu trúc dữ liệu mô phỏng

// Tư duy thống kê
