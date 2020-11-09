<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\Partner;
use AppBundle\Entity\ProductImage;
use AppBundle\Entity\Slider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    public function donationListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:Donate')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('AdminBundle:Default:usersDonation.html.twig', ['dons' => $pagination]);
    }

    public function donationFilterAction(Request $request, $filter)
    {
        $em = $this->getDoctrine()->getManager();
        if ($filter == "payed") {
            $users = $em->getRepository('AppBundle:Donate')->findByEtat('Payé');
        } elseif
        ($filter == "refused") {
            {
                $users = $em->getRepository('AppBundle:Donate')->findByEtat('refused');
            }
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('AdminBundle:Default:usersDonation.html.twig', ['dons' => $pagination]);
    }

    public function excelexportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')) {
            if ($request->get("etat") == 1) {
                $query = $em->createQuery("SELECT us.nom,us.prenom,dn.societe,us.email,dn.etat,dn.dateCreation,dn.montant,dn.telephone FROM
                                           AppBundle:Donate dn INNER JOIN AppBundle:User us WHERE dn.donator = us.id  and dn.etat =:paye 
                                           and dn.dateCreation >= :de and dn.dateCreation <= :a ")
                    ->setParameter('paye', 'Payé')
                    ->setParameter('de', $request->get("datede") . '%')
                    ->setParameter('a', $request->get("datea") . '%');
                $query->getResult();
                $projects = $query->getResult();

            } elseif ($request->get("etat") == 0) {
                $query = $em->createQuery("SELECT us.nom,us.prenom,dn.societe,us.email,dn.etat,dn.dateCreation,dn.montant,dn.telephone FROM
         AppBundle:Donate dn INNER JOIN AppBundle:User us WHERE dn.donator = us.id  and (dn.dateCreation >= :de and dn.dateCreation <= :a)")
                    ->setParameter('de', $request->get("datede") . '%')
                    ->setParameter('a', $request->get("datea") . '%');
                $projects = $query->getResult();
            }
        }


        $fp = fopen('php://output', 'w');
        $params = array('Nom', 'Prenom', 'Societe', 'email', 'etat', 'Date de Paiment', 'Montant', 'Telephone');
        fputcsv($fp, $params);
        foreach ($projects as $fields) {
            $fields['dateCreation'] = $fields['dateCreation']->format('d/m/Y');;
            $fields['etat'] = utf8_decode($fields['etat']);
            $fields['montant'] = $fields['montant'] . ' TND';
            fputcsv($fp, $fields);
        }
        $response = new Response(stream_get_contents($fp));
        fclose($fp);
        $obj = new \DateTime();
        $dmy = $obj->format('d-m-Y');
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=" . $dmy . "-Dons.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        header('Content-Transfer-Encoding: binary');
        return $response;

    }

    public function excelexportEventAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('post')) {
            if ($request->get("etat") == 1) {
                $query = $em->createQuery("SELECT us.nom,us.prenom,us.email,dn.etat,dn.dateInscrit,dn.montant,us.telephone FROM
                                           AppBundle:EventPart dn INNER JOIN AppBundle:User us WHERE dn.userid = us.id 
                                        and dn.etat =:paye and (dn.dateInscrit >= :de and dn.dateInscrit <= :a)  ")
                    ->setParameter('paye', 'Payé')
                    ->setParameter('de', $request->get("datede") . '%')
                    ->setParameter('a', $request->get("datea") . '%');
                $projects = $query->getResult();

            } elseif ($request->get("etat") == 0) {
                $query = $em->createQuery("SELECT us.nom,us.prenom,us.email,dn.etat,dn.dateInscrit,dn.montant,us.telephone FROM
         AppBundle:EventPart dn INNER JOIN AppBundle:User us WHERE dn.userid = us.id and dn.dateInscrit >= :de and dn.dateInscrit <= :a ")
                    ->setParameter('de', $request->get("datedeb") . '%')
                    ->setParameter('a', $request->get("dateFin") . '%');
                $projects = $query->getResult();
            }
        }
        $fp = fopen('php://output', 'w');
        $params = array('Nom', 'Prenom', 'email', 'etat', 'Date de Paiment', 'Montant', 'Telephone');
        fputcsv($fp, $params);
        foreach ($projects as $fields) {
            $fields['dateInscrit'] = $fields['dateInscrit']->format('d/m/Y');;
            $fields['etat'] = utf8_decode($fields['etat']);
            $fields['montant'] = $fields['montant'] . ' TND';
            fputcsv($fp, $fields);
        }
        $response = new Response(stream_get_contents($fp));
        fclose($fp);
        $obj = new \DateTime();
        $dmy = $obj->format('d-m-Y');

        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=" . $dmy . "Event.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        header('Content-Transfer-Encoding: binary');
        return $response;

    }

    public function findCsvByDateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $from = $request->get('from');
        $to = $request->get('to');
        $btc = $em->getRepository('AppBundle:Donate')->DeleteBtcTimeout($from, $to);
        return $this->redirectToRoute('BTCHistoryadmin');
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

    /**
     * Finds and displays a event entity.
     *
     */
    public function aventParticipationAction(Request $request, Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT V From AppBundle:EventPart V order by V.dateInscrit');
        $recentevent = $query->getResult();

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $recentevent,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('event/eventpart.html.twig', array(
            'event' => $event,
            'parts' => $pagination
        ));
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

    public function listEventPayedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('AppBundle:Event')->findSpecial(1);
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        return $this->render('event/payedEvent.html.twig', ['events' => $pagination]);
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

    public function indexGalleryAdminAction(Request $request, $id)
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
                ->add('url', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('buttonName', TextType::class, array('attr' => array('class' => 'form-control')))
                ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $titre = $form['title']->getData();
                $content = $form['content']->getData();

                $file = $cat->getPhoto();
                if ($file) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
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
                $cat->setButtonName($form['buttonName']->getData());
                $cat->setUrl($form['url']->getData());
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

?>
