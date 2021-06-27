<?php

namespace App\Controller\ApiV2;

use App\Controller\BaseController;
use App\Entity\Category;
use App\Entity\Good;
use App\Form\CategoryType;
use App\Helpers\ResponseHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v2/categories", name="category_")
 */
class CategoryController extends BaseController
{

    /**
     * @Route("", name="list", methods={"GET"})
     */
    public function listAction(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->responseHelper->successResponse($categories, ResponseHelper::STATUS_OK, ['category']);
    }

    /**
     * @Route("/{categoryId}/goods", name="goods_list", methods={"GET"})
     *
     * В параметрах можно было сразу использовать сущность Category, но при вводе несуществующей категории
     * выйдет дефолтная ошибка PAramConverter
     */
    public function goodsListAction($categoryId): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $category = $this->getEntityByIdOrFailResponse((int)$categoryId, Category::class);

        if (!$category instanceof Category) {
            return $category;
        }

        $goods = $em->getRepository(Good::class)->getGoodsByCategory($category->getId());

        return $this->responseHelper->successResponse($goods, ResponseHelper::STATUS_OK, ['good']);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     * @IsGranted("ROLE_API")
     *
     */
    public function createAction(Request $request): JsonResponse
    {
        $data = $this->getDataFromRequest($request);
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->submit($data);

        $errors = $this->getFormErrors($form);

        if (count($errors) !== 0) {
            return $this->responseHelper->failResponse($errors, ResponseHelper::STATUS_VALIDATION_ERROR);
        }

        $em = $this->getDoctrine()->getManager();

        try {
            $em->persist($form->getData());
            $em->flush();
        } catch (\Throwable $e) {
            return $this->responseHelper->failResponse(
                'Ошибка сервиса',
                ResponseHelper::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        return $this->responseHelper->successResponse(
            $category,
            ResponseHelper::STATUS_CREATED,
            ['category']
        );
    }


    /**
     * @Route("/{categoryId}", name="edit", methods={"POST"})
     * @IsGranted("ROLE_API")
     *
     */
    public function editAction(Request $request, $categoryId): JsonResponse
    {
        $category = $this->getEntityByIdOrFailResponse((int)$categoryId, Category::class);

        if (!$category instanceof Category) {
            return $category;
        }

        $data = $this->getDataFromRequest($request);

        $form = $this->createForm(CategoryType::class, $category);
        $form->submit($data);

        $errors = $this->getFormErrors($form);

        if (count($errors) !== 0) {
            return $this->responseHelper->failResponse($errors, ResponseHelper::STATUS_VALIDATION_ERROR);
        }

        $em = $this->getDoctrine()->getManager();

        try {
            $em->flush();
        } catch (\Throwable $e) {
            return $this->responseHelper->failResponse(
                'Ошибка сервиса',
                ResponseHelper::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        return $this->responseHelper->successResponse(
            $category,
            ResponseHelper::STATUS_CREATED,
            ['category']
        );
    }

    /**
     * @Route("/{categoryId}", name="delete", methods={"DELETE"})
     * @IsGranted("ROLE_API")
     *
     */
    public function deleteAction($categoryId): JsonResponse
    {
        $category = $this->getEntityByIdOrFailResponse((int)$categoryId, Category::class);

        if (!$category instanceof Category) {
            return $category;
        }

        $em = $this->getDoctrine()->getManager();

        try {
            $em->remove($category);
            $em->flush();
        } catch (\Throwable $e) {
            return $this->responseHelper->failResponse(
                'Ошибка сервиса',
                ResponseHelper::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        return $this->responseHelper->successResponse();
    }

}
