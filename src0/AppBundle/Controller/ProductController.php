<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Videos;
use AppBundle\Form\ProductType;
use AppBundle\Form\VideosType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Product controller.
 *
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     *
     */
    public function indexGalleryAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('AppBundle:ProductImage')->findByProduct($id);

        return $this->render('product/indexGallery.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * Lists all product entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('AppBundle:Product')->findAll();

        return $this->render('product/index.html.twig', array(
            'products' => $products,
        ));
    }
    public function indexvideoAction()
    {
        $em = $this->getDoctrine()->getManager();

        $videos = $em->getRepository('AppBundle:Videos')->findAll();

        return $this->render('product/indexvideos.html.twig', array(
            'videos' => $videos,
        ));
    }

    /**
     * Creates a new product entity.
     *
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productImages = $product->getProductImages();
            foreach ($productImages as $key => $productImage) {
                $productImage->setProduct($product);
                $productImages->set($key, $productImage);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a product entity.
     *
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($id);

        $editForm = $this->createFormBuilder($product)
            ->add('name')
            ->add('imageFile', VichFileType::class, array('required' => false))
            ->getForm();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->persist($product);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('product/edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $videos = $em->getRepository('AppBundle:Videos')->find($id);

        $em->remove($videos);
        $em->flush();


        return $this->redirectToRoute('indexGallery');
    }
    /**
     * Deletes a product entity.
     *
     */
    public function deleteImageAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $videos = $em->getRepository('AppBundle:ProductImage')->find($id);
        $videos->setProduct(null);
        $em->persist($videos);
        $em->flush();

        $em->remove($videos);
        $em->flush();


        return $this->redirectToRoute('indexGallery');
    }
    /**
     * Deletes a product entity.
     *
     */
    public function deleteAlbumAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $videos = $em->getRepository('AppBundle:Product')->find($id);
        $em->persist($videos);
        $em->flush();

        $em->remove($videos);
        $em->flush();


        return $this->redirectToRoute('indexGallery');
    }

    public function newVedioAction(Request $request)
    {
        $product = new Videos();
        $form = $this->createForm(VideosType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $url = $form['url']->getData();
            $product->setUrl('https://www.youtube.com/embed/'.$url);
            $product->setImageName('http://img.youtube.com/vi/'.$url.'/0.jpg');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('video_index');
        }

        return $this->render('product/newVideos.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

}
