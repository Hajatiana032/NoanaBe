<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
class DeleteModal
{
    public string $id;
    public string $title;
    public string $message;
    public string $path;
}
