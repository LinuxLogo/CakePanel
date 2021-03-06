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

class ServersMenuBuilder extends ContainerAware{

	public function sideMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', '');
		//$menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked');

		//Have to use safe-label to put in icons. Shame KnpMenu doesn't support icon tags. Maybe it does, but I haven't figured out how. Feel free to rewrite.
		$menu->addChild('Servers', array('route' => 'sekjun9878_cake_panel_servers'));
		$menu->addChild('Create Server', array('route' => 'sekjun9878_cake_panel_servers_create'));

		$menu->setCurrentUri($this->container->get('request')->getRequestUri());
		return $menu;
	}
}