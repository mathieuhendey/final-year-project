<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service;

class AnalysisTermValidator
{
    const TOO_MANY_HASHTAGS = 'You can only search for one hashtag at a time';
    const TOO_MANY_KEYWORDS = 'You can only search for up to 400 keywords at a time';
    const TOO_MANY_SCREEN_NAMES = 'You can only search for one user at a time';
    const TOO_MANY_TERM_TYPES = 'You can only search for a user OR a hashtag OR some keywords';
    const MUST_ENTER_SEARCH_TERM = 'You must enter a search term';

    /**
     * @var bool
     */
    protected $hasKeywords;

    /**
     * @var bool
     */
    protected $hasHashtags;

    /**
     * @var bool
     */
    protected $hasScreenNames;

    /**
     * @param string $analysisTerm what the user typed into the search box
     *
     * @return array any validation errors encountered
     */
    public function validate(string $analysisTerm): array
    {
        $validationMessages = [];

        if (strlen($analysisTerm) === 0) {
            $validationMessages[] = self::MUST_ENTER_SEARCH_TERM;

            return $validationMessages;
        }

        $this->hasKeywords = false;
        $this->hasHashtags = false;
        $this->hasScreenNames = false;

        $analysisTerm = explode(' ', $analysisTerm);

        if (!$this->checkKeywords($analysisTerm)) {
            $validationMessages[] = self::TOO_MANY_KEYWORDS;
        }

        if (!$this->checkHashtags($analysisTerm)) {
            $validationMessages[] = self::TOO_MANY_HASHTAGS;
        }

        if (!$this->checkScreenNames($analysisTerm)) {
            $validationMessages[] = self::TOO_MANY_SCREEN_NAMES;
        }

        foreach ($analysisTerm as $word) {
            $firstLetterOfWord = substr($word, 0, 1);
            if ($firstLetterOfWord !== '#' && $firstLetterOfWord !== '@') {
                $this->hasKeywords = true;
            }
        }

        $numberOfTermTypes = count(
            array_filter(
                [$this->hasKeywords, $this->hasHashtags, $this->hasScreenNames],
                function ($v) {
                return $v === true;
                }
            )
        );

        if ($numberOfTermTypes > 1) {
            $validationMessages[] = self::TOO_MANY_TERM_TYPES;
        }

        return $validationMessages;
    }

    /**
     * @param string[] $analysisTerm
     *
     * @return bool only one hashtag in analysis term
     */
    private function checkHashtags(array $analysisTerm): bool
    {
        $numberOfHashtags = 0;
        foreach ($analysisTerm as $word) {
            if (substr($word, 0, 1) === '#') {
                $numberOfHashtags += 1;
                $this->hasHashtags = true;
            }
        }

        return $numberOfHashtags <= 1;
    }

    /**
     * @param string[] $analysisTerm
     *
     * @return bool 400 or fewer keywords in analysis term
     */
    private function checkKeywords(array $analysisTerm): bool
    {
        return count($analysisTerm) <= 400;
    }

    /**
     * @param string[] $analysisTerm
     *
     * @return bool only one screen name in analysis term
     */
    private function checkScreenNames(array $analysisTerm): bool
    {
        $numberOfScreenNames = 0;
        foreach ($analysisTerm as $word) {
            if (substr($word, 0, 1) === '@') {
                $numberOfScreenNames += 1;
                $this->hasScreenNames = true;
            }
        }

        return $numberOfScreenNames <= 1;
    }
}
