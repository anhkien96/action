<?php

namespace Security\Rule\Blog;

class Create {

    // public function getPermission() {
    //     return 'admin.create';
    // }

    // public function allowUser_ref_Kien() {
    //     // \Reg::set('user', $user);
    //      $user = \Reg::user();
    // }

    public function handle($user) {
        $ref_name = 'admin.user'
        // $ref = \Repo\Ref::instance()->load($ref_name);
        $ref = \Repo\Ref::load($ref_name); // viết thêm custom static hàm cho nó

        if ($user->id == $ref['src_id']) {
            return true;
        }
        return false;
    }
}

// có bộ quản trị ref

// mặc định không có hoặc false, rule sẽ thêm vào và sửa / đè quyền
// chú ý thêm priority khi có nhiều rule

// rule sẽ tạo từ quản trị, cho permission nào và link đến code
// eg:
// admin.user.create | Blog\Create (prefix giống nhau hết nên lưu phần sau là đủ)
// quản hệ nhiều nhiều hoặc lưu thành mảng (join '|', json_encode)

// rule cũng có thể coi là 1 kiểu dynamic permission
// hoặc rule là các filter của permission
// chắc tạo 1 phần giao diện để mô hình hóa lên cho dễ
// ant design

// repo, model sequence

// cách các model đã load theo id
