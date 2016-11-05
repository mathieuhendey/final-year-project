<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A generic Tweet. This could be a top-level Tweet
 * (i.e. the first Tweet in a thread of replies), or a reply to a top-level
 * Tweet.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 *
 * @ORM\Entity
 * @ORM\Table(name="tweet")
 */
class Tweet
{
}
