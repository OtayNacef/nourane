<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Partner;
use AppBundle\Entity\ProductImage;
use AppBundle\Entity\Slider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DefaultController extends Controller
{

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        return $this->render('AdminBundle:Default:index.html.twig');
    }

    public function userAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('AdminBundle:Default:users.html.twig', ['users' => $pagination]);
    }

    public function listactualiteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:Blog')->findAll();
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('blog/indexAdmin.html.twig', ['blogs' => $pagination]);
    }

    public function listEventAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('AppBundle:Event')->findAll();
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('event/indexadmin.html.twig', ['events' => $pagination]);
    }

    public function indexGalleryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository('AppBundle:Videos')->findAll();
        $photos = $em->getRepository('AppBundle:Product')->findAll();

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $photos,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        $pagination2 = $paginator->paginate(
            $videos,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );

        return $this->render('@Admin/Default/adminGallery.twig', array(
            'videos' => $pagination2,
            'photos' => $pagination
        ));
    }
    public function indexGalleryAdminAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository('AppBundle:Videos')->findAll();
        $album = $em->getRepository('AppBundle:Product')->find($id);

        $photos = $em->getRepository('AppBundle:ProductImage')->findByProduct($album);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $photos,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        $pagination2 = $paginator->paginate(
            $videos,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        $image = new ProductImage();
        $form = $this->createFormBuilder($image)
            ->add('imageFile', VichFileType::class, array('data_class' => null))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $image->setProduct($album);
            $em->persist($image);

            $em->flush();

            return $this->redirectToRoute('indexGalleryAdmin', array(
                'id' => $album->getId()));
        }
        return $this->render('@Admin/Default/adminGalleryshow.twig', array(
            'videos' => $pagination2,
            'photos' => $pagination,
            'form' => $form->createView(),

        ));
    }
    public function adminBlockAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $findUser = $em->getRepository('AppBundle:User')->find($id);
        $a = $findUser->isEnabled();
        if ($a == false) {
            $findUser->setEnabled(true);
        } else {
            $findUser->setEnabled(false);
        }
        $em->flush();
        return $this->redirectToRoute('users');
    }

    public function adminDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $findUser = $em->getRepository('AppBundle:User')->find($id);
        $em->remove($findUser);
        $em->flush();
        return $this->redirectToRoute('users');
    }

    public function deleteSliderAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $findUser = $em->getRepository('AppBundle:Slider')->find($id);
        $em->remove($findUser);
        $em->flush();
        return $this->redirectToRoute('listSlider');
    }

    public function addSliderAction(Request $request)
    {

        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $cat = new Slider();
            $sn = $this->getDoctrine()->getManager();

            $form = $this->createFormBuilder($cat)
                ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('content', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
                ->add('photo', FileType::class, array('required' => false, 'data_class' => null, 'label' => 'upload your photo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $titre = $form['title']->getData();
                $content = $form['content']->getData();

                $file = $cat->getPhoto();

                if ($file) {

                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                    // Move the file to the directory where brochures are stored

                    $file->move(
                        $this->getParameter('images'),
                        $fileName
                    );

                } else {

                    $fileName = "";
                }


                $cat->setTitle($titre);
                $cat->setContent($content);
                $cat->setPhoto($fileName);

                $sn->persist($cat);
                $sn->flush();

                return $this->redirectToRoute('listSlider');

            }

            return $this->render('AdminBundle:Default:addSlider.html.twig', array(
                'form' => $form->createView()
            ));

        } else
            return $this->redirectToRoute('fos_user_security_login');


    }

    public function listSliderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('AppBundle:Slider')->findAll();
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('AdminBundle:Default:listSliders.html.twig', ['sliders' => $pagination]);
    }

    public function editSliderAction($id, Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $cat = $this->getDoctrine()
                ->getRepository('AppBundle:Slider')
                ->find($id);
            $oldFile = $cat->getPhoto();

            $cat->setTitle($cat->getTitle());
            $cat->setContent($cat->getContent());


            $form = $this->createFormBuilder($cat)
                ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('content', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
                ->add('photo', FileType::class, array('required' => false, 'data_class' => null, 'label' => 'upload your photo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {


                $titre = $form['title']->getData();
                $desc = $form['content']->getData();


                if ($cat->getPhoto() != null) {
                    $file = $cat->getPhoto();

                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();


                    $file->move(
                        $this->getParameter('images'),
                        $fileName
                    );

                    $cat->setPhoto($fileName);
                } else {

                    $cat->setPhoto($oldFile);
                }

                $sn = $this->getDoctrine()->getManager();

                $cat->setTitle($titre);
                $cat->setContent($desc);

                $sn->persist($cat);
                $sn->flush();

                return $this->redirectToRoute('listSlider');
            }

            return $this->render('AdminBundle:Default:editSlider.html.twig', array(
                'cat' => $cat,
                'form' => $form->createView()
            ));
        } else
            return $this->redirectToRoute('fos_user_security_login');
    }


    public function deleteFriendAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $findUser = $em->getRepository('AppBundle:Partner')->find($id);
        $em->remove($findUser);
        $em->flush();
        return $this->redirectToRoute('listFriend');
    }

    public function addFriendAction(Request $request)
    {

        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $cat = new Partner();
            $sn = $this->getDoctrine()->getManager();

            $form = $this->createFormBuilder($cat)
                ->add('photo', FileType::class, array('required' => false, 'data_class' => null, 'label' => 'upload your photo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {


                $file = $cat->getPhoto();

                if ($file) {

                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                    // Move the file to the directory where brochures are stored

                    $file->move(
                        $this->getParameter('images'),
                        $fileName
                    );

                } else {

                    $fileName = "";
                }

                $cat->setPhoto($fileName);

                $sn->persist($cat);
                $sn->flush();

                return $this->redirectToRoute('listFriend');

            }

            return $this->render('AdminBundle:Default:addFriend.html.twig', array(
                'form' => $form->createView()
            ));

        } else
            return $this->redirectToRoute('fos_user_security_login');


    }

    public function listFriendAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('AppBundle:Partner')->findAll();
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('AdminBundle:Default:listFriend.html.twig', ['sliders' => $pagination]);
    }

    public function editFriendAction($id, Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $cat = $this->getDoctrine()
                ->getRepository('AppBundle:Partner')
                ->find($id);
            $oldFile = $cat->getPhoto();


            $form = $this->createFormBuilder($cat)
                ->add('photo', FileType::class, array('required' => false, 'data_class' => null, 'label' => 'upload your photo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {


                if ($cat->getPhoto() != null) {
                    $file = $cat->getPhoto();

                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();


                    $file->move(
                        $this->getParameter('images'),
                        $fileName
                    );

                    $cat->setPhoto($fileName);
                } else {

                    $cat->setPhoto($oldFile);
                }

                $sn = $this->getDoctrine()->getManager();

                $sn->persist($cat);
                $sn->flush();

                return $this->redirectToRoute('listFriends');
            }

            return $this->render('AdminBundle:Default:editFriend.html.twig', array(
                'cat' => $cat,
                'form' => $form->createView()
            ));
        } else
            return $this->redirectToRoute('fos_user_security_login');
    }

}