<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Test\Service;

use AppBundle\Service\AnalysisTermValidator;
use PHPUnit\Framework\TestCase;

class AnalysisTermValidatorTest extends TestCase
{
    /**
     * @var AnalysisTermValidator
     */
    private $analysisTermValidator;

    protected function setUp()
    {
        $this->analysisTermValidator = new AnalysisTermValidator();
    }

    /**
     * @param string $analysisTerm
     *
     * @dataProvider validDataProvider
     */
    public function testValid(string $analysisTerm)
    {
        $this->assertEmpty($this->analysisTermValidator->validate($analysisTerm));
    }

    /**
     * @return array
     */
    public function validDataProvider(): array
    {
        return [
            [
                (function (): string {
                    $analysisTerm = '';
                    for ($i = 0; $i < 399; ++$i) {
                        $analysisTerm = $analysisTerm.'test ';
                    }

                    return $analysisTerm;
                })(),
            ],
            ['@test'],
            ['#test'],
        ];
    }

    /**
     * @param string $analysisTerm
     * @param array  $expectedMessages
     *
     * @dataProvider invalidDataProvider
     */
    public function testInvalid(string $analysisTerm, array $expectedMessages)
    {
        $this->assertEquals($expectedMessages, $this->analysisTermValidator->validate($analysisTerm));
    }

    /**
     * @return array
     */
    public function invalidDataProvider(): array
    {
        return [
            'Empty input' => [
                '', [AnalysisTermValidator::MUST_ENTER_SEARCH_TERM],
            ],
            'Too many keywords' => [
                (function (): string {
                    $analysisTerm = '';
                    for ($i = 0; $i < 400; ++$i) {
                        $analysisTerm = $analysisTerm.'test ';
                    }

                    return $analysisTerm;
                })(), [AnalysisTermValidator::TOO_MANY_KEYWORDS],
            ],
            'Too many hashtags' => [
                '#test #test2', [AnalysisTermValidator::TOO_MANY_HASHTAGS],
            ],
            'Too many screen names' => [
                '@test @test2', [AnalysisTermValidator::TOO_MANY_SCREEN_NAMES],
            ],
            'Too many keywords and too many hashtags' => [
                (function (): string {
                    $analysisTerm = '';
                    for ($i = 0; $i < 400; ++$i) {
                        $analysisTerm = $analysisTerm.'test ';
                    }

                    return $analysisTerm.'#test '.'#test2';
                })(), [AnalysisTermValidator::TOO_MANY_KEYWORDS, AnalysisTermValidator::TOO_MANY_HASHTAGS, AnalysisTermValidator::TOO_MANY_TERM_TYPES],
            ],
            'Too many keywords and too many screen names' => [
                (function (): string {
                    $analysisTerm = '';
                    for ($i = 0; $i < 400; ++$i) {
                        $analysisTerm = $analysisTerm.'test ';
                    }

                    return $analysisTerm.'@test '.'@test2';
                })(), [AnalysisTermValidator::TOO_MANY_KEYWORDS, AnalysisTermValidator::TOO_MANY_SCREEN_NAMES, AnalysisTermValidator::TOO_MANY_TERM_TYPES],
            ],
            'Too many hashtags and too many screen names' => [
                '#test #test2 @test1 @test2', [AnalysisTermValidator::TOO_MANY_HASHTAGS, AnalysisTermValidator::TOO_MANY_SCREEN_NAMES, AnalysisTermValidator::TOO_MANY_TERM_TYPES],
            ],
        ];
    }
}
