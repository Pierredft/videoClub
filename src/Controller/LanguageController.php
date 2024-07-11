<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Language;
use App\Repository\ProductRepository;
use App\Repository\LanguageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguageController extends AbstractController
{
    #[Route('/api/language', name: 'app_language', methods: ['GET'])]
    public function getLanguage(LanguageRepository $languageRepository, SerializerInterface $serializer): JsonResponse
    {
        $language = $languageRepository->findAll();
        $jsonLanguage = $serializer->serialize($language, 'json');
        return new JsonResponse($jsonLanguage, Response::HTTP_OK,[], true);
    }

    #[Route('/api/language/{id}', name: 'detailLanguage', methods: ['GET'])]
    public function getDetailLanguage(SerializerInterface $serializer, Language $language): JsonResponse
    {
            $jsonLanguage = $serializer->serialize($language,'json');
            return new JsonResponse($jsonLanguage, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/language', name:'createLanguage', methods: ['POST'])]
        public function createLanguage(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
        {
            $language = $serializer->deserialize($request->getContent(), Language::class,'json');
            $content = $request->toArray();

            $em->persist($language);
            $em->flush();

            $jsonLanguage = $serializer->serialize($language,'json');

            $location = $urlGenerator->generate('detailLanguage', ['id' => $language->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse($jsonLanguage, Response::HTTP_CREATED, ["Location" => $location], true);
        }

    #[Route('/api/language/{id}', name: 'updateLanguage', methods: ['PUT'])]
        public function updateLanguage(Request $request, SerializerInterface $serializer, Language $currentLanguage, EntityManagerInterface $em): JsonResponse
        {
            $updateLanguage = $serializer->deserialize($request->getContent(), Language::class,'json',[AbstractNormalizer::OBJECT_TO_POPULATE => $currentLanguage]);

            $content = $request->toArray();
            
            $em->persist($updateLanguage);
            $em->flush();

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }

    #[Route('/api/language/{id}', name: 'deleteLanguage', methods: ['DELETE'])]
    public function deleteLAnguage(Language $language, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($language);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
