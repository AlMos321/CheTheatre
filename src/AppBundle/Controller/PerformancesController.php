<?php

namespace AppBundle\Controller;

use AppBundle\Model\Link;
use AppBundle\Model\PaginationLinks;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Model\PerformancesResponse;

/**
 * @RouteResource("Performance")
 */
class PerformancesController extends Controller
{
    /**
     * @ApiDoc(
     * resource=true,
     *  description="Returns a collection of Performances",
     *  statusCodes={
     *      200="Returned when all parameters were correct",
     *      404="Returned when the entity is not found",
     *  },
     *  output = "array<AppBundle\Model\PerformancesResponse>"
     * )
     *
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Count entries at one page")
     * @QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     *
     * @RestView
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $performances = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Performance')
            ->findBy([], null, $paramFetcher->get('limit'), ($paramFetcher->get('page')-1) * $paramFetcher->get('limit'));

        $performancesResponse = new PerformancesResponse();
        $performancesResponse->setPerformances($performances);
        $performancesResponse->setTotalCount($this->getDoctrine()->getManager()->getRepository('AppBundle:Performance')->getCount());
        $performancesResponse->setPageCount(ceil($performancesResponse->getTotalCount() / $paramFetcher->get('limit')));
        $performancesResponse->setPage($paramFetcher->get('page'));

        $self = $this->generateUrl('get_performances', [
            'limit' => $paramFetcher->get('limit'),
            'page' => $paramFetcher->get('page'),
        ], true
        );

        $first = $this->generateUrl('get_performances', [], true);

        $nextPage = $paramFetcher->get('page') < $performancesResponse->getPageCount() ?
            $this->generateUrl('get_performances', [
                'limit' => $paramFetcher->get('limit'),
                'page' => $paramFetcher->get('page')+1,
            ], true
            ) :
            'false';

        $previsiousPage = $paramFetcher->get('page') > 1 ?
            $this->generateUrl('get_performances', [
                'limit' => $paramFetcher->get('limit'),
                'page' => $paramFetcher->get('page')-1,
            ], true
            ) :
            'false';

        $last = $this->generateUrl('get_performances', [
            'limit' => $paramFetcher->get('limit'),
            'page' => $performancesResponse->getPageCount(),
        ], true
        );

        $links = new PaginationLinks();

        $performancesResponse->setLinks($links->setSelf(new Link($self)));
        $performancesResponse->setLinks($links->setFirst(new Link($first)));
        $performancesResponse->setLinks($links->setNext(new Link($nextPage)));
        $performancesResponse->setLinks($links->setPrev(new Link($previsiousPage)));
        $performancesResponse->setLinks($links->setLast(new Link($last)));

        foreach ($performances as $performance) {
            $performance->setLinks([
                ['rel' => 'self', 'href' => $this->generateUrl('get_performance', ['slug' => $performance->getSlug()], true)],
                ['rel' => 'self.roles', 'href' => $this->generateUrl('get_performance_roles', ['slug' => $performance->getSlug()], true)],
                ['rel' => 'self.events', 'href' => $this->generateUrl('get_performanceevents', ['performance' => $performance->getSlug()], true)],
            ]);
        }

        return $performancesResponse;
    }

    /**
     * @ApiDoc(
     * resource=true,
     *  description="Returns Performance by unique property {slug}",
     *  statusCodes={
     *      200="Returned when Performance was found in database",
     *      404="Returned when Performance was not found in database",
     *  },
     *  parameters={
     *      {"name"="slug", "dataType"="string", "required"=true, "description"="Performance by unique name"}
     *  },
     *  output = "AppBundle\Entity\Performance"
     * )
     *
     * @RestView
     */
    public function getAction($slug)
    {
        $performance = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Performance')->findOneByslug($slug);

        if (!$performance) {
            throw $this->createNotFoundException('Unable to find '.$slug.' entity');
        }

        return $performance;
    }

    /**
     * @ApiDoc(
     * resource=true,
     *  description="Returns Performance roles by his unique {slug}",
     *  statusCodes={
     *      200="Returned when Performance by slug was found in database",
     *      404="Returned when Performance by slug was not found in database",
     *  },
     *  parameters={
     *      {"name"="slug", "dataType"="string", "required"=true, "description"="Performance unique name"}
     *  },
     *  output = "array<AppBundle\Entity\Role>"
     * )
     *
     * @RestView
     */
    public function getRolesAction($slug)
    {
        $performance = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Performance')->findOneByslug($slug);

        if (!$performance) {
            throw $this->createNotFoundException('Unable to find '.$slug.' entity');
        }

        $roles = $performance->getRoles();

        return $roles;
    }

    /**
     * @ApiDoc(
     * resource=true,
     *  description="Returns Performance events by Performance {slug}",
     *  statusCodes={
     *      200="Returned when Performance by {slug} was found in database",
     *      404="Returned when Performance by {slug} was not found in database",
     *  },
     *  parameters={
     *      {"name"="slug", "dataType"="string", "required"=true, "description"="Performance unique name"}
     *  },
     *  output = "array<AppBundle\Entity\PerformanceEvent>",
     * deprecated = true
     * )
     *
     * @RestView
     */
    public function getPerformanceeventsAction($slug)
    {
        $performance = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Performance')->findOneByslug($slug);

        if (!$performance) {
            throw $this->createNotFoundException('Unable to find '.$slug.' entity');
        }

        $performanceEvents = $performance->getPerformanceEvents();

        return $performanceEvents;
    }
}
