<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CsvExporter;
use App\Form\Product\ProductStep1Type;
use App\Form\Product\ProductStep2Type;
use App\Form\Product\ProductStep3Type;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $sort = $request->query->get('sort', 'name_asc');

        $sortMapping = [
            'name_asc'   => ['name' => 'ASC'],
            'name_desc'  => ['name' => 'DESC'],
            'price_asc'  => ['price' => 'ASC'],
            'price_desc' => ['price' => 'DESC'],
        ];

        $orderBy = $sortMapping[$sort] ?? $sortMapping['name_asc'];
        $products = $productRepository->findBy([], $orderBy);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'currentSort' => $sort,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[IsGranted('PRODUCT_ADD')]
    public function create(Request $request, SessionInterface $session, EntityManagerInterface $em): Response
    {
        $step = $request->query->getInt('step', 1);
        
        $data = $session->get('product_creation_data', []);

        if ($step === 1) {
            $form = $this->createForm(ProductStep1Type::class, $data);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $session->set('product_creation_data', array_merge($data, $form->getData()));
                return $this->redirectToRoute('app_product_new', ['step' => 2]);
            }
            
            return $this->render('product/new_step.html.twig', [
                'form' => $form->createView(),
                'step' => 1,
                'total_steps' => 3
            ]);
        }

        if ($step === 2) {
            $form = $this->createForm(ProductStep2Type::class, $data);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $session->set('product_creation_data', array_merge($data, $form->getData()));
                return $this->redirectToRoute('app_product_new', ['step' => 3]);
            }

            return $this->render('product/new_step.html.twig', [
                'form' => $form->createView(),
                'step' => 2,
                'total_steps' => 3
            ]);
        }

        if ($step === 3) {
            $isPhysical = ($data['type'] ?? 'physique') === 'physique';

            $form = $this->createForm(ProductStep3Type::class, $data, [
                'is_physical' => $isPhysical
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $finalData = array_merge($data, $form->getData());
                
                $product = new Product();
                $product->setName($finalData['name']);
                $product->setDescription($finalData['description']);
                $product->setPrice($finalData['price']);
                $product->setType($finalData['type']);
                
                if ($isPhysical) {
                    $product->setStock($finalData['stock']);
                } else {
                    $product->setLicenseKey($finalData['licenseKey']);
                }

                $em->persist($product);
                $em->flush();

                $session->remove('product_creation_data');
                $this->addFlash('success', 'Produit créé avec succès !');

                return $this->redirectToRoute('app_product_index');
            }

            return $this->render('product/new_step.html.twig', [
                'form' => $form->createView(),
                'step' => 3,
                'total_steps' => 3
            ]);
        }

        return $this->redirectToRoute('app_product_new', ['step' => 1]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[IsGranted('PRODUCT_EDIT', subject: 'product')]
    public function edit(Request $request, Product $product, SessionInterface $session, EntityManagerInterface $em): Response
    {
        $step = $request->query->getInt('step', 1);
        
        $sessionKey = 'product_edit_' . $product->getId();
        
        $data = $session->get($sessionKey, []);
        
        if (empty($data)) {
            $data = [
                'type' => $product->getType(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'licenseKey' => $product->getLicenseKey(),
            ];
            $session->set($sessionKey, $data);
        }

        if ($step === 1) {
            $form = $this->createForm(ProductStep1Type::class, $data);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $session->set($sessionKey, array_merge($data, $form->getData()));
                return $this->redirectToRoute('app_product_edit', ['id' => $product->getId(), 'step' => 2]);
            }
            
            return $this->render('product/edit_step.html.twig', [
                'form' => $form->createView(), 'step' => 1, 'total_steps' => 3, 'product' => $product
            ]);
        }

        if ($step === 2) {
            $form = $this->createForm(ProductStep2Type::class, $data);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $session->set($sessionKey, array_merge($data, $form->getData()));
                return $this->redirectToRoute('app_product_edit', ['id' => $product->getId(), 'step' => 3]);
            }

            return $this->render('product/edit_step.html.twig', [
                'form' => $form->createView(), 'step' => 2, 'total_steps' => 3, 'product' => $product
            ]);
        }

        if ($step === 3) {
            $isPhysical = ($data['type'] ?? 'physique') === 'physique';
            $form = $this->createForm(ProductStep3Type::class, $data, ['is_physical' => $isPhysical]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $finalData = array_merge($data, $form->getData());

                $product->setType($finalData['type']);
                $product->setName($finalData['name']);
                $product->setDescription($finalData['description']);
                $product->setPrice($finalData['price']);

                if ($finalData['type'] === 'physique') {
                    $product->setStock($finalData['stock']);
                    $product->setLicenseKey(null);
                } else {
                    $product->setStock(null);
                    $product->setLicenseKey($finalData['licenseKey']);
                }

                $em->flush();
                
                $session->remove($sessionKey);
                $this->addFlash('success', 'Produit modifié avec succès !');

                return $this->redirectToRoute('app_product_index');
            }

            return $this->render('product/edit_step.html.twig', [
                'form' => $form->createView(), 'step' => 3, 'total_steps' => 3, 'product' => $product
            ]);
        }

        return $this->redirectToRoute('app_product_edit', ['id' => $product->getId(), 'step' => 1]);
    }

    private function getStepNumber(string $stepName): int
    {
        return match ($stepName) {
            'category' => 1,
            'details' => 2,
            'logistics', 'license' => 3,
            default => 1,
        };
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('PRODUCT_DELETE', subject: 'product')]
    public function delete(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
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
            $product->setLicenseKey(null);
        } elseif ($dto->type === 'numerique') {
            $product->setLicenseKey($dto->license);
            $product->setStock(null);
        }
    }

    #[Route('/export/csv', name: 'app_product_export')]
    public function export(CsvExporter $csvExporter): Response
    {
        return $csvExporter->exportProducts();
    }
}