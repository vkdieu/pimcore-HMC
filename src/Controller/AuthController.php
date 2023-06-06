<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace App\Controller;




use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Customer;

// use App\Repository\CustomerRepository; //cung cấp phương thức để thực hiện thao tác truy vấn 

use Pimcore\Model\DataObject\Customer\Service as CustomerService;

class AuthController extends BaseController

{
    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function loginAction(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        // Tìm đối tượng Customer dựa trên tên người dùng
        $customer = Customer::getByFirstname($username);
        // Kiểm tra đối tượng Customer và so sánh mật khẩu
        if ($customer instanceof Customer && Customer::verifyPassword($password, $customer->getPassword())) {
            // Đăng nhập thành công

            $data = [
                'message' => 'success',
            ];
        } else {
            // Đăng nhập thất bại
            $data = [
                'message' => 'error',
            ];
        }

        return new JsonResponse($data);
    }


    /**
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function registerAction(Request $request)
    {
        $customer = new Customer();
        $random = rand() . 'User';

        $customer->setKey(\Pimcore\Model\Element\Service::getValidKey($random, 'object'));
        $customer->setParentId(5);

        // Lấy thông tin đăng ký từ yêu cầu
        $username = $request->get('username');
        $email = $request->get('email');
        $password = $request->get('password');

        // Tạo mã xác thực duy nhất
        $verificationCode = uniqid();

        // Lưu thông tin đăng ký và mã xác thực vào cơ sở dữ liệu hoặc nơi lưu trữ

        // Thiết lập các thuộc tính cho đối tượng khách hàng
        $customer->setFirstname($username);
        $customer->setEmail($email);
        $customer->setPassword($password);
        $customer->setActive(0);
        $customer->setverificationCode($verificationCode);

        $activationLink = 'http://mchien.localhost/code=' . $verificationCode;


        // Lưu đối tượng khách hàng
        $customer->save();

        $data = [
            'message' => 'success'
        ];

        return new JsonResponse($data);
    }



    /**
     * @Route("/api/verify-account/{id}", name="verify_account")
     */
    public function verifyAccountAction(Request $request, $id)
    {
        // Lấy đối tượng Tintuc dựa trên ID (giả sử Tintuc::getById là phương thức hợp lệ)
        $tintuc = Customer::getById($id);

        // Lấy mã xác thực từ yêu cầu
        $activationLink = $request->get('code');

        // Kiểm tra mã xác thực với giá trị của VerificationCode đã lưu trữ
        $verificationCode = $tintuc->getVerificationCode();
        // dd($verificationCode);

        if ($verificationCode == $activationLink) {
            // Mã xác thực hợp lệ, kích hoạt tài khoản bằng cách cập nhật trạng thái tài khoản

            // Đặt trạng thái kích hoạt của Tintuc thành true
            $tintuc->setActive(1);
            $tintuc->save();

            // Trả về phản hồi thông báo cho người dùng
            $data = [
                'message' => 'success',
            ];
        } else {
            // Mã xác thực không hợp lệ
            $data = [
                'message' => 'error',
            ];
        }

        return new JsonResponse($data);
    }


    /**
     * @Route("/api/forget-password", name="api_forget_password", methods={"POST"})
     */
    public function forgetPasswordAction(Request $request)
    {
        // Lấy địa chỉ email từ yêu cầu
        $email = $request->get('email');

        // Kiểm tra xem địa chỉ email có tồn tại trong hệ thống hay không
        $customer = Customer::getByEmail($email);
        // dd( $customer);
        if (!$customer) {
            return new JsonResponse(['message' => 'Email does not exist']);
        }

        // Lấy ID của người dùng
        // $customerId = $customer->getId();

        // Lấy mã xác thực từ đối tượng Customer
        $verificationCode = $customer->getVerificationCode();

        // Lưu $verificationData vào cơ sở dữ liệu hoặc nơi lưu trữ tương ứng

        // Gửi email chứa đường dẫn đặt lại mật khẩu đến địa chỉ email người dùng
        $resetLink = 'http://mchien/reset-password?code=' . $verificationCode;
        $emailContent = "Xin chào, Vui lòng nhấp vào liên kết sau để đặt lại mật khẩu của bạn: {$resetLink}";

        // Gửi email đến địa chỉ email của người dùng
        // ...

        // Trả về phản hồi thành công
        $data = [
            'message' => 'success',
            'reset_link' => $resetLink,
        ];

        return new JsonResponse($data);
    }



    /**
     * @Route("/api/resetpassword", name="api_reset_password", methods={"POST"})
     */
    public function resetPasswordAction(Request $request)
    {
        // Lấy mã xác thực và mật khẩu mới từ yêu cầu
        $verificationCode = $request->get('code');
        $newPassword = $request->get('new_password');
    
        // Kiểm tra mã xác thực với dữ liệu đã lưu trữ và đảm bảo rằng nó hợp lệ
        $customer = Customer::getByverificationCode($verificationCode);
        if (!$customer) {
            return new JsonResponse(['message' => 'Email does not exist']);
        }
    
        // Đặt lại mật khẩu mới cho khách hàng
        $customer->setNewPassword($newPassword); // Thay thế bằng phương thức chính xác để đặt lại mật khẩu
    
        // Lưu khách hàng vào cơ sở dữ liệu
        $customer->save();
    
        // Trả về phản hồi thành công
        $data = [
            'message' => 'success',
        ];
    
        return new JsonResponse($data);
    }
}
