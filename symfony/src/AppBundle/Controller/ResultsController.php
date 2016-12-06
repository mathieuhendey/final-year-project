<?php

namespace AppBundle\Controller;

use AppBundle\Service\AnalysisGetter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ResultsController extends Controller
{
    /**
     * @var AnalysisGetter
     */
    private $analysisGetter;

    public function __construct(AnalysisGetter $analysisGetter)
    {
        $this->analysisGetter = $analysisGetter;
    }

    /**
     * @Route("/results/{", name="results")
     * @Template("default/results.html.twig")
     *
     * @return array
     */
    public function resultsAction(): array
    {
        return ['test' => 'todo'];
    }
}