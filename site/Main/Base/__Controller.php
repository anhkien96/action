<?php

namespace Base;

class Controller {

    protected $view;//, $_middleware = [];

    public function __construct() {
        $this->view = \Reg::get('view');
    }

    // protected function middleware($name) {
    //     // thêm to doing
    //     $this->_middleware[] = $name;
    //     return $this;
    // }

    // public function internalMiddleware() {
    //     $req = \Reg::get('request');
    //     $next = function() use($req) {
    //         $action = $req->getAction();
    //         $handle = [$this, $action. '__' .$req->method()];
    //         if (!is_callable($handle)) {
    //             $handle = [$this, $action];
    //         }
    //         if (is_callable($handle)) {
    //             return $handle($this->req);
    //         }
    //         // return 404;
    //     };
    //     foreach (array_reverse($this->_middleware) as $name) {
    //         $next = function () use ($name, $next, $req) {
    //             $middle = new $name();
    //             return $middle->handle($next);
    //         }
    //     }
    //     return $next;
    // }

    // public function responseInsideMiddleware() {
    //     return 123;
    // }

    // public function __access() {
    //     return ['*' => '@user'];
    // }

    public function __before() {
        
    }

    public function __after() {
        
    }

    public function __error() {
        // $this->view->error404();
    }

    public function __access() {
        return [
            '*' => '@user', // cần không ?
            
            'post.create' => '@admin | admin.view | -admin.create',
            'post.demo' => [
                '*' => '@admin | admin.view | -admin.create',
                'GET' => 'post.view',
                'POST' => 'post.create',
                'PUT' => 'post.update',
                'DELETE' => 'post.delete'
            ],
            'post.delete' => 'post.delete',
            'post.update' => [
                'PUT' => 'post.update'
            ],
            'kien_lam_duoc' => function() {
                // $role = new \Role\Admin();
                // $permission = new \Permission\Post\Create();
                // ...
                // tổng hợp role, permission vào đây làm linh động hơn
                
                // chuyển sang thành phân hệ Access để khai báo list lên quản trị 

                return true;
            },
            // 'kien_lam_duoc' => '\\Permission\\Post\\Create',
            // ...
            // 'kien_lam_duoc' => '\\Access\\Post\\Create',
            // ...
            // 'kien_lam_duoc' => 'class:Access\\Post\\Create',
            // class đằng trước để nhận dạng
            // ...
            'kien_lam_duoc' => 'access:post.create',
            // access đằng trước để nhận dạng là dùng custom, xong có một file cấu hình map
            // phân hệ class access để dùng custom / dynamic role, permission, kết hợp role, permission tĩnh
        ];

        // có 1 file cấu hình khai báo các permission được sử dụng để cập nhật vào quản trị
    }

}

// ánh xạ action theo quyền (role / permission)
// quyền (role, permission) lại gán theo user

// bảng (map) định nghĩa quyền

// ...

$permission_map = [
    'post.create' => ['name' => 'Tạo bài viết'],
];

$access_map = [
    'kien_lam_duoc' => ['name' => 'Kien lam duoc', 'class' => '\\Access\\Post\\Create'], // có class thì là dynamic access
];

// hay

$dynamic_permission_map = [
    'kien_lam_duoc' => ['name' => 'Kien lam duoc', 'class' => '\\Permission\\Post\\Create'],
];

// phần dymaic khi cần phải load nên lưu riêng load từng phần cho tối ưu

$role_map = [
    'system' => ['name' => 'Hệ thống'],
    'admin' => ['name' => 'Quản trị']
];

// @user: Chưa gán permission gì thì là người dùng phải đăng nhập


// gắn permission vào role thì quản trị
// role admin hoặc system thì không cần gán, full luôn và được đĩnh nghĩa trước trong hệ thống core

// ---- truy vấn tối ưu
// scan permission sẽ được lưu vào DB
// nếu gọi một lần ra nhiều quá không tối ưu bộ nhớ (mà chắc tầm vài trăm cái vẫn bình thường) thì gọi theo prefix: post. (sẽ load create | update | delete | ...)

// ---
// phần __access trong controller chỉ cần permission được không? (hay vẫn cần group), bới vì permission cũng có thể thuộc, group được gắn vào user rồi check permission thuộc group
// à, gán group cho nó gọn

// ----

// JAVASCRIPT FRAMEWORK, id key không cùng cấp hoặc khác cha mẹ thì có thể trùng nhau
// key sẽ chung cho cả hệ children không phải chỉ trong mỗi frag