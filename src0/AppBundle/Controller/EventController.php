<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\EventPart;
use Symfony\Component\HttpClient\HttpClient;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    /**
     * Lists all event entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('AppBundle:Event')->findBy([], ['start' => 'DESC']);
        $pagination = $paginator->paginate(
            $events, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );
        return $this->render('event/index.html.twig', array(
            'events' => $pagination,
        ));
    }

    public function indexMarathonAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('AppBundle:Event')->findBy(["marathon" => 1], ['start' => 'DESC']);
        $pagination = $paginator->paginate(
            $events, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );
        return $this->render('event/indexMarathon.html.twig', array(
            'events' => $pagination,
        ));
    }

    /**
     * Creates a new event entity.
     *
     */
    public function newAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm('AppBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     */
    public function showAction(Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT V From AppBundle:Event V order by V.start')->setMaxResults(3);
        $recentevent = $query->getResult();
        return $this->render('event/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            'latest' => $recentevent
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     */
    public function aventParticipationAction(Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT V From AppBundle:Event V order by V.start')->setMaxResults(3);
        $recentevent = $query->getResult();
        return $this->render('event/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            'latest' => $recentevent
        ));
    }
    /**
     * Displays a form to edit an existing event entity.
     *
     */
    public function editAction(Request $request, Event $event)
    {
        $editForm = $this->createForm('AppBundle\Form\EventType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('indexEventAdmin');
        }

        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->find($id);

        $em->remove($event);
        $em->flush();


        return $this->redirectToRoute('indexEventAdmin');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Event $event The event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Creates a new donate entity.
     *
     */
    public function paymentAction(Request $request, $id)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $event = $em->getRepository('AppBundle:Event')->find($id);
            $ip = $request->getClientIp();
            if ($ip == 'unknown') {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $montant = $request->get('totalamount');
            dump($montant);
            if ($request->isMethod('post')) {
                $montant = $request->get('totalamount');
                $submittedToken = $request->request->get('_csrf_token');
                if ($this->isCsrfTokenValid('ctp-pay', $submittedToken)) {
                    $part = new EventPart();
                    $part->setMontant($montant);
                    $part->setEtat('created');
                    $part->setEvenement($event);
                    $part->setClientIp($ip);
                    $part->setDateInscrit(new \DateTime());
                    $part->setUserid($user);
                    $part->setPaymentId('0');
                    $em->persist($part);
                    $em->persist($user);
                    $em->flush();
                    $httpClient = HttpClient::create();
                    $response = $httpClient->request(
                        'GET',
                        'https://ipay.clictopay.com/payment/rest/register.do?currency=788&amount=' . $part->getMontant() * 1000
                        . '&language=fr&orderNumber=' . $part->getId()
                        . '&password=' . $this->getParameter('pwdcp')
                        . '&userName=' . $this->getParameter('ctp')
                        . '&returnUrl=http://localhost/nourane/web/app_dev.php/payment-check/' . $part->getId() . '/'
                        . '&failUrl=http://localhost/nourane/web/app_dev.php/payment-check/'. $part->getId() . '/'
                    );
                    $body = json_decode($response->getContent(), true);
                    $order = $body['orderId'];
                    $part->setPaymentId($order);
                    $em->persist($part);
                    $em->flush();
                    return $this->redirect($body['formUrl']);
                }
            }

            return $this->redirectToRoute('failurl');
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
        $events = $em->getRepository('AppBundle:EventPart')->find($id);
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
            $events->setEtat('Payé');
            $em->persist($events);
            $em->flush();
            return $this->render('donate/success.html.twig', array('don' => $events));
        } elseif ($body['orderStatus'] == 0) {
            $events->setEtat('created');
            $em->remove($events);
            $em->flush();
            return $this->render('donate/error.html.twig', array('don' => $events));
        } else {
            $events->setEtat('refused');
            $em->persist($events);
            $em->flush();
            return $this->render('donate/error.html.twig', array('don' => $events));
        }
    }

}

