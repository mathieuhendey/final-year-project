<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A topic, as opposed to a user.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnalysisTopicRepository")
 * @ORM\Table(name="analysis_topic")
 */
class AnalysisTopic implements AnalysisEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="term", type="string", nullable=false)
     */
    protected $term;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_hashtag", type="boolean", nullable=false)
     */
    protected $hashtag;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Tweet", mappedBy="analysisTopicId")
     */
    protected $tweets;

    /**
     * @var string
     */
    protected $type;

    public function __construct()
    {
        $this->tweets = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @return Collection
     */
    public function getTweets(): Collection
    {
        return $this->tweets;
    }

    /**
     * @return bool
     */
    public function isHashtag(): bool
    {
        return $this->hashtag;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'topic';
    }
}
