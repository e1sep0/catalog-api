<?php

namespace App\Controller\ApiV2;

use App\Controller\BaseController;
use App\Entity\Good;
use App\Form\GoodType;
use App\Helpers\ResponseHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v2/goods", name="goods_")
 */
class GoodController extends BaseController
{

    /**
     * @Route("", name="create", methods={"POST"})
     * @IsGranted("ROLE_API")
     *
     */
    public function createAction(Request $request): JsonResponse
    {
        $data = $this->getDataFromRequest($request);
        $good = new Good();

        $form = $this->createForm(GoodType::class, $good);
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
            $good,
            ResponseHelper::STATUS_CREATED,
            ['new_good']
        );
    }


    /**
     * @Route("/{goodId}", name="edit", methods={"POST"})
     * @IsGranted("ROLE_API")
     *
     */
    public function editAction(Request $request, $goodId): JsonResponse
    {
        $good = $this->getEntityByIdOrFailResponse((int)$goodId, Good::class);

        if (!$good instanceof Good) {
            return $good;
        }

        $data = $this->getDataFromRequest($request);

        $form = $this->createForm(GoodType::class, $good);
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
            $good,
            ResponseHelper::STATUS_CREATED,
            ['new_good']
        );
    }

    /**
     * @Route("/{goodId}", name="delete", methods={"DELETE"})
     * @IsGranted("ROLE_API")
     *
     */
    public function deleteAction($goodId): JsonResponse
    {
        $good = $this->getEntityByIdOrFailResponse((int)$goodId, Good::class);

        if (!$good instanceof Good) {
            return $good;
        }

        $em = $this->getDoctrine()->getManager();

        try {
            $em->remove($good);
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
