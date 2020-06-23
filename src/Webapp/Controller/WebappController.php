<?php

namespace Webapp\Controller;

use Domain\Entity\Segment;
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

    public function home()
    {
        $segments = $this->getDoctrine()
            ->getRepository(Segment::class)
            ->findAll();

        return $this->viewRender(self::TWIG_PATH_WEBAPP . 'webapp_base.html.twig', compact('segments'));

    }

    public function showSegment(Segment $segment)
    {
        return $this->viewRender(self::TWIG_PATH_WEBAPP . 'segment.html.twig', compact('segment'));

    }

    public function createSegment(Request $request)
    {

        $segment = new Segment();
        $segment->getCreatedAt(new \DateTime('now'));

        $form = $this->createFormBuilder($segment)
                ->add('name',TextType::class)
                ->add('uidentifier',TextType::class)
                ->add('createdAt',DateType::class)
                ->add('save', SubmitType::class, ['label' => "save Segment"])
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_segment = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($new_segment);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->viewRender(self::TWIG_PATH_WEBAPP . 'create_segment.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deleteSegment(Segment $segment) {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($segment);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }

    public function editSegment(Request $request,Segment $segment){

        $form = $this->createFormBuilder($segment)
            ->add('name',TextType::class)
            ->add('uidentifier',TextType::class)
            ->add('update', SubmitType::class, ['label' => "edit Segment"])
            ->getForm();

        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {

            $segment->setName($form->get('name')->getData());
            $segment->setUidentifier($form->get('uidentifier')->getData());
            $entityManager->flush();

            return $this->redirectToRoute('segment_id',[
                'id' => $segment->getId()
            ]);
        }

        return $this->viewRender(self::TWIG_PATH_WEBAPP . 'edit_segment.html.twig', [
            'form' => $form->createView(),
            'segment' => $segment
        ]);


    }
}
