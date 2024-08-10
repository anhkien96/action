<?php

namespace site\admin\Controller;
use site\admin\Base\Controller;

class Index extends Controller {

    // public function __construct() {
    //     parent::__construct();
    //     // $this->setMiddleware('auth');

    //     $this->access->config([
    //         // 'index' => [['role', 'admin'], ['permission', 'admin.view']],
    //         // 'index' => '(@admin123 && (admin.view || !admin.create))',
    //         // có @ thì đại diện cho group hoặc role -> !@admin
    //         // làm cái parse boolean, condition: ||, &&, !, ()

    //         // 'index' => '@admin123 | admin.view | -admin.create',
    //     ]);

    //     // \User::can('admin.index');
    // }

    public function __access() {
        return [
            '*' => '@user',
            'index' => '@admin | admin.view | -admin.create',
        ];
        // @admin thì sẽ là @user, tính kế thừa
        // @public -> @user -> @admin
    }

    public function index() {
        echo 'Admin Index<br/>';

        // return $this->view->json();

        // return 123;
        $this->view->layout();
        // return $this->view->display();
    }
}

// thêm role, permission cho action -> chọn có sẵn, hoặc tạo biểu thức tùy chọn
// permission khi gọi có thể là permission tĩnh hoặc dynamic permission

// -----

/*

PHP viết code parse thành mảng điều kiện, theo các phép toán logic, toán tử: "(", ")", "&&", "||", "!"

'(@admin123 && (admin.view || !admin.create))'

Kết quả mong muốn:
[
'op' => 'and',
'left' => '@admin123',
'right' => [
'op' => 'or',
'left' => 'admin.view',
'right' => [
'op' => 'not',
'left'=> 'admin.create',
'right' => null
]
]
]

____________


Để chuyển biểu thức điều kiện thành một cấu trúc dữ liệu như mong muốn, bạn có thể sử dụng một hàm đệ quy để phân tích biểu thức. Dưới đây là cách bạn có thể triển khai nó trong PHP:

php
Sao chép mã
<?php

function parseCondition($conditionString) {
    $conditionString = str_replace(' ', '', $conditionString);

    $tokens = tokenize($conditionString);
    return parseTokens($tokens);
}

function tokenize($conditionString) {
    $tokens = [];
    $currentToken = '';

    for ($i = 0; $i < strlen($conditionString); $i++) {
        $char = $conditionString[$i];

        if (in_array($char, ['(', ')', '!', '&', '|'])) {
            if (!empty($currentToken)) {
                $tokens[] = $currentToken;
                $currentToken = '';
            }
            $tokens[] = $char;
        } else {
            $currentToken .= $char;
        }
    }

    if (!empty($currentToken)) {
        $tokens[] = $currentToken;
    }

    return $tokens;
}

function parseTokens(&$tokens) {
    $token = array_shift($tokens);

    if ($token === '(') {
        $left = parseTokens($tokens);
        $op = array_shift($tokens); // '&&' or '||'
        $right = parseTokens($tokens);
        array_shift($tokens); // Pop ')'
        return ['op' => $op, 'left' => $left, 'right' => $right];
    } elseif ($token === '!') {
        $op = array_shift($tokens); // '!'
        $left = parseTokens($tokens);
        return ['op' => $op, 'left' => $left, 'right' => null];
    } else {
        return $token;
    }
}

$conditionString = '(@admin123 && (admin.view || !admin.create))';
$result = parseCondition($conditionString);
print_r($result);

?>
Kết quả sẽ là:

less
Sao chép mã
Array
(
    [op] => &&
    [left] => @admin123
    [right] => Array
        (
            [op] => ||
            [left] => admin.view
            [right] => Array
                (
                    [op] => !
                    [left] => admin.create
                    [right] => 
                )

        )

)
Mảng này tương ứng với cấu trúc dữ liệu mà bạn mong muốn.

*/