<?php

namespace sekjun9878\InvoiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Stripe;

class DefaultController extends Controller
{
    public function indexAction()
    {
	    Stripe::setApiKey($this->container->getParameter('stripe.api.key'));
        return $this->render('sekjun9878InvoiceBundle:Default:index.html.twig');
    }
}
