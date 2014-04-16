<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Dashboard controller.
 */
class DashboardController extends AbstractController
{
    /**
     * Browse all Activity entities.
     *
     * @Get("/.{_format}", name="dashboard_browse", defaults={"_format"="~"})
     */
    public function browseAction(Request $request)
    {
        $responseData = array();
        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }
}
