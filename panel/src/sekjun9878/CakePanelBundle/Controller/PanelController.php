<?php

namespace sekjun9878\CakePanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use sekjun9878\CakePanelBundle\Entity\Server;
use sekjun9878\CakePanelBundle\Entity\ServerConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Guzzle\Http\Client;

class PanelController extends Controller
{
    public function panelAction($id)
    {
	    $page = array(
		    'title' => 'Servers'
	    );

	    $server = $this->getDoctrine()
		    ->getRepository('sekjun9878CakePanelBundle:Server')
		    ->findOneBy(array(
			    'id' => $id,
			    'ownerID' => $this->getUser()->getId(),
		    ));

        return $this->render('sekjun9878CakePanelBundle:Panel:panel.html.twig', array('server' => $server, 'page' => $page));
    }

	public function playersAction($id)
	{
		$page = array(
			'title' => 'Servers'
		);

		$server = $this->getDoctrine()
			->getRepository('sekjun9878CakePanelBundle:Server')
			->findOneBy(array(
				'id' => $id,
				'ownerID' => $this->getUser()->getId(),
			));

		return $this->render('sekjun9878CakePanelBundle:Panel:players.html.twig', array('server' => $server, 'page' => $page));
	}

	public function actionAction($id, $action, Request $Request_)//I know. Dodgy name.
	{
		$server = $this->getDoctrine()//TODO: Enable administrators to control servers.
			->getRepository('sekjun9878CakePanelBundle:Server')
			->findOneBy(array(
				'id' => $id,
				'ownerID' => $this->getUser()->getId(),
			));

		if(!$server)
		{
			$this->get('session')->getFlashBag()->add(
				'error',
				'Server Does Not Exist!'
			);
			return new RedirectResponse($Request_->headers->get('referer'));
		}

		$node = $this->getDoctrine()
			->getRepository('sekjun9878CakePanelBundle:Node')
			->findOneBy(array(
				'id' => $server->getNode(),
			));

		$authKeys = $node->getAuthKeys();
		$username = $authKeys[0];
		$password = hash('sha512', $authKeys[1].':'.idate('i').':'.$authKeys[2]);

		$guzzle = new Client('http://'.$node->getDomain().':'.$node->getPort());
		$guzzle->setDefaultOption('auth', array($username, $password, 'Basic'));

		$id = $server->getIdNodeMap();

		switch($action)
		{
			case 'start':
				$request = $guzzle->post("/servers/start/{$id}", array(), array(
					'authkey' => $server->getAuthKey(),
				));
				try
				{
					$response = $request->send();
				}
				catch (\Exception $e)
				{
					$this->get('session')->getFlashBag()->add(
						'error',
						$e->getMessage()
					);
					return new RedirectResponse($Request_->headers->get('referer'));
				}
				$code = $response->getStatusCode();
				$response = $response->getBody();
				if($code != 200)
				{
					$this->get('session')->getFlashBag()->add(
						'error',
						'Failed to start server!'
					);
					return new RedirectResponse($Request_->headers->get('referer'));
				}
				if($response != "Server Start Successful")
				{
					$this->get('session')->getFlashBag()->add(
						'error',
						'Failed to start server!'
					);
					return new RedirectResponse($Request_->headers->get('referer'));
				}
				break;
			case 'stop':
				$request = $guzzle->post("/servers/stop/{$id}", array(), array(
					'authkey' => $server->getAuthKey(),
				));
				try
				{
					$response = $request->send();
				}
				catch (\Exception $e)
				{
					$this->get('session')->getFlashBag()->add(
						'error',
						'Unknown Error!'
					);
					return new RedirectResponse($Request_->headers->get('referer'));
				}
				$code = $response->getStatusCode();
				$response = $response->getBody();
				if($code != 200)
				{
					$this->get('session')->getFlashBag()->add(
						'error',
						'Failed to start server!'
					);
					return new RedirectResponse($Request_->headers->get('referer'));
				}
				if($response != "Server Start Successful")
				{
					$this->get('session')->getFlashBag()->add(
						'error',
						'Failed to start server!'
					);
					return new RedirectResponse($Request_->headers->get('referer'));
				}
				break;
		}
	}
}
