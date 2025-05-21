<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/category')]
#[IsGranted('ROLE_ADMIN')]
class AdminCategoryController extends AbstractController
{
    #[Route('/', name: 'app_admin_category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_category_new')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            $this->addFlash('success', 'Category created successfully.');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_category_edit')]
    public function edit(
        Request $request,
        Category $category,
        CategoryRepository $categoryRepository
    ): Response {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            $this->addFlash('success', 'Category updated successfully.');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_category_delete')]
    public function delete(
        Request $request,
        Category $category,
        CategoryRepository $categoryRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);

            $this->addFlash('success', 'Category deleted successfully.');
        }

        return $this->redirectToRoute('app_admin_category_index');
    }
}
