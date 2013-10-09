<?php

namespace sekjun9878\CakePanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use sekjun9878\CakePanelBundle\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Guzzle\Http\Client;

class NodesController extends Controller
{
    public function nodesAction(Request $request)
    {
	    $page = array(
		    'title' => 'Nodes'
	    );

	    $nodes = $this->getDoctrine()
		    ->getRepository('sekjun9878CakePanelBundle:Node')
		    ->findAll();

        return $this->render('sekjun9878CakePanelBundle:Nodes:nodes.html.twig', array('nodes' => $nodes, 'page' => $page));
    }

	public function testNodeAction($id, Request $request)//TODO: Firewall to make ID numeric only.
	{
		$node = $this->getDoctrine()
			->getRepository('sekjun9878CakePanelBundle:Node')
			->findOneBy(array(
				'id' => $id,
			));

		if(!$node)
		{
			$this->get('session')->getFlashBag()->add(
				'error',
				'Node Not Found!'
			);
			return $this->redirect($this->generateUrl('sekjun9878_cake_panel_nodes'));
		}

		$authKeys = $node->getAuthKeys();
		$username = $authKeys[0];
		$password = hash('sha512', $authKeys[1].':'.idate('i').':'.$authKeys[2]);

		$guzzle = new Client('http://'.$node->getDomain().':'.$node->getPort());
		$guzzle->setDefaultOption('auth', array($username, $password, 'Basic'));
		$request = $guzzle->get('/ping');
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
			return $this->redirect($this->generateUrl('sekjun9878_cake_panel_nodes'));
		}
		$code = $response->getStatusCode();
		$response = $response->getBody();

		if($code === 200 and $response == 'pong')
		{
			$this->get('session')->getFlashBag()->add(
				'success',
				'Test Successful!'
			);
		}
		else if($code === 401)
		{
			$this->get('session')->getFlashBag()->add(
				'error',
				'Invalid Keys.'
			);
		}
		else if($response === false)
		{
			$this->get('session')->getFlashBag()->add(
				'error',
				'Node Offline.'
			);
		}
		else
		{
			$this->get('session')->getFlashBag()->add(
				'error',
				'Unknown Error!'
			);
		}

		return $this->redirect($this->generateUrl('sekjun9878_cake_panel_nodes'));
	}

	public function addNodeAction(Request $request)//TODO: Add success message
	{
		$node = new Node();

		$form = $this->createFormBuilder($node)
			->add('Domain', 'text')
			->add('Port', 'integer')
			->add('submit', 'submit')
			->getForm();

		$form->handleRequest($request);

		if ($form->isValid()) {

			//Check server validity and initialise it.

			$ch = curl_init();//TODO: Replace with Guzzle
			curl_setopt($ch, CURLOPT_URL, "http://".$node->getDomain().":".$node->getPort()."/index.php/init");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($ch, CURLOPT_HEADER, 1); // Don't return header stupid!!
			$response = curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if($response === false)
			{
				$form->addError(new FormError('Node Unreachable.'));
				return $this->render('sekjun9878CakePanelBundle:Nodes:addNode.html.twig', array(
					'form' => $form->createView(),
				));
			}
			else if($code != 200)
			{
				$form->addError(new FormError('Remote Error.'));
				return $this->render('sekjun9878CakePanelBundle:Nodes:addNode.html.twig', array(
					'form' => $form->createView(),
				));
			}

			$json = json_decode(base64_decode($response), true);

			$node->setAuthMethod("HTTP_BASIC_3KEY");
			$node->setAuthKeys($json['authkeys']);//Just directly store array

			$em = $this->getDoctrine()->getManager();
			$em->persist($node);
			$em->flush();

			return $this->redirect($this->generateUrl('sekjun9878_cake_panel_nodes'));
		}

		return $this->render('sekjun9878CakePanelBundle:Nodes:addNode.html.twig', array(
			'form' => $form->createView(),
		));
	}

	public function removeNodeAction($id, Request $request)
	{
		$node = $this->getDoctrine()
			->getRepository('sekjun9878CakePanelBundle:Node')
			->findOneBy(array(
				'id' => $id,
			));

		$em = $this->getDoctrine()->getManager();
		$em->remove($node);
		$em->flush();

		$this->get('session')->getFlashBag()->add(
			'success',
			'Node Removed!'
		);
		return $this->redirect($this->generateUrl('sekjun9878_cake_panel_nodes'));
	}
}
