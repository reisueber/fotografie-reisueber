<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class KlaroSettingsExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('klaro_settings', [$this, 'processKlaroSettings'], ['is_safe' => ['html']]),
        ];
    }

    public function processKlaroSettings(string $content): string
    {
        $button = '<button href="#" class="cm-link cn-learn-more bg-primary text-white px-6 py-2 rounded-md hover:bg-primary-dark transition-colors" id="open-klaro-settings">Cookie Einstellungen öffnen</button>';
        
        return str_replace('#klaro-settings#', $button, $content);
    }
}
