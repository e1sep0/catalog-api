<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helpers\ResponseHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class BaseController.
 */
class BaseController extends AbstractController
{
    /**
     * @var ResponseHelper
     */
    protected $responseHelper;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * BaseController constructor.
     *
     * @param ResponseHelper $responseHelper
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ResponseHelper $responseHelper,
        SerializerInterface $serializer
    ) {
        $this->responseHelper = $responseHelper;
        $this->serializer = $serializer;
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function getFormErrors(FormInterface $form): array
    {
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];

            foreach ($form->getErrors() as $error) {
                $errors[$form->getName()][] = $error->getMessage();
            }

            foreach ($form as $child) {
                if ($child->isSubmitted() && !$child->isValid()) {
                    foreach ($child->getErrors() as $error) {
                        $errors[] = [
                            'field' => $child->getName(),
                            'message' => $error->getMessage(),
                        ];
                    }
                }
            }

            return $errors;
        }

        return [];
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \JsonException
     */
    public function getDataFromRequest(Request $request): array
    {
        if (!$request->getContent()) {
            return [];
        }

        return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param int|null $id
     * @param $className
     * @return \Symfony\Component\HttpFoundation\JsonResponse|null
     */
    public function getEntityByIdOrFailResponse(?int $id, $className)
    {
        if (!$id) {
            return $this->responseHelper->failResponse(
                'Не указан идентификатор'
            );
        }

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($className)->find($id);

        return $entity ?? $this->responseHelper->failResponse(
                'Объект не найден',
                ResponseHelper::STATUS_RESOURCE_NOT_FOUND
            );
    }
}
