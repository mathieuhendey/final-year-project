<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Handles search Twitter for a given search term
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 *
 * @Route(service="app.search_controller")
 */
class SearchController
{
    /**
     * SearchController constructor.
     */
    public function __construct()
    {

    }


    /**
     * @Route("/search", name="search")
     * @Template("default/search.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function searchAction(Request $request): array
    {

    }

}
