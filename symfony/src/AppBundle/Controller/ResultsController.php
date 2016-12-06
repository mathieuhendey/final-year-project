<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;

use AppBundle\Service\AnalysisGetter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Handles the page showing the results of the analysis of a term.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 *
 * @Route(service="app.results_page_controller")
 */
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