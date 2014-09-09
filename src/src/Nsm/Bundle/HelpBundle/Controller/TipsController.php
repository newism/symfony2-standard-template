<?php

namespace Nsm\Bundle\HelpBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Hateoas\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\HelpBundle\Entity\Tip;
use Nsm\Bundle\HelpBundle\Entity\TipRepository;
use Nsm\Bundle\HelpBundle\Form\Type\TipFilterType;
use Nsm\Bundle\HelpBundle\Form\Type\TipType;
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tips controller.
 */
class TipsController extends AbstractController
{
    protected $servicePrefix = "nsm_help";
    protected $bundleName = "NsmHelpBundle";
    protected $entityDiscriminator = "Tip";
    protected $templateGroup = "Tips";

    /**
     * Browse all Tip entities.
     *
     * @Get("/tips.{_format}", name="tip_browse", defaults={"_format"="~"})
     *
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Tip count limit")
     * @ApiDoc(
     *  resource=true,
     *  filters={
     *      {"name"="title", "dataType"="string"},
     *      {"name"="orderBy", "dataType"="string", "pattern"="(title|createdAt) ASC|DESC"}
     *  })
     */
    public function browseAction(Request $request, $page, $perPage)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var TipRepository $repo */
        $repo = $em->getRepository('NsmHelpBundle:Tip');

        /** @var Form $form */
        $tipSearchForm = $this->createForm(
            new TipFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('tip_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $tipSearchForm->handleRequest($request);
        $criteria = $repo->sanatiseCriteria($tipSearchForm->getData());

        $qb = $repo->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);
        $results = $pager->getCurrentPageResults();
        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $tipSearchForm->createView();
        } else {

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('tip_browse', array())
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Finds and displays a Tip entity.
     *
     * @Get("/tips/{id}.{_format}", name="tip_read", requirements={"id" = "\d+"}, defaults={"_format"="~"})
     *
     * @View(templateVar="entity", serializerGroups={"tip_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\HelpBundle\Entity\Tip"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findOr404('Tip', $id);
        $entity->getTasks();

        return $entity;
    }

    /**
     * Edits an existing Tip entity.
     *
     * @Patch("/tips/{id}", name="tip_patch")
     * @Get("/tips/{id}/edit", name="tip_edit")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\HelpBundle\Form\Type\TipType",
     *  output="Nsm\Bundle\HelpBundle\Entity\Tip"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findOr404('Tip', $id);

        /** @var Form $form */
        $form = $this->createForm(
            new TipType(),
            $entity,
            array(
                'action' => $this->generateUrl('tip_patch', array('id' => $entity->getId())),
                'method' => 'PATCH'
            )
        )->add('Update', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl(
                    'tip_read',
                    array(
                        'id' => $entity->getId()
                    )
                )
            );
        }

        $responseData = array(
            'entity' => $entity,
            'form' => $form
        );

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Creates a add Tip entity.
     *
     * @Post("/tips", name="tip_post")
     * @Get("/tips/add", name="tip_add")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\HelpBundle\Form\Type\TipType",
     *  output="Nsm\Bundle\HelpBundle\Entity\Tip"
     * )
     */
    public function addAction(Request $request)
    {
        $entity = new Tip();
        $entity->setRoute($request->query->get('route'));

        /** @var Form $form */
        $form = $this->createForm(
            new TipType(),
            $entity,
            array(
                'action' => $this->generateUrl('tip_post'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('tip_read', array('id' => $entity->getId())),
                Codes::HTTP_CREATED
            );
        }

        $responseData = array(
            'entity' => $entity,
            'form' => $form,
        );

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'add')));

        return $view;

    }

    /**
     * Destroys a Tip entity.
     *
     * @Delete("/tips/{id}", name="tip_delete")
     * @Get("/tips/{id}/destroy", name="tip_destroy")
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findOr404('Tip', $id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('tip_destroy', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('tip_browse', array()), Codes::HTTP_OK);
            } else {
                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form
        );

    }
}
