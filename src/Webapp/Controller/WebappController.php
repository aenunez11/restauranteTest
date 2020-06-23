<?php

namespace Webapp\Controller;

use Domain\Entity\Segment;
use Domain\Repository\SegmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebappController extends AbstractController
{
    /**
     * TEMPLATES (Twig) PATHS
     */
    const TWIG_PATH_WEBAPP = 'webapp' . '/';
    const TWIG_PATH_WEBAPP_COMMON = self::TWIG_PATH_WEBAPP . 'common' . '/';


    /**
     * Array to store and send variables to the View
     * @var array
     */
    protected $viewVariables = array();

    /**
     * @var Request
     */
    protected $request;


    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        // Creating routing paths of twigs and sent it to view
        $this->generateTwigPaths();
    }

    /**
     * Generate most used Paths for TWIG
     */
    private function generateTwigPaths()
    {
        $viewPaths = array(
            'webApp' => self::TWIG_PATH_WEBAPP,
            'webAppCommon' => self::TWIG_PATH_WEBAPP_COMMON,
        );
        $this->addViewVariable('viewPaths', $viewPaths);
    }

    /* ******************************* BASE METHODS ********************************** */

    /**
     * Add value in View Variables array indexed by $index
     *
     * @param $index
     * @param $value
     */
    protected function addViewVariable($index, $value)
    {
        $this->viewVariables[$index] = $value;
    }

    /**
     * Remove variable indexed by $index of View Variables
     * @param $index
     */
    protected function deleteViewVariable($index)
    {
        unset($this->viewVariables[$index]);
    }

    /**
     * Custom method to render view
     *
     * @param $viewPath
     * @param $additionalData array
     * @return Response
     */
    protected function viewRender($viewPath, $additionalData = [])
    {
        /* Add format (html, twig) if necessary */
        if (preg_match('/html\.twig$/', trim($viewPath)) == 0) {
            $viewPath = trim($viewPath) . '.html.twig';
        }
        /* Generate CurrentPath */
        $arrayPath = explode('/', $viewPath);
        if (count($arrayPath) > 1) {
            $currentPath = implode('/', array_slice($arrayPath, 0, -1));
            $this->addViewVariable('CurrentPath', $currentPath);
        }
        $dataArray = array_merge($this->viewVariables, $additionalData);

        /* @var $viewPath string */
        return $this->render(
            $viewPath,
            $dataArray
        );
    }

    /* ********************** ROUTE CALLABLE METHODS **************************** */

    public function home(SegmentRepository $repository)
    {
        $segments = $repository->findAll();

        return $this->viewRender(self::TWIG_PATH_WEBAPP . 'webapp_base.html.twig', compact('segments'));

    }

}
