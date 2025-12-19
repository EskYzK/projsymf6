<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Data\ProductFlowDTO;
use App\Form\Product\ProductFlowType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Flow\FormFlowInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAllOrderedByPrice(),
        ]);
    }

    #[Route('/new', name: 'app_product_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dto = new ProductFlowDTO();

        /** @var FormFlowInterface $flow */
        $flow = $this->createForm(ProductFlowType::class, $dto)
            ->handleRequest($request);

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            $product = new Product();
            $this->mapDtoToEntity($dto, $product);

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès !');
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/flow.html.twig', [
            'form' => $flow->getStepForm(),
            'flow' => $flow,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $dto = new ProductFlowDTO();
        $dto->name = $product->getName();
        $dto->description = $product->getDescription();
        $dto->price = $product->getPrice();
        $dto->type = $product->getType();
        $dto->stock = $product->getStock();
        $dto->license = $product->getLicense();

        /** @var FormFlowInterface $flow */
        $flow = $this->createForm(ProductFlowType::class, $dto)
            ->handleRequest($request);

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            $this->mapDtoToEntity($dto, $product);
            
            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/flow.html.twig', [
            'form' => $flow->getStepForm(),
            'flow' => $flow,
            'product' => $product
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }

        return $this->redirectToRoute('app_product_index');
    }

    private function mapDtoToEntity(ProductFlowDTO $dto, Product $product): void
    {
        $product->setName($dto->name);
        $product->setDescription($dto->description);
        $product->setPrice($dto->price);
        $product->setType($dto->type);

        if ($dto->type === 'physique') {
            $product->setStock($dto->stock);
            $product->setLicense(null);
        } elseif ($dto->type === 'numerique') {
            $product->setLicense($dto->license);
            $product->setStock(null);
        }
    }
}