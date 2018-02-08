<?php
namespace BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * AgentNote
 */
class AgentNote
{
    const STATUS_PUBLIC  = true;
    const STATUS_PRIVATE = false;

    public static $visibility = [
       'labels.private' => self::STATUS_PRIVATE,
       'labels.public'  => self::STATUS_PUBLIC,
    ];

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $note;

    /**
     * @var Agent
     */
    private $agent;

    /**
     * @var User
     */
    private $author;

    /**
     * @var boolean
     */
    private $public = true;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param boolean $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed|null
     */
    public function getVisibilityName()
    {
        if (in_array($this->isPublic(), self::$visibility, true)) {
            return array_search($this->isPublic(), self::$visibility);
        }

        return null;
    }
}
