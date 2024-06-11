// tùy theo control, admin, gắn trước biến sẵn cho view
// + từ middleware
// + từ controller base


// css, js dùng postCss, gulp hoặc kiểu vậy
// phân quyền, validate, upload

// GET, POST, PUT, DELETE ... __post, __get, ...
// middleware
// auth, role, permission

// xu ly khi la api, web
// lang
// filter lang -> api, demo:  /lang/vi/api/
// bỏ web? cần không

// control chuyên mục
// underscore 2 capital, ...

// map permission theo GET, POST, PUT, DELETE
// chưa ổn lắm, chưa tách biệt trường hợp

// hay ăn theo model, đánh type

// cơ chế đánh ref giống kiểu Odoo

// bảng role, permission khi thay đổi chỉnh sửa thì render ra json để cache

// ---

// Route::get('/bai-viet/:slug', '/web/BaiViet/detail/$1');

// --> is MAP have type -> lúc xử lý không cần auto type GET, POST nữa


// ---
// Lớp Filter, lớp Validate gắn liền Model
// save, tiền xư lý như mã hóa password cần lớp không

// -----

// load control:
// $_ = array_map('ucfirst', preg_split('/_-/', $control));

// xy ly view render json khi co 'api'
// 

// command, migrate, seeding

// checking, log performance, sql query