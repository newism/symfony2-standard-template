<?php

namespace Nsm\Bundle\TestOutputBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $data = file_get_contents(
            $this->container->getParameter('kernel.root_dir') . "/../features/_output/json/output.json"
        );

        $data = json_decode($data, true);

        return $this->render('NsmTestOutputBundle:Default:index.html.twig', array('data' => $data));
    }
}
