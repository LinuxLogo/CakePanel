<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sekjun9878
 * Date: 3/10/13
 * Time: 9:05 PM
 * To change this template use File | Settings | File Templates.
 */

namespace sekjun9878\CakePanelBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 */
class User extends BaseUser
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	public function __construct()
	{
		parent::__construct();
		//TODO: Own Logic
	}

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}