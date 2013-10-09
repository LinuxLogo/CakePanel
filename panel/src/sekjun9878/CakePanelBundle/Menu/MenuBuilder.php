<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sekjun9878
 * Date: 27/08/13
 * Time: 6:30 PM
 * To change this template use File | Settings | File Templates.
 */

namespace sekjun9878\CakePanelBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder extends ContainerAware{

	public function mainMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', '');

		//Have to use safe-label to put in icons. Shame KnpMenu doesn't support icon tags. Maybe it does, but I haven't figured out how. Feel free to rewrite.
		$menu->addChild('Dashboard', array('route' => 'sekjun9878_cake_panel_dashboard', 'label' => "<span class='glyphicon glyphicon-home'></span> Dashboard", 'extras' => array('safe_label' => true)));
		$menu->addChild('Servers', array('route' => 'sekjun9878_cake_panel_servers', 'label' => "<span class='glyphicon glyphicon-list'></span> Servers", 'extras' => array('safe_label' => true)));
		$menu->addChild('Statistics', array('route' => 'sekjun9878_cake_panel_dashboard', 'label' => "<span class='glyphicon glyphicon-tasks'></span> Statistics", 'extras' => array('safe_label' => true)));
		$menu->addChild('Nodes', array('route' => 'sekjun9878_cake_panel_nodes', 'label' => "<span class='glyphicon glyphicon-hdd'></span> Nodes", 'extras' => array('safe_label' => true)));
		$menu->addChild('Settings', array('route' => 'sekjun9878_cake_panel_dashboard', 'label' => "<span class='glyphicon glyphicon-wrench'></span> Settings", 'extras' => array('safe_label' => true)));


		$uri = $this->container->get('request')->getRequestUri();
		$array = explode('/', $uri);//TODO: Menu URI explode Could be made more efficient
		$uri = '/'.$array[1];
		$menu->setCurrentUri($uri);
		return $menu;
	}

	public function userMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', 'navbar-right');

		//Have to use safe-label to put in icons. Shame KnpMenu doesn't support icon tags. Maybe it does, but I haven't figured out how. Feel free to rewrite.
		$menu->addChild('Account', array('route' => 'sekjun9878_cake_panel_dashboard', 'label' => "<span class='glyphicon glyphicon-user'></span> Account", 'extras' => array('safe_label' => true)));
		//TODO: Change route


		$uri = $this->container->get('request')->getRequestUri();
		$array = explode('/', $uri);//TODO: Menu URI explode Could be made more efficient
		$uri = '/'.$array[1];
		if($uri == '/account' or $uri == '/login' or $uri == '/register')
		{
			$menu['Account']->setCurrent(true);
		}

		return $menu;
	}
}