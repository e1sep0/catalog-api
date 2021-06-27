<?php

declare(strict_types=1);

namespace App\Helpers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ResponseHelper.
 */
final class ResponseHelper
{
    /**
     * Успешный ответ.
     */
    public const STATUS_OK = Response::HTTP_OK;

    /**
     * Успешное создание ресурса
     */
    public const STATUS_CREATED = Response::HTTP_CREATED;

    /**
     * Ошибка на стороне клиента.
     */
    public const STATUS_CLIENT_ERROR = Response::HTTP_BAD_REQUEST;

    /**
     * Ошибка доступа, когда недостаточно прав и т.п.
     */
    public const STATUS_ACCESS_DENIED = Response::HTTP_FORBIDDEN;

    /**
     * Требуется аутентификация.
     */
    public const STATUS_UNAUTHORIZED = Response::HTTP_UNAUTHORIZED;

    /**
     * Ресурс не найден.
     *
     */
    public const STATUS_RESOURCE_NOT_FOUND = Response::HTTP_NOT_FOUND;

    /**
     * Ошибка валидации.
     */
    public const STATUS_VALIDATION_ERROR = Response::HTTP_UNPROCESSABLE_ENTITY;

    /**
     * Внутренняя ошибка сервера.
     */
    public const STATUS_INTERNAL_SERVER_ERROR = Response::HTTP_INTERNAL_SERVER_ERROR;


    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * ResponseHelper constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array $data
     * @param int|null $status
     * @param array $groups
     *
     * @return JsonResponse
     *
     * @throws \InvalidArgumentException
     */
    public function successResponse(
        $data = [],
        ?int $status = ResponseHelper::STATUS_OK,
        array $groups = ['all']
    ): JsonResponse {
        if (null === $status) {
            $status = self::STATUS_OK;
        }

        $json = $this->serializer->serialize($data, 'json', ['groups' => $groups]);
        $response = JsonResponse::fromJsonString($json);
        $response->setStatusCode($status);

        return $response;
    }

    /**
     * @param array|null|string $errors
     * @param int $status
     * @param array $groups
     *
     * @return JsonResponse
     *
     * @throws \InvalidArgumentException
     */
    public function failResponse(
        $errors = [],
        int $status = ResponseHelper::STATUS_CLIENT_ERROR,
        array $groups = ['all']
    ): JsonResponse {

        if (is_string($errors)) {
            $errors = [
                ['message' => $errors],
            ];
        }

        foreach ($errors as $error) {
            if (is_string($error)) {
                $error = ['message' => $error];
            }
        }

        $json = $this->serializer->serialize($errors, 'json', ['groups' => $groups]);
        $response = JsonResponse::fromJsonString($json);
        $response->setStatusCode($status);

        return $response;
    }
}
