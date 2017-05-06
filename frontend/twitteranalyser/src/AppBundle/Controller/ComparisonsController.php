<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ComparisonsController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/compare", name="compare")
     *
     * @return Response
     */
    public function comparisonsAction(Request $request): Response
    {
        $type = $request->get('term_type');
        $ids = $request->get('term_ids');

        return $this->render(
            'default/comparisons.html.twig', [
                'ids' => $ids,
                'type' => $type,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @Route("/compareAjax", name="compareAjax")
     *
     * @return JsonResponse
     */
    public function compareAjaxAction(Request $request): JsonResponse
    {
        $type = $request->get('term_type');
        $ids = $request->get('term_ids');
        $comparator = $this->get('app.comparator');
        $datasets = $comparator->getDatasets($ids, $type);

        $data = [
            'datasets' => $datasets,
        ];

        $response = new JsonResponse($data);
        return $response;
    }
}
