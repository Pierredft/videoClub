<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    #[Route('/api/product', name: 'app_product', methods: ['GET'])]
    public function getProduct(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $product = $productRepository->findAll();
        $jsonProduct = $serializer->serialize($product, 'json');
        return new JsonResponse($jsonProduct, Response::HTTP_OK,[], true);
    }

    #[Route('/api/product/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct(SerializerInterface $serializer, Product $product): JsonResponse
    {
            $jsonProduct = $serializer->serialize($product,'json');
            return new JsonResponse($jsonProduct, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/product', name:'createProduct', methods: ['POST'])]
        public function createProduct(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
        {
            $product = $serializer->deserialize($request->getContent(), Product::class,'json');
            $content = $request->toArray();

            $em->persist($product);
            $em->flush();

            $jsonProduct = $serializer->serialize($product,'json');

            $location = $urlGenerator->generate('detailProduct', ['id' => $product->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ["Location" => $location], true);
        }

    #[Route('/api/product/{id}', name: 'updateProduct', methods: ['PUT'])]
        public function updateProduct(Request $request, SerializerInterface $serializer, Product $currentProduct, EntityManagerInterface $em): JsonResponse
        {
            $updateProduct = $serializer->deserialize($request->getContent(), Product::class,'json',[AbstractNormalizer::OBJECT_TO_POPULATE => $currentProduct]);

            $content = $request->toArray();
            
            $em->persist($updateProduct);
            $em->flush();

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }

    #[Route('/api/product/{id}', name: 'deleteProduct', methods: ['DELETE'])]
    public function deleteProduct(Product $product, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($product);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
