<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Entity;

/**
 * A Twitter user. Will store data relevant to the app's purpose, such as the
 * first time they were scanned by the app, their popularity score, as well
 * as any information from Twitter that could be useful (sign-up date, whether
 * they have a blue tick, whether their account is private etc.).
 *
 * This does not store any access credentials, I am still
 * deciding whether to persist credentials to the DB or just make the user
 * authorise again each time they visit the app.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 *
 * @ORM\Entity
 * @ORM\Table(name="twitter_user")
 */
class TwitterUser
{
}
