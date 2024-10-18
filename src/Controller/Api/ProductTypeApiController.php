<?php

namespace App\Controller\Api;

use App\Entity\ProductType;
use App\Form\ProductTypeType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\serializer;

#[Route('/api/product/type')]
final class ProductTypeApiController extends AbstractController
{
    #[Route(name: 'api_product_type_index', methods: ['GET'])]
    public function index(ProductTypeRepository $productTypeRepository,SerializerInterface $serializer): Response
    {  
        $listProductType = $productTypeRepository->findAll();
        $jsonProductType = $serializer->serialize($listProductType, 'json', ['groups' => 'productType:list']);
        return new JsonResponse($jsonProductType, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'api_product_type_show', methods: ['GET'])]
    public function show(ProductType $productType, SerializerInterface $serializer): Response
    {
        $jsonProductType = $serializer->serialize($productType, 'json', ['groups' => 'productType:detail']);
        return new JsonResponse($jsonProductType, Response::HTTP_OK, [], true);
    }

    #[Route('/new', name: 'api_product_type_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductTypeRepository $productTypeRepository, SerializerInterface $serializer): JsonResponse
    {
        $now = new \DateTime();

        //Objets de la requete
        // dd($request->getContent());
        // dd($request->toArray());


        $data = $request->toArray();
        $name = $productTypeRepository->find($data["name"]);
        $price = $productTypeRepository->find($data["price"]);

        /* Façon par Deserialisation */


        $newProductType = $serializer->deserialize($request->getContent(), ProductType::class, 'json', []);
        $newProductType
            ->setName($name)
            ->setPrice($price)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setStatus("on")
        ;



        /* Façon par Ajout manuel */

        // $newProductType = new ProductType();
        // $newProductType
        //     ->setName($data["name"])
        //     ->setUrl($data["url"])
        //     ->setPassword($data["password"])
        //     ->setCreatedAt($now)
        //     ->setUpdatedAt($now)
        //     ->setStatus("on")
        //     ->setClient($client)
        // ;


        $entityManager->persist($newProductType);
        $entityManager->flush();

        $jsonProductType = $serializer->serialize($newProductType, 'json', ['groups' => ['ProductType']]);

        return new JsonResponse($jsonProductType, Response::HTTP_CREATED, [], true);
    }


    #[Route('/{id}/edit', name: 'api_product_type_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, ProductType $ProductType, EntityManagerInterface $entityManager, ProductTypeRepository $productTypeRepository, SerializerInterface $serializer): Response
    {
        $now = new \DateTime();


        $data = $request->toArray();
        $name = $productTypeRepository->find($data["name"]);
        $price = $productTypeRepository->find($data["price"]);

        /* Façon par Deserialisation */
        $updatedProductType = $serializer->deserialize($request->getContent(), ProductType::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $ProductType]);
        $updatedProductType
        ->setName($name)
        ->setPrice($price)
        ->setCreatedAt($now)
        ->setUpdatedAt($now)
        ->setStatus("on")
        ;


        /* Façon par Ajout manuel */

        // $ProductType
        //     ->setName($data["name"])
        //     ->setUrl($data["url"])
        //     ->setPassword($data["password"])
        //     ->setUpdatedAt($now)
        //     ->setStatus("on")
        //     ->setClient($client)
        // ;

        $entityManager->persist($updatedProductType);
        $entityManager->flush();


        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'api_product_type_delete', methods: ['DELETE'])]
    public function delete(Request $request, ProductType $ProductType, EntityManagerInterface $entityManager): Response
    {


        $data = $request->toArray();
        if (isset($data['force']) && $data["force"] === true) {

            $entityManager->remove($ProductType);
        } else {
            $ProductType->setStatus('off');
            $entityManager->persist($ProductType);
        }


        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
