<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Donate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Donate controller.
 *
 */
class DonateController extends Controller
{
    /**
     * Lists all donate entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $donates = $em->getRepository('AppBundle:Donate')->findAll();

        return $this->render('donate/index.html.twig', array(
            'donates' => $donates,
        ));
    }

    /**
     * Creates a new donate entity.
     *
     */
    public function newAction(Request $request)
    {
        $donate = new Donate();
        $form = $this->createForm('AppBundle\Form\DonateType', $donate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($donate);
            $em->flush();

            return $this->redirectToRoute('donate_show', array('id' => $donate->getId()));
        }

        return $this->render('donate/new.html.twig', array(
            'donate' => $donate,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a donate entity.
     *
     */
    public function showAction(Donate $donate)
    {
        $deleteForm = $this->createDeleteForm($donate);

        return $this->render('donate/show.html.twig', array(
            'donate' => $donate,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing donate entity.
     *
     */
    public function editAction(Request $request, Donate $donate)
    {
        $deleteForm = $this->createDeleteForm($donate);
        $editForm = $this->createForm('AppBundle\Form\DonateType', $donate);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('donate_edit', array('id' => $donate->getId()));
        }

        return $this->render('donate/edit.html.twig', array(
            'donate' => $donate,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a donate entity.
     *
     */
    public function deleteAction(Request $request, Donate $donate)
    {
        $form = $this->createDeleteForm($donate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($donate);
            $em->flush();
        }

        return $this->redirectToRoute('donate_index');
    }

    /**
     * Creates a form to delete a donate entity.
     *
     * @param Donate $donate The donate entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Donate $donate)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('donate_delete', array('id' => $donate->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
