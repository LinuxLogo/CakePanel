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

class SecurityMenuBuilder extends ContainerAware{

	public function sideMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', '');
		//$menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked');

		//Have to use safe-label to put in icons. Shame KnpMenu doesn't support icon tags. Maybe it does, but I haven't figured out how. Feel free to rewrite.
		$menu->addChild('Login', array('route' => 'fos_user_security_login'));
		$menu->addChild('Register', array('route' => 'fos_user_registration_register'));

		$menu->setCurrentUri($this->container->get('request')->getRequestUri());
		return $menu;
	}
}