<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Team controller.
 *
 */
class TeamController extends Controller
{
    /**
     * Lists all team entities.
     *
     */
    public function indexAction(Request $request,PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $teams = $em->getRepository('AppBundle:Team')->findAll();
        $pagination = $paginator->paginate(
            $teams, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            38 /*limit per page*/
        );
        return $this->render('team/index.html.twig', array(
            'teams' => $pagination,
        ));
    }
    /**
     * Lists all team entities.
     *
     */
    public function indexTeamAdminAction()
    {



            $em = $this->getDoctrine()->getManager();

            $teams = $em->getRepository('AppBundle:Team')->findAll();

            return $this->render('team/indexTeam.html.twig', array(
                'teams' => $teams,
            ));

    }
    /**
     * Creates a new team entity.
     *
     */
    public function newAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'êtes pas autorisés à accéder à cette page!", Response::HTTP_FORBIDDEN);
        }

        $team = new Team();
        $form = $this->createForm('AppBundle\Form\TeamType', $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();

            return $this->redirectToRoute('teamAdmin');
        }

        return $this->render('team/new.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a team entity.
     *
     */
    public function showAction(Team $team)
    {
        $deleteForm = $this->createDeleteForm($team);

        return $this->render('team/show.html.twig', array(
            'team' => $team,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing team entity.
     *
     */
    public function editAction(Request $request, Team $team)
    {
        $editForm = $this->createForm('AppBundle\Form\TeamType', $team);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('teamAdmin');
        }

        return $this->render('team/edit.html.twig', array(
            'team' => $team,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a team entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('AppBundle:Team')->find($id);

        $em->remove($team);
            $em->flush();


        return $this->redirectToRoute('teamAdmin');
    }


}
