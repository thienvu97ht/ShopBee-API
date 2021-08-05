<?php

// Import thư viện
include_once '../../configs/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';
include_once '../../helpers/cors.php';
include_once '../../db/dbhelper.php';
include_once '../../helpers/utility.php';

cors();

use Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;

// Xử lý backend
// B1. Xác thực email có tồn tại trong hệ thống
// B2. Kiểm tra email có tồn tại trong bảng token
// B3. Kiểm tra xem token trong hệ thống có tồn tại quá 5p => xóa token hết hạn.
// B4. Nếu thời gian tạo chưa quá 5p gửi email hoặc báo token hết hạn
// B5. Gửi 6 số ngẫu nhiên đến email người dùng đồng thời lưu vào database

// Check xem username có tồn tại trong hệ thống
$sql = "SELECT * FROM users WHERE email = '$email'";
$user = executeResult($sql, true);

// B1. Xác thực email có tồn tại trong hệ thống 
if ($user) {
    // B2. Kiểm tra email có tồn tại trong bảng token
    $email = $user['email'];
    $sql = "SELECT * FROM token WHERE email = '$email'";
    $result = executeResult($sql, true);

    // B3. Kiểm tra xem token trong hệ thống có tồn tại quá 5p => xóa token hết hạn.
    if ($result) {
        // Kiểm tra thời gian tồn tại của token
        $sql = "SELECT * FROM token WHERE email = '$email' 
        AND created_at > now() - interval 5 minute limit 0,1 ";
        $result = executeResult($sql);


        // B4. Nếu thời gian tạo chưa quá 5p gửi email hoặc báo token hết hạn
        if ($result) {
            // Token tồn tại chưa đến 5p => không gửi mail mới
            // Kiểm tra token người dùng gửi lên
            $res = [
                'status' => 0,
                'message' => "Vui lòng kiểm tra email"
            ];
            echo json_encode($res);
        } else {
            // B5. Gửi 6 số ngẫu nhiên đến email người
            // Xóa token cũ và lưu token mới vào database
            $sql = "DELETE FROM token WHERE email = '$email'";
            execute($sql);

            $token = rand(0, 999999);
            sendEmail($email, $token);

            // Thêm token vào database
            $sql = "INSERT INTO token (email, token) VALUES ('$email', '$token')";
            execute($sql);

            http_response_code(200);
            $res = [
                'status' => 1,
                'message' => "Send Email success"
            ];
            echo json_encode($res);
        }
    } else {
        // B5. Gửi 6 số ngẫu nhiên đến email người dùng đồng thời lưu vào database
        $token = rand(0, 999999);
        sendEmail($email, $token);

        // Thêm token vào database
        $sql = "INSERT INTO token (email, token) VALUES ('$email', '$token')";
        execute($sql);

        http_response_code(200);
        $res = [
            'status' => 1,
            'message' => "Send Email success"
        ];
        echo json_encode($res);
    }
} else {
    $res = [
        'status' => 0,
        'message' => "Email không tồn tại"
    ];
    echo json_encode($res);
}