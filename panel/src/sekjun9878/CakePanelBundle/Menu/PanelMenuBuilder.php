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

class PanelMenuBuilder extends ContainerAware{

	public function sideMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', '');
		//$menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked');

		//Have to use safe-label to put in icons. Shame KnpMenu doesn't support icon tags. Maybe it does, but I haven't figured out how. Feel free to rewrite.
		$menu->addChild('Dashboard', array('route' => 'sekjun9878_cake_panel_panel', 'routeParameters' => array('id' => 1)));//TODO: Fix this up!
		$menu->addChild('Players', array('route' => 'sekjun9878_cake_panel_panel_players', 'routeParameters' => array('id' => 1)));
		$menu->addChild('Whitelist', array('route' => 'sekjun9878_cake_panel_nodes_add'));
		$menu->addChild('Threats', array('route' => 'sekjun9878_cake_panel_nodes_add'));
		$menu->addChild('Configuration', array('route' => 'sekjun9878_cake_panel_nodes_add'));

		$menu->setCurrentUri($this->container->get('request')->getRequestUri());
		return $menu;
	}
}