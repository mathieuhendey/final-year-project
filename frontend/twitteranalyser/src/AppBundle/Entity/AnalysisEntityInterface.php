<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface AnalysisEntityInterface
{
    public function getId(): int;

    public function getTweets(): Collection;

    public function getType(): string;

    public function getPrettyTerm(): string;

    public function getNumberOfTweets(): int;

    public function getNumberOfPositiveTweets(): int;

    public function getNumberOfNegativeTweets(): int;

    public function getNormalisedNumberOfPositiveTweets(): int;

    public function getNormalisedNumberOfNegativeTweets(): int;

    public function getCreatedOn(): \DateTime;

    public function getUpdatedOn(): \DateTime;
}
