<?php

namespace Api\Controller;

use App\Entity\ProductType;
use App\Form\ProductTypeType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\serializer;
use Symfony\Component\Serializer\Annotation\Groups;

#[Route('/api/product/type')]
final class ProductTypeController extends AbstractController
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

    #[Route('/new', name: 'api_account_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository, SerializerInterface $serializer): JsonResponse
    {
        $now = new \DateTime();

        //Objets de la requete
        // dd($request->getContent());
        // dd($request->toArray());


        $data = $request->toArray();
        $client = $clientRepository->find($data["client"]);


        /* Façon par Deserialisation */


        $newAccount = $serializer->deserialize($request->getContent(), Account::class, 'json', []);
        $newAccount
            ->setClient($client)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setStatus("on")
        ;



        /* Façon par Ajout manuel */

        // $newAccount = new Account();
        // $newAccount
        //     ->setName($data["name"])
        //     ->setUrl($data["url"])
        //     ->setPassword($data["password"])
        //     ->setCreatedAt($now)
        //     ->setUpdatedAt($now)
        //     ->setStatus("on")
        //     ->setClient($client)
        // ;


        $entityManager->persist($newAccount);
        $entityManager->flush();

        $jsonAccount = $serializer->serialize($newAccount, 'json', ['groups' => ['account']]);

        return new JsonResponse($jsonAccount, Response::HTTP_CREATED, [], true);
    }


    #[Route('/{id}/edit', name: 'api_account_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Account $account, EntityManagerInterface $entityManager, ClientRepository $clientRepository, SerializerInterface $serializer): Response
    {
        $now = new \DateTime();


        $data = $request->toArray();
        $client = $clientRepository->find($data["client"]);

        /* Façon par Deserialisation */
        $updatedAccount = $serializer->deserialize($request->getContent(), Account::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $account]);
        $updatedAccount
            ->setClient($client)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setStatus("on")
        ;


        /* Façon par Ajout manuel */

        // $account
        //     ->setName($data["name"])
        //     ->setUrl($data["url"])
        //     ->setPassword($data["password"])
        //     ->setUpdatedAt($now)
        //     ->setStatus("on")
        //     ->setClient($client)
        // ;

        $entityManager->persist($updatedAccount);
        $entityManager->flush();


        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'api_account_delete', methods: ['DELETE'])]
    public function delete(Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {


        $data = $request->toArray();
        if (isset($data['force']) && $data["force"] === true) {

            $entityManager->remove($account);
        } else {
            $account->setStatus('off');
            $entityManager->persist($account);
        }


        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
