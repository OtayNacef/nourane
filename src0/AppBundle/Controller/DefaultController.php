<?php

namespace AppBundle\Controller;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Blog;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DefaultController extends Controller
{
    public function policyAction(Request $request)
    {
        return $this->render('policy/policy.html.twig');

    }

    public function successAction(Request $request)
    {
        return $this->render(':donate:success.html.twig');

    }

    public function tosAction(Request $request)
    {
        return $this->render('policy/tos.html.twig');

    }

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT V From AppBundle:Blog V order by V.dateCreation DESC')->setMaxResults(3);
        $bloglatest = $query->getResult();
        $query = $em->createQuery('SELECT V From AppBundle:Event V order by V.start DESC')
            ->setMaxResults(5);
        $events = $query->getResult();

        $query = $em->createQuery('SELECT V From AppBundle:Team V')->setMaxResults(4);
        $team = $query->getResult();
        $sliders = $em->getRepository('AppBundle:Slider')->findAll();

        if ($request->isMethod('POST')) {

            return new Response($request->get('don'));
        }
        $friends = $em->getRepository('AppBundle:Partner')->findAll();

        return $this->render('default/index.html.twig', array(
            'sliders' => $sliders,
            'blogs' => $bloglatest,
            'events' => $events,
            'team' => $team,
            'partner' => $friends

        ));
    }


    public function AboutusAction()
    {
        $sn = $this->getDoctrine()->getManager();
        $cat = $sn->getRepository('AppBundle:Association')->findAll();
        $friends = $sn->getRepository('AppBundle:Partner')->findAll();
        $query = $sn->createQuery('SELECT V From AppBundle:Team V')->setMaxResults(4);
        $team = $query->getResult();
        return $this->render('default/aboutus.html.twig', array(
            'cat' => $cat,
            'team' => $team,
            'partner' => $friends
        ));
    }

    public function article1Action()
    {
        return $this->render('default/Article1.html.twig');
    }

    public function article2Action()
    {
        return $this->render('default/Article2.html.twig');
    }

    public function article3Action()
    {
        return $this->render('default/Article3.html.twig');
    }

    public function failAction()
    {
        return $this->render(':donate:error.html.twig');
    }
    /**
     * Lists all blog entities.
     *
     */
    public function indexBlogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('AppBundle:Blog')->findBy([], ['dateCreation' => 'DESC']);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $blog,
            $request->query->getInt('page', 1)/*page number*/,
            4/*limit per page*/
        );
        $query = $em->createQuery('SELECT V From AppBundle:Blog V order by V.dateCreation')->setMaxResults(3);
        $blogmax = $query->getResult();

        return $this->render('blog/index.html.twig', array(
            'blogs' => $pagination,
            'blogsmax' => $blogmax,
        ));
    }

    /**
     * Creates a new blog entity.
     *
     */
    public function newAction(Request $request)
    {
        $blog = new Blog();
        $form = $this->createFormBuilder($blog)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Titre *')))
            ->add('content', CKEditorType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'content *')))
            ->add('dateCreation', DateTimeType::class, array('attr' => array('class' => 'form-control')))
            ->add('imageFile', VichFileType::class, array('data_class' => null))
            ->getForm();
        $form->handleRequest($request);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $blog->setAuthor($user);
            $em->persist($blog);

            $em->flush();

            return $this->redirectToRoute('addBlog');
        }

        return $this->render('blog/new.html.twig', array(
            'blog' => $blog,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a blog entity.
     *
     */
    public function showAction(Request $request, $blog)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('AppBundle:Blog')->find($blog);
        $query = $em->createQuery('SELECT V From AppBundle:Blog V order by V.dateCreation')->setMaxResults(3);
        $blogmax = $query->getResult();

        return $this->render('blog/show.html.twig', array(
            'blog' => $blog,
            'latest' => $blogmax
        ));
    }

    /**
     * Displays a form to edit an existing blog entity.
     *
     */
    public function editBlogAction(Request $request, $blog)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $em = $this->getDoctrine()->getManager();

            $blog = $em->getRepository('AppBundle:Blog')->find($blog);

            $editForm = $this->createFormBuilder($blog)
                ->add('title', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Titre *')))
                ->add('content', CKEditorType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'content *')))
                ->add('dateCreation', DateTimeType::class)
                ->add('imageFile', VichFileType::class, array('required' => false, 'data_class' => null))
                ->getForm();
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('indexAdminActualite');
            }

            return $this->render('blog/edit.html.twig', array(
                'blog' => $blog,
                'edit_form' => $editForm->createView(),
            ));
        }
    }

    public function deleteAction($blog)
    {


        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository("AppBundle:Blog")->find($blog);
        $em->remove($blog);
        $em->flush();


        return $this->redirectToRoute('indexAdminActualite');
    }

    public function RechercheBlogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $entities = $em->getRepository('AppBundle:Blog')->AjaxRecherche($requestString);
        if (!$entities) {
            $result['entities']['error'] = "there is no Blog ";
        } else {
            $result['entities'] = $this->getRealEntities($entities);
        }
        return new Response(json_encode($result));

    }


    public function getRealEntities($entities)
    {
        foreach ($entities as $entity) {
            $realEntities[$entity->getId()] = [
                $entity->getTitle(),
                $entity->getContent(),
                $entity->getCategorie(),
                $entity->getImage(),
                $entity->getAuthor()->getNom(),
                $entity->getDateCreation()->format("Y-m-d"),
            ];
        }
        return $realEntities;
    }
}
