<?php


namespace Webapp\Controller;


use Domain\Repository\SegmentRepository;
use Domain\Services\Factory\SegmentFactory;
use Domain\Entity\Segment;
use Domain\Services\SegmentService;
use Form\SegmentType;
use Symfony\Component\HttpFoundation\Request;

class SegmentController extends WebappController
{
    public function show(Segment $segment)
    {
        return $this->viewRender(self::TWIG_PATH_WEBAPP . 'segment.html.twig', compact('segment'));
    }

    public function create(Request $request, SegmentFactory $segmentFactory, SegmentService $segmentService, SegmentRepository $segmentRepository)
    {
        $segment = $segmentFactory->create();

        $form = $this->createForm(SegmentType::class, $segment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if(null === $segmentRepository->findOneBy([
                'uidentifier' => $segment->getUidentifier()
                ])) {
                $segment->updateValueSegment();

                $segmentService->save($segment);

                return $this->redirectToRoute(
                    'segment_id',
                    [
                        'id' => $segment->getId(),
                    ]
                );
            } else {
                $this->addFlash(
                    'error',
                    'El identificador de segmento ya existe!!!....'
                );

                return $this->redirectToRoute('create_segment');
            }

        }

        return $this->viewRender(
            self::TWIG_PATH_WEBAPP . 'create_segment.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function delete(Segment $segment, SegmentService $segmentService)
    {
        $segmentService->remove($segment);

        return $this->redirectToRoute('home');
    }

    public function edit(Request $request, Segment $segment, SegmentService $segmentService, SegmentRepository $segmentRepository)
    {
        $form = $this->createForm(SegmentType::class, $segment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if(null === $segmentRepository->findOneBy([
                    'uidentifier' => $segment->getUidentifier()
                ])) {
                $segment->updateValueSegment();
                $segmentService->save($segment);

                return $this->redirectToRoute(
                    'segment_id',
                    [
                        'id' => $segment->getId(),
                    ]
                );
            } else {
                $this->addFlash(
                    'error',
                    'El identificador de segmento ya existe!!!....'
                );

                return $this->redirectToRoute('segment_edit_id',[
                    'id' => $segment->getId(),
                ]);
            }

        }

        return $this->viewRender(
            self::TWIG_PATH_WEBAPP . 'edit_segment.html.twig',
            [
                'form' => $form->createView(),
                'segment' => $segment,
            ]
        );


    }
}
