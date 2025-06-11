<?php

namespace Iconic\Twig;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\MarkdownConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownFilter extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown_to_html', [$this, 'convert'], ['is_safe' => ['html']]),
        ];
    }

    public function convert(string $markdown, array $theme = []): string
    {
        $environment = new Environment([]);
        $environment->addExtension(new CommonMarkCoreExtension());

        $renderer = new LinkRenderer($theme);
        $environment->addRenderer(Link::class, $renderer);

        $converter = new MarkdownConverter($environment);

        return (string) $converter->convert($markdown);
    }
}