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




class AuthController extends BaseController

{
    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function loginAction(Request $request)
    {
      $username = $request->get('username');
      $password = $request->get('password');
      
      // Kiểm tra thông tin đăng nhập
      if ($username === 'admin' && $password === '123456') {
          // Đăng nhập thành công
          $data = [
              'message' => 'dang nhap thanh cong',
            
          ];
      } else {
          // Đăng nhập thất bại
          $data = [
              'message' => 'dang nhap khong thanh cong'
          ];
      }
      // dd($data);
      return new JsonResponse($data);
      
    }

    /**
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function registerAction(Request $request)
    {
        // Lấy thông tin đăng ký từ request
        $username = $request->get('username');
        $email = $request->get('email');
        $password = $request->get('password');

        // Xử lý logic đăng ký ở đây (tạo tài khoản, lưu thông tin, vv.)

        // Trả về kết quả đăng ký
        $data = [
            'message' => 'Đăng ký thành công'
        ];

        return new JsonResponse($data);
    }
}
