<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Donate;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;

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
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $donates = $em->getRepository('AppBundle:Donate')->findByDonator($user);
        $pagination = $paginator->paginate(
            $donates, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );
        return $this->render('donate/donation.html.twig', array(
            'dons' => $pagination,
        ));
    }

    /**
     * Creates a new donate entity.
     *
     */
    public function newAction(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $ip = $request->getClientIp();
            if ($ip == 'unknown') {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            if ($request->isMethod('POST')) {
                $montant = $request->get('totalamount');
                $submittedToken = $request->request->get('_csrf_token');
                if ($this->isCsrfTokenValid('ctp-pay', $submittedToken)) {
                    $donate = new Donate();
                    $donate->setMontant($montant);
                    $donate->setEtat('created');
                    $donate->setClientIp($ip);
                    $donate->setAdresse($request->get('address'));
                    $donate->setTelephone($request->get('phone'));
                    $donate->setSociete($request->get('societe'));
                    $donate->setDateCreation(new \DateTime());
                    $donate->setDonator($user);
                    $donate->setPaymentId('0');
                    $em->persist($donate);
                    $em->flush();
                    $httpClient = HttpClient::create();
                    $response = $httpClient->request(
                        'GET',
                        'https://ipay.clictopay.com/payment/rest/register.do?currency=788&amount=' . $donate->getMontant() * 1000
                        . '&language=fr&orderNumber=' . $donate->getId()
                        . '&password=' . $this->getParameter('pwdcp')
                        . '&userName=' . $this->getParameter('ctp')
                        . '&returnUrl=http://localhost/nourane/web/app_dev.php/checkpayment/' . $donate->getId()
                        . '&failUrl=http://localhost/nourane/web/app_dev.php/payment-failed'
                    );
                    $body = json_decode($response->getContent(), true);
                    $order = $body['orderId'];
                    $donate->setPaymentId($order);
                    $em->persist($donate);
                    $em->flush();
                    return $this->redirect($body['formUrl']);
                }
            }

            return $this->render('donate/index.html.twig', array());
        } else
            return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * Finds and displays a donate entity.
     *
     */
    public function checkPaymentAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $orderId = $request->get('orderId');
        $donates = $em->getRepository('AppBundle:Donate')->find($id);
        $httpClient = HttpClient::create();
        $response = $httpClient->request(
            'GET',
            'https://ipay.clictopay.com/payment/rest/getOrderStatusExtended.do?orderId=' . $orderId .
            '&language=fr&password='
            . $this->getParameter('pwdcp') . '&userName='
            . $this->getParameter('ctp') . '&orderNumber=' . $id
        );
        $body = json_decode($response->getContent(), true);
        if ($body['orderStatus'] == 2) {
            $donates->setEtat('Payé');
            $em->persist($donates);
            $em->flush();
            return $this->render('donate/success.html.twig', array('don' => $donates));
        } elseif ($body['orderStatus'] == 0) {
            $donates->setEtat('created');
            $em->persist($donates);
            $em->flush();
            return $this->render('donate/error.html.twig', array('don' => $donates));
        } else {
            $donates->setEtat('refused');
            $em->persist($donates);
            $em->flush();
            return $this->render('donate/error.html.twig', array('don' => $donates));
        }


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
            ->getForm();
    }

}
