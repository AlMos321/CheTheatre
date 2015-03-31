<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Performance;
use AppBundle\Entity\Post;
use AppBundle\Entity\Employee;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Sonata\MediaBundle\Controller\Api\MediaController;
use Symfony\Component\Routing\Router;

class SerializerSubscriber implements EventSubscriberInterface
{
    /** @var MediaController  */
    protected $mediaController;

    /** @var Router */
    protected $router;

    public function __construct(MediaController $mediaController, Router $router)
    {
        $this->mediaController = $mediaController;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.pre_serialize', 'class' => 'AppBundle\Entity\Employee', 'method' => 'onPreEmployeeSerialize'],
            ['event' => 'serializer.pre_serialize', 'class' => 'AppBundle\Entity\PerformanceEvent', 'method' => 'onPrePerformanceEventSerialize'],
            ['event' => 'serializer.pre_serialize', 'class' => 'AppBundle\Entity\Performance', 'method' => 'onPrePerformanceSerialize'],
            ['event' => 'serializer.pre_serialize', 'class' => 'AppBundle\Entity\Post', 'method' => 'onPrePostSerialize'],
        ];
    }

    public function onPreEmployeeSerialize(ObjectEvent $event)
    {
        /** @var Employee $employee */
        $employee = $event->getObject();

        if (!$avatar = $event->getObject()->getAvatar()) {
            return;
        }

        $avatarLinks = $this->mediaController->getMediumFormatsAction($avatar->getId());
        $event->getObject()->avatarThumbnails = $avatarLinks;

        if ($employee->getGalleryHasMedia()->getValues()) {
            foreach ($employee->getGalleryHasMedia()->getValues() as $gallery) {
                $galleryHasMediaLinks[] = $this->mediaController->getMediumFormatsAction($gallery->getMedia());
                $employee->galleryHasMediaThumbnails = $galleryHasMediaLinks;
            }
        }
    }

    public function onPrePerformanceEventSerialize(ObjectEvent $event)
    {
    }

    public function onPrePerformanceSerialize(ObjectEvent $event)
    {
        /** @var Performance $performance */
        $performance = $event->getObject();

        if ($performance->getMainPicture()) {
            $mainImageLinks = $this->mediaController->getMediumFormatsAction($performance->getMainPicture());
            $performance->mainPictureThumbnails = $mainImageLinks;
        }

        if ($performance->getSliderImage()) {
            $sliderImageLinks = $this->mediaController->getMediumFormatsAction($performance->getSliderImage());
            $performance->sliderImageThumbnails = $sliderImageLinks;
        }

        if ($performance ->getGalleryHasMedia()->getValues()) {
            foreach ($performance ->getGalleryHasMedia()->getValues() as $gallery) {
                $galleryHasMediaLinks[] = $this->mediaController->getMediumFormatsAction($gallery->getMedia());
                $performance ->galleryHasMediaThumbnails = $galleryHasMediaLinks;
            }
        }
    }

    public function onPrePostSerialize(ObjectEvent $event)
    {
        /** @var Post $post */
        $post = $event->getObject();

        if ($post->getMainPicture()) {
            $mainImageLinks = $this->mediaController->getMediumFormatsAction($post->getMainPicture());
            $post->mainPictureThumbnails = $mainImageLinks;
        }
    }
}
