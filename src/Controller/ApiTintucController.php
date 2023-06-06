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



use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Tintuc\Listing;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Model\DataObject\Tintuc;
use Pimcore\Model\DataObject;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse; // để sửa dung JsonResponse()




class ApiTintucController extends BaseController
{
  /**
   * @Route("/tintuc/create ", name="Create", methods={"POST"})
   *
   * @param Request $request
   * @return Response|RedirectResponse
   */
  public function Create(Request $request)
  {
    $requestData = $request->request->all();
    // dd($requestData);


    $newObject = new DataObject\Tintuc();
    $ramdom = rand() . 'Tintuc';


    $newObject->setKey(\Pimcore\Model\Element\Service::getValidKey($ramdom, 'object'));
    $newObject->setParentId(2);
    $newObject->setTitle($requestData['title']);
    $newObject->setShortContent($requestData['shortContent']);
    $newObject->setContent($requestData['content']);



    $newObject->save(["versionNote" => "my new version"]);

    // Do something with the saved object

    $response = new Response('Object created successfully',);
    return $response;
  }




  /**
   * @Route("/tintuc/showall", name="showAll", methods={"get"} )
     
   * @return Response|RedirectResponse
   */
  public function showAll()
  {
    // Truy vấn tất cả các đối tượng trong lớp Tintuc
    $objects = new Tintuc\Listing();

    // Lặp qua từng đối tượng và lấy thông tin cần hiển thị
    $data = [];
    foreach ($objects as $object) {
      $id = $object->getId(); //get lấy dữ liệu 
      $title = $object->getTitle();
      $shortContent = $object->getShortContent();
      $content = $object->getContent();


      // Lưu thông tin vào mảng dữ liệu
      $data[] = [
        'id' => $id,
        'title' => $title,
        'shortContent'=>$shortContent,
        'content'=>$content,
      ];
    }
    // dd($data);
    return new JsonResponse($data);
  }

  /**
   * @Route("/tintuc/show/{id}", name="show", methods={"get"} )
     
   * @return Response|RedirectResponse
   */
  public function show($id)
  {
    // Truy vấn đối tượng trong lớp Tintuc dựa trên id
    $object = DataObject\Tintuc::getById($id);

    if ($object) {
      $data = [
        'id' => $object->getId(),
        'title' => $object->getTitle(),
        'shortContent' => $object->getShortContent(),
        'content' => $object->getContent(),
    


      ];

      // Tạo một đối tượng JsonResponse chứa dữ liệu và trả về
      return new JsonResponse($data);
    } else {
      // Nếu không tìm thấy đối tượng, trả về một JSON response rỗng hoặc thông báo lỗi
      return new JsonResponse("Object khong co");
    }
  }



  /**
   * @Route("/tintuc/update/{id}", name="update", methods={"post"})
   */
  public function update(Request $request, $id)
  {
    $requestData = $request->request->all();
    // dd($requestData);
    $object = DataObject\Tintuc::getById($id);


    // Kiểm tra nếu không tìm thấy đối tượng
    if (!$object) {
      return new Response("Object not found", Response::HTTP_NOT_FOUND);
    }

    // Cập nhật thông tin của đối tượng
    $object->setTitle($requestData['title']);
    $object->setShortContent($requestData['shortContent']);
    $object->setContent($requestData['content']);

    // Lưu các thay đổi
    $object->save();

    return new Response("Object updated successfully", Response::HTTP_OK);
  }





  /**
  
     
   * @Route("/tintuc/delete/{id}", name="delete", methods={"DELETE"})
   */
  public function delete($id)
  {
    // Lấy đối tượng cần xóa dựa trên ID
    $object = DataObject\Tintuc::getById($id);

    // Kiểm tra nếu không tìm thấy đối tượng
    if (!$object) {
      return new Response("Object not found", Response::HTTP_NOT_FOUND);
    }

    // Xóa đối tượng
    $object->delete();

    return new Response("Object deleted successfully", Response::HTTP_OK);
  }
}