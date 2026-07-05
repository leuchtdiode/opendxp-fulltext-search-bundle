<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Try2catch\OpenDxp\FulltextSearchBundle\DependencyInjection\OpenDxpFulltextSearchExtension;

class OpenDxpFulltextSearchBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new OpenDxpFulltextSearchExtension();
    }
}
