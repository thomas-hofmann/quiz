<?php

namespace App\Twig;

use App\Entity\Quiz;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageUrlExtension extends AbstractExtension
{
    public function __construct(
        private readonly Packages $packages,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('versioned_image_url', [$this, 'versionedImageUrl']),
        ];
    }

    public function versionedImageUrl(?string $image, ?Quiz $quiz = null): string
    {
        if ($image === null || trim($image) === '') {
            return '';
        }

        $image = trim($image);
        $url = str_starts_with($image, 'http') ? $image : $this->packages->getUrl($image);
        $version = $quiz?->getUpdatedAt()?->getTimestamp();

        if ($version === null) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . 'v=' . $version;
    }
}
