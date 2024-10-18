<?php

namespace App\Controller;

use App\Entity\ProductType;
use App\Form\ProductTypeType;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product/type')]
final class ProductTypeController extends AbstractController
{
    #[Route(name: 'app_product_type_index', methods: ['GET'])]
    public function index(ProductTypeRepository $productTypeRepository): Response
    {
        return $this->render('product_type/index.html.twig', [
            'product_types' => $productTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $productType = new ProductType();
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($productType);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_type/new.html.twig', [
            'product_type' => $productType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_type_show', methods: ['GET'])]
    public function show(ProductType $productType): Response
    {
        return $this->render('product_type/show.html.twig', [
            'product_type' => $productType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductType $productType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_type/edit.html.twig', [
            'product_type' => $productType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_type_delete', methods: ['POST'])]
    public function delete(Request $request, ProductType $productType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($productType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
