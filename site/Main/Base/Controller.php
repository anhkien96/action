<?php

namespace Base;

class Controller {

    protected $req, $view;

    public function __construct() {
        $this->req = \Reg::get('request');
        $this->view = \Reg::get('view');
    }

    protected function access($name, $option = []) {
        
    }

    protected function canView() {
        // có làm thêm trường hợp only view của ai đó tạo mới được xem
        return true;
    }

    protected function canCreate() {
        if ($this->req->method() == 'POST') {
            
        }
        return true;
    }

    protected function canUpdate() {
        return true;
    }

    protected function canDelete() {
        return true;
    }
    
    public function index() {   
    }
}

class Blog extends Controller {
    
    public function __construct() {
        parent::__construct();

        // $this->access('blog', ['role' => ['admin', 'editor']]);
        
        $this->access('blog');

        // blog ở đây là định danh cho phân hệ, không phải role, group, hay permission
        // role, group, permission riêng -> gán, quản trị cho định danh này trực quan trên giao diện quản trị
        // thêm file meta để mô tả blog là gì -> Chức năng bài viết
    }

    public function list() {
        if ($this->canView()) {
            
        }
    }

    public function detail($id) {
        if ($this->canView()) {
            // $id = $this->req->param('id');
            
            // if $record exists

            // else {
            //     return error 404
            // }
        }
    }

    public function create() {
        if ($this->canCreate()) {
            $data = $this->req->all();
            // allow fields
        }
    }

    public function update($id) {
        if ($this->canUpdate()) {
            // $id = $this->req->param('id');
        }
    }

    public function delete($id) {
        if ($this->canDelete()) {
            // $id = $this->req->param('id');
        }
        else {
            // return no permission

            // hay bắn tín hiệu bên response là ko thỏa mãn quyền (theo LOẠI)
            // -> xong tự xử lý response thông báo tương ứng
            // -> có thể custom bằng các hàm trả về error trong Controller
        }
    }
}


// role: admin 
//  + blog -> view | create | edit | delete
//              v      v       x       x
//  + page -> view | create | edit | delete
//              v      v       x       x