<?php

namespace sekjun9878\CakePanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use sekjun9878\CakePanelBundle\Entity\Server;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Guzzle\Http\Client;

class ServersController extends Controller
{
    public function serversAction()
    {
	    $page = array(
		    'title' => 'Servers'
	    );

	    $servers = $this->getDoctrine()
		    ->getRepository('sekjun9878CakePanelBundle:Server')
		    ->findAll();

	    $nodes = $this->getDoctrine()
		    ->getRepository('sekjun9878CakePanelBundle:Node')
		    ->findAll();

	    $nodesIndex = array();

	    foreach($nodes as $node)
	    {
		    $nodesIndex[$node->getId()] = $node;
	    }

        return $this->render('sekjun9878CakePanelBundle:Servers:servers.html.twig', array('nodes' => $nodesIndex, 'servers' => $servers, 'page' => $page));
    }

	public function createServerAction(Request $request)
	{
		$page = array(
			'title' => 'Add Server'
		);

		$server = new Server();

		$server->setExtras(array());
		$server->setPlugins(array());
		$server->setServerConfig(array(
			'server-name' => "MCPE Server",
			'description' => "Server Managed by CakePanel",
			'motd' => "Welcome!",
			'server-ip' => "",
			'server-port' => 19132,
			'server-type' => "normal",
			'memory-limit' => "128M",
			'last-update' => "off",
			'white-list' => "off",
			'spawn-protection' => 16,
			'view-distance' => 10,
			'max-players' => 20,
			'allow-flight' => "off",
			'spawn-animals' => "on",
			'spawn-mobs' => "on",
			'gamemode' => 0,
			'hardcore' => "off",
			'pvp' => "on",
			'difficulty' => 1,
			'generator-settings' => "",
			'level-name' => "world",
			'level-seed' => "",
			'level-type' => "DEFAULT",
			'enable-query' => "on",
			'enable-rcon' => "off",
			'rcon.password' => "123456",
			'send-usage' => "on",
			'auto-save' => "on",
		));

		$nodes = $this->getDoctrine()
			->getRepository('sekjun9878CakePanelBundle:Node')
			->findAll();

		$nodes_array = array();
		foreach($nodes as $n)//Generate a map of Nodes for dropdown.
		{
			$nodes_array[$n->getId()] = $n->getDomain()." (Port: ".$n->getPort().")";
		}

		$userManager = $this->get('fos_user.user_manager');
		$users = $userManager->findUsers();

		$users_array = array();
		foreach($users as $n)//Generate a map of Users for dropdown.
		{
			$users_array[$n->getId()] = $n->getUsername()." (ID: ".$n->getId().")";
		}

		$form = $this->createFormBuilder($server)
			->add('Node', 'choice', array(
				'choices' => $nodes_array,
			))
			->add('Port', 'integer')
			/*->add('ServerConfig', 'collection', array(
			// each item in the array will be an "email" field
			//'type'   => 'text',
			// these options are passed to each "email" type
			'options'  => array(
				'required'  => true,
				//'attr'      => array('class' => 'email-box')
			)))*///Server Config will be edited in a different menu, maybe in Panel.
			->add('OwnerID', 'choice', array(
				'choices' => $users_array,
				'label' => "Owner"
			))
			->add('Create', 'submit')
			->getForm();

		$form->handleRequest($request);

		if ($form->isValid()) {

			$node = $this->getDoctrine()
				->getRepository('sekjun9878CakePanelBundle:Node')
				->findOneBy(array(
					'id' => $server->getNode(),
				));

			//TODO: Add node NULL detection here in case of modified forms.

			$authKeys = $node->getAuthKeys();
			$username = $authKeys[0];
			$password = hash('sha512', $authKeys[1].':'.idate('i').':'.$authKeys[2]);

			$guzzle = new Client('http://'.$node->getDomain().':'.$node->getPort());
			$guzzle->setDefaultOption('auth', array($username, $password, 'Basic'));
			$request = $guzzle->get('/index.php/ping');
			$response = $request->send();
			if($response->getStatusCode() !== 200 or $response->getBody() != 'pong')
			{
				$form->addError(new FormError('Node Unreachable.'));
				return $this->render('sekjun9878CakePanelBundle:Servers:createServer.html.twig', array(
					'form' => $form,
					'page' => $page,
				));
			}

			$request = $guzzle->post('/index.php/servers/create');
			$response = $request->send();
			if($response->getStatusCode() !== 201)
			{
				$form->addError(new FormError('Failed to create server.'));
				return $this->render('sekjun9878CakePanelBundle:Servers:createServer.html.twig', array(
					'form' => $form,
					'page' => $page,
				));
			}

			$response = $response->getBody();

			$response = json_decode(base64_decode($response), true);

			$server->setAuthKey($response['authkey']);
			$server->setIdNodeMap($response['id']);


			$em = $this->getDoctrine()->getManager();
			$em->persist($server);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Server Created!'
			);

			return $this->redirect($this->generateUrl('sekjun9878_cake_panel_servers'));
		}

		return $this->render('sekjun9878CakePanelBundle:Servers:createServer.html.twig', array(
			'form' => $form->createView(),
			'page' => $page,
		));
	}
}
