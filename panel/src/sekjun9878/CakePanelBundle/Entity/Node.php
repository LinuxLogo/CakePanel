<?php

namespace sekjun9878\CakePanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 *
 * @ORM\Table(name="Nodes")
 * @ORM\Entity(repositoryClass="sekjun9878\CakePanelBundle\Entity\NodeRepository")
 */
class Node
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Domain", type="string", length=255)
     */
    private $domain;

    /**
     * @var integer
     *
     * @ORM\Column(name="Port", type="integer")
     */
    private $port;

	/**
	 * @var array
	 *
	 * @ORM\Column(name="AuthKeys", type="array")
	 */
	private $authKeys;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="AuthMethod", type="string")
	 */
	private $authMethod;
	
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set domain
     *
     * @param string $domain
     * @return Node
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    
        return $this;
    }

    /**
     * Get domain
     *
     * @return string 
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return Node
     */
    public function setPort($port)
    {
        $this->port = $port;
    
        return $this;
    }

    /**
     * Get port
     *
     * @return integer 
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set authKeys
     *
     * @param array $authKeys
     * @return Node
     */
    public function setAuthKeys($authKeys)
    {
        $this->authKeys = $authKeys;
    
        return $this;
    }

    /**
     * Get authKeys
     *
     * @return array 
     */
    public function getAuthKeys()
    {
        return $this->authKeys;
    }

    /**
     * Set authMethod
     *
     * @param string $authMethod
     * @return Node
     */
    public function setAuthMethod($authMethod)
    {
        $this->authMethod = $authMethod;
    
        return $this;
    }

    /**
     * Get authMethod
     *
     * @return string 
     */
    public function getAuthMethod()
    {
        return $this->authMethod;
    }
}