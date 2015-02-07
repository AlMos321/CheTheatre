<?php

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\NoRoute;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @RouteResource("Employee")
 */
class EmployeesController extends Controller
{
    /**
     * @ApiDoc(
     * resource=true,
     *  description="Returns a collection of Employees",
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the entity is not found",
     *           "Returned when something else is not found"
     *         }
     *     },
     * output = { "class" = "AppBundle\Entity\Employee", "collection" = true, "collectionName" = "Employees" }
     * )
     *
     *
     * Collection get action
     * @return Response
     *
     * @RestView
     */
    public function cgetAction()
    {
        $em = $this->getDoctrine()->getManager();

        $employees = $em->getRepository('AppBundle:Employee')->findAll();

        $restView = View::create();
        $restView
            ->setData($employees)
            ->setHeaders(array(
                "Content-Type" => "application/json",
                "Location" => $this->generateUrl('get_employees')
                )
            )
        ;
        return $restView;
    }

    /**
     * @ApiDoc(
     * resource=true,
     *  description="Returns an Employee by slug",
     *
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the entity is not found",
     *           "Returned when something else is not found"
     *         }
     *     },
     *  parameters={
     *      {"name"="Slug", "dataType"="string", "required"=true, "description"="Employee slug"}
     *  },
     * output = { "class" = "AppBundle\Entity\Employee" }
     * )
     *
     * @return Response
     *
     * @RestView
     */
    public function getAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $employee = $em->getRepository('AppBundle:Employee')->findOneByslug($slug);

        if (!$employee) {
            throw $this->createNotFoundException('Unable to find '.$slug.' entity');
        }

        $restView = View::create();
        $restView
            ->setData($employee)
            ->setHeaders(array(
                "Content-Type" => "application/json",
                "Location" => $this->generateUrl('get_employees').'/'.$employee->getSlug()
                )
            )
        ;
        return $restView;
    }

    /**
     * @ApiDoc(
     * resource=true,
     *  description="Returns an Employee by slug and his roles",
     *
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the entity is not found",
     *           "Returned when something else is not found"
     *         }
     *     },
     *  parameters={
     *      {"name"="Slug", "dataType"="string", "required"=true, "description"="Employee slug"}
     *  },
     * output = { "class" = "AppBundle\Entity\Role", "collection" = true, "collectionName" = "Roles" }
     * )
     *
     * @return Response
     *
     * @RestView
     */
    public function getRolesAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $employee = $em->getRepository('AppBundle:Employee')->findOneByslug($slug);

        if (!$employee) {
            throw $this->createNotFoundException('Unable to find '.$slug.' entity');
        }

        $roles = $employee->getRoles();

        $restView = View::create();
        $restView
            ->setData($roles)
            ->setHeaders(array(
                "Content-Type" => "application/json",
                "Location" => $this->generateUrl('get_employees').'/'.$employee->getSlug().'/roles'
                )
            )
        ;
        return $restView;
    }
}
