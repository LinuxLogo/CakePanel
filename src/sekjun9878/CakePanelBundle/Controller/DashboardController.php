<?php

namespace sekjun9878\CakePanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    public function dashboardAction()
    {
	    $page = array(
		    'title' => 'Dashboard'
	    );
        return $this->render('sekjun9878CakePanelBundle:Dashboard:dashboard.html.twig', array('page' => $page));
    }
}
