<?php
/**
 * Created by PhpStorm.
 * User: ZeROo
 * Date: 15/11/2019
 * Time: 10:17
 */

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;



class Variables extends Controller
{

    private $nbrOrders;
    private $panier;
    private $panierCount;
    private $subTotal;



    protected $em;
    protected $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;

    }


    private function panier()
    {

        $session = new Session();
        if (!$session->has('panier')) $session->set('panier', array());
        $panier = $session->get('panier');

        return $panier;
    }

    private function subTotal()
    {


        $panier = $this->panier();
        $total = 0;
        foreach ($panier as $p){

            $total = $total + $p['prix'];

        }

        return $total;

    }
    private function panierCount()
    {

        if ($this->panier()){

            $t= count($this->panier());

        }
        else{
            $t= 0;
        }
        return $t;
    }


    private function nbrOrders()
    {
        $query = $this->em->createQuery(
            'SELECT Count(s)
                        FROM AppBundle:Orders s where s.etat = 0');

        return $query->getResult();
    }

    /**
     * @return mixed
     */
    public function getNbrOrders()
    {
        return $this->nbrOrders = $this->nbrOrders()[0][1];
    }

    /**
     * @return mixed
     */
    public function getPanier()
    {
        return $this->panier = $this->panier();
    }

    /**
     * @return mixed
     */
    public function getPanierCount()
    {
        return $this->panierCount = $this->panierCount();
    }

    /**
     * @return mixed
     */
    public function getSubTotal()
    {
        return $this->subTotal = $this->subTotal();
    }








}