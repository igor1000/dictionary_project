<?php

namespace App\Controller;

use App\Dto\ItemDto;
use App\Service\DictionaryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Manager API",
 *     description="HTTP JSON API",
 * )
 * @OA\Server(
 *     url="/"
 * ),
 */
class DictionaryController extends AbstractController
{
    /**
     * @var DictionaryService
     */
    private $dictionaryService;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(DictionaryService $dictionaryService, SerializerInterface $serializer)
    {
        $this->dictionaryService = $dictionaryService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/load", name="load", methods={"POST"})
     *
     * @OA\Get(
     *     path="/load",
     *     description="Data load",
     *     @OA\Response(
     *          response="200",
     *          description="Data loaded"
     *     )
     * )
     */
    public function load(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('dictionary');

        $this->dictionaryService->fill($file->getContent());

        return new Response('Данные загружены');
    }

    /**
     * @Route("/add", methods={"POST"})
     *
     * @OA\Get(
     *     path="/add",
     *     description="Add Item",
     *     @OA\Response(
     *          response="200",
     *          description="Item added"
     *     )
     * )
     */
    public function add(Request $request): Response
    {
        $data = $this->serializer->deserialize($request->getContent(), ItemDto::class, 'json');

        $this->dictionaryService->add($data);

        return new Response('Данные добавлены');
    }

    /**
     * @Route("/edit", methods={"PUT"})
     *
     * @OA\Get(
     *     path="/edit",
     *     description="Edit Item",
     *     @OA\Response(
     *          response="200",
     *          description="Edit added"
     *     )
     * )
     */
    public function edit(Request $request): Response
    {
        $data = $this->serializer->deserialize($request->getContent(), ItemDto::class, 'json');

        $this->dictionaryService->edit($data);

        return new Response('Данные отредактированы');
    }

    /**
     * @Route("/delete", methods={"DELETE"})
     *
     * @OA\Get(
     *     path="/delete",
     *     description="Delete Item",
     *     @OA\Response(
     *          response="200",
     *          description="Item deleted"
     *     )
     * )
     */
    public function delete(Request $request): Response
    {
        $data = $this->serializer->deserialize($request->getContent(), ItemDto::class, 'json');

        $this->dictionaryService->delete($data->code);

        return new Response('Данные удалены');
    }

    /**
     * @Route("/show-all", methods={"GET"})
     *
     * @OA\Get(
     *     path="/show-all",
     *     description="Show all items",
     *     @OA\Response(
     *          response="200",
     *          description="All dictionary items in tree"
     *     )
     * )
     */
    public function showAll()
    {
        $items = $this->dictionaryService->findAll();

        return new JsonResponse($items);
    }

    /**
     * @Route("/find-by-code/{code}", methods={"GET"})
     *
     * @OA\Get(
     *     path="/find-by-code/{code}",
     *     description="List items",
     *     @OA\Response(
     *          response="200",
     *          description="List items",
     *          @OA\JsonContent(
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="name", type="string")
     *              )
     *          )
     *     )
     * )
     */
    public function fineByCode(string $code)
    {
        $items = $this->dictionaryService->findByCodePart($code);

        return new JsonResponse($items);
    }

    /**
     * @Route("/find-by-name/{name}", methods={"GET"})
     *
     * @OA\Get(
     *     path="/find-by-name/{name}",
     *     description="List items",
     *     @OA\Response(
     *          response="200",
     *          description="List items",
     *          @OA\JsonContent(
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="name", type="string")
     *              )
     *          )
     *     )
     * )
     */
    public function fineByName(string $name)
    {
        $items = $this->dictionaryService->findByNamePart($name);

        return new JsonResponse($items);
    }
}