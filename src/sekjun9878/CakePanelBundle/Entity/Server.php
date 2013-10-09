<?php

namespace sekjun9878\CakePanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Server
 *
 * @ORM\Table(name="Servers")
 * @ORM\Entity(repositoryClass="sekjun9878\CakePanelBundle\Entity\ServerRepository")
 */
class Server
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
     * @var integer
     *
     * @ORM\Column(name="Node", type="smallint")
     */
    private $node;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="idNodeMap", type="smallint")
	 */
	private $idNodeMap;

    /**
     * @var integer
     *
     * @ORM\Column(name="Port", type="integer")
     */
    private $port = 19132;

    /**
     * @var integer
     *
     * @ORM\Column(name="OwnerID", type="integer")
     */
    private $ownerID = 0;

    /**
     * @var array
     *
     * @ORM\Column(name="ServerConfig", type="array")
     */
    private $serverConfig;

    /**
     * @var array
     *
     * @ORM\Column(name="Plugins", type="array")
     */
    private $plugins;

    /**
     * @var array
     *
     * @ORM\Column(name="Extras", type="array")
     */
    private $extras;

	/**
	 * @var array
	 *
	 * @ORM\Column(name="AuthKey", type="string")
	 */
	private $authKey;


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
     * Set node
     *
     * @param integer $node
     * @return Server
     */
    public function setNode($node)
    {
        $this->node = $node;
    
        return $this;
    }

    /**
     * Get node
     *
     * @return integer 
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return Server
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
     * Set ownerID
     *
     * @param integer $ownerID
     * @return Server
     */
    public function setOwnerID($ownerID)
    {
        $this->ownerID = $ownerID;
    
        return $this;
    }

    /**
     * Get ownerID
     *
     * @return integer 
     */
    public function getOwnerID()
    {
        return $this->ownerID;
    }

    /**
     * Set serverConfig
     *
     * @param array $serverConfig
     * @return Server
     */
    public function setServerConfig($serverConfig)
    {
        $this->serverConfig = $serverConfig;
    
        return $this;
    }

    /**
     * Get serverConfig
     *
     * @return array
     */
    public function getServerConfig()
    {
        return $this->serverConfig;
    }

    /**
     * Set plugins
     *
     * @param array $plugins
     * @return Server
     */
    public function setPlugins($plugins)
    {
        $this->plugins = $plugins;
    
        return $this;
    }

    /**
     * Get plugins
     *
     * @return array 
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Set extras
     *
     * @param array $extras
     * @return Server
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;
    
        return $this;
    }

    /**
     * Get extras
     *
     * @return array 
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Set authKey
     *
     * @param string $authKey
     * @return Server
     */
    public function setAuthKey($authKey)
    {
        $this->authKey = $authKey;
    
        return $this;
    }

    /**
     * Get authKey
     *
     * @return string 
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * Set idNodeMap
     *
     * @param integer $idNodeMap
     * @return Server
     */
    public function setIdNodeMap($idNodeMap)
    {
        $this->idNodeMap = $idNodeMap;
    
        return $this;
    }

    /**
     * Get idNodeMap
     *
     * @return integer 
     */
    public function getIdNodeMap()
    {
        return $this->idNodeMap;
    }
}