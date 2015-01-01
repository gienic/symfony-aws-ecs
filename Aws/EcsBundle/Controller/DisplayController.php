<?php

namespace Aws\EcsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DisplayController extends Controller
{
    public function indexAction($type)
    {
        return $this->render('AwsEcsBundle:Display:index.html.twig', array('type' => $type));
    }
}
