<?php
namespace Iconic\Twig;

use Iconic\Photography\Color;
use League\CommonMark\Node\Node;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class LinkRenderer implements NodeRendererInterface
{
    private array $colors;

    public function __construct(array $colors = [])
    {
        $this->colors = $colors;
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (!($node instanceof Link)) {
            throw new \InvalidArgumentException('Expected a Link node');
        }

        $text = $node->firstChild()->getLiteral(); // e.g. "primary:Confirm Email"
        $role = 'text'; // default role
        $label = $text;

        if (str_contains($text, ':')) {
            [$role, $label] = explode(':', $text, 2);
            $role = trim(strtolower($role));
            $label = trim($label);
            $node->firstChild()->setLiteral($label);
        }

        $style = match ($role) {
            'primary' => sprintf(
                'display:inline-block;background:%s;color:%s;padding:10px 20px;border-radius:4px;text-decoration:none;border: 1px solid %s;',
                $this->colors['primary'] ?? '#007BFF',
                $this->colors['text'] ?? '#222',
                Color::brightness($this->colors['primary'], -0.2),
            ),
            'secondary' => sprintf(
                'display:inline-block;background:%s;color:%s;padding:10px 20px;border-radius:4px;text-decoration:none;border: 1px solid %s;',
                $this->colors['secondary'] ?? '#007BFF',
                $this->colors['white'] ?? '#fff',
                Color::brightness($this->colors['primary'], -0.2),
            ),
            'plain' => sprintf(
                'color:%s;text-decoration:none;',
                $this->colors['text'] ?? '#222'
            ),
            default => sprintf('color:%s;', $this->colors['text'] ?? '#333'),
        };

        return new HtmlElement('a', [
            'href' => $node->getUrl(),
            'style' => $style,
        ], $childRenderer->renderNodes($node->children()));
    }
}