<?php

namespace Kraken\RankingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="proposals")
 */
class Proposal
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Boiler", cascade={"persist"})
     * @ORM\JoinColumn(name="boiler_id", referencedColumnName="id")
     */
    protected $boiler = null;

    /**
     * @Assert\Url(
     *    checkDNS = true,
     *    dnsMessage = "Wygląda na to, że taka strona nie istnieje",
     *    message = "Podany adres jest nieprawidłowy"
     * )
     * @ORM\Column(type="string")
     * @Assert\NotNull(message="Trzeba wypełnić to pole")
     */
    protected $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email(
     *    checkHost = true,
     *    message = "Adres jest nieprawidłowy lub nie istnieje"
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="boolean", name="is_done")
     */
    protected $done = false;

    public function getId()
    {
        return $this->id;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setBoiler($boiler)
    {
        $this->boiler = $boiler;

        return $this;
    }

    public function getBoiler()
    {
        return $this->boiler;
    }

    public function isDone()
    {
        return $this->done;
    }

    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }
}
