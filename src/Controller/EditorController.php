<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoFormType;
use Doctrine\Persistence\ManagerRegistry;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditorController extends AbstractController
{
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    #[Route('/editor/test', name: 'test')]
    public function test()
    {
        return $this->render('editor/test_editor.html.twig');
    }

    #[Route('/watch/{video}/player', name: 'watch.player')]
    public function watchPlayer($video, ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Video::class);
        /**
         * @var $currentVideo Video
         */
        $currentVideo = $repository->findOneBy([
            'name' => $video
        ]);

//        $refVideos = $repository->findBy([
//            'previous_video_id' => $currentVideo->getId()
//        ]);
//        $variants = [];
//        foreach ($refVideos as $refVideo) {
//            $variants[$refVideo->getName()] = $refVideo->getName();
//        }
        return $this->render('watch/video_new.html.twig', [
            'video' => $currentVideo,
//            'variants' => $variants
        ]);
    }

    #[Route('/watch/list', name: 'watch.list')]
    public function watchList(ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Video::class);
        $videos = $repository->findBy([
            'previous_video_id' => null
        ]);
        return $this->render('watch/video_list.html.twig', [
            'videos' => $videos
        ]);
    }

    #[Route('/watch/{video}/variants', name: 'watch.variants')]
    public function watchVariants($video, ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Video::class);
        /**
         * @var $currentVideo Video
         */
        $currentVideo = $repository->findOneBy([
            'name' => $video
        ]);
        $refVideos = $repository->findBy([
            'previous_video_id' => $currentVideo->getId()
        ]);
        $variants = [];
        foreach ($refVideos as $refVideo) {
            $variants[] = [
                'name' => $refVideo->getName(),
                'file' => $refVideo->getFile()
            ];
        }
        return new Response(json_encode([
            'variants' => $variants,
        ]), 200);
    }

    #[Route('editor/videos/list', name: 'video_list')]
    public function list(ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Video::class);
        $videos = $repository->findBy([
            'previous_video_id' => null
        ]);
        return $this->render('editor/video_list.html.twig', [
            'videos' => $videos
        ]);
    }

    #[Route('/editor/{video}/edit', name: 'app_editor')]
    public function index($video, ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Video::class);
        $generalVideo = $repository->findOneBy([
           'name' => $video
        ]);
        $previousVideo = $repository->findOneBy([
            'id' => $generalVideo->getPreviousVideoId()
        ]);
        $nextVideos = $repository->findBy([
            'previous_video_id' => $generalVideo->getId()
        ]);
        return $this->render('editor/index.html.twig', [
            'video_name' => $generalVideo->getName(),
            'video_file' => $generalVideo->getFile(),
            'refs' => [
                'next_videos' => $nextVideos,
                'previous_video' => $previousVideo
            ]
        ]);
    }

    #[Route('/editor/add', methods: ['GET', 'POST'], name: 'video_add')]
    public function add(Request $request, ManagerRegistry $doctrine)
    {
        $video = new Video();
        $form = $this->createForm(VideoFormType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            /**
             * @var $name string
             */
            $name = $form->get('name')->getData();
            $video = new Video();
            $video->setName($name);
            /**
             * @var $file UploadedFile
             */
            $file = $form->get('file')->getData();
            $newFilename = $name . '.mp4';
            $video->setFile($name);
            $path = $this->parameterBag->get('kernel.project_dir');
            $videoPath = $path . '/public/videos/';
            $file->move($videoPath, $newFilename);

            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('app_editor', ['video' => $name]);
        }
        return $this->renderForm('editor/new.html.twig', ['form' => $form]);
    }

    #[Route('/video/edit', methods: ['POST'], name: 'video_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine)
    {
        $currentVideoName = $request->request->get('current_video');
        $stopSecond = $request->request->get('time');
        $variants = $request->request->all('video');

        $entityManager = $doctrine->getManagerForClass(Video::class);

        $repository = $doctrine->getRepository(Video::class);

        /**
         * @var $currentVideo Video
         */
        $currentVideoDb = $repository->findOneBy([
            'name' => $currentVideoName
        ]);

        $path = $this->parameterBag->get('kernel.project_dir');

        foreach ($variants as $variantKey => $variant) {
            $variant = $variant['variant'];
            $file = $request->files->get('video')[$variantKey]['file'];
            $newFilename = $variant . '.mp4';

            $videoPath = $path . '/public/videos/';
            $file->move($videoPath, $newFilename);

            $newVideo = new Video();
            $newVideo->setName($variant);
            $newVideo->setFile($variant);
            $newVideo->setPreviousVideoId($currentVideoDb->getId());

            $entityManager->persist($newVideo);
            $entityManager->flush();
        }

        $newCurrentVideoName = $currentVideoName . '_' . uniqid();
        $currentVideoPath = $path . '/public/videos/' . $currentVideoName . '.mp4';
        $ffmpeg = FFMpeg::create();
        $currentVideo = $ffmpeg->open($currentVideoPath);
        $currentVideo
            ->clip(TimeCode::fromSeconds(0), TimeCode::fromSeconds($stopSecond))
            ->save(new X264(), 'videos/' . $newCurrentVideoName . '.mp4');

        $currentVideoDb->setFile($newCurrentVideoName);
        $entityManager->persist($currentVideoDb);
        $entityManager->flush();

        $currentCases = $repository->findBy([
            'previous_video_id' => $currentVideoDb->getId()
        ]);
        $formattedCurrentCases = [];
        foreach ($currentCases as $currentCase) {
            $formattedCurrentCases[$currentCase->getName()] = $currentCase->getName();
        }

        return new Response(json_encode([
            'current_video_file' => $newCurrentVideoName,
            'current_cases' => $formattedCurrentCases
        ]), 200);
    }
}