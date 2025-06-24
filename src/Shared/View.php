<?php

declare(strict_types=1);

namespace App\Shared;

use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

/**
 * View
 *
 * @package App\Shared;
 */
class View
{
    protected string $viewsPath;
    protected string $layoutsPath;

    public function __construct(string $viewsPath, string $layoutsPath)
    {
        $this->viewsPath = rtrim($viewsPath, '/');
        $this->layoutsPath = rtrim($layoutsPath, '/');
    }

    public function render(string $view, array $data = [], string $layout = 'main'): ResponseInterface
    {
        $viewFile = "{$this->viewsPath}/pages/{$view}.html";
        $layoutFile = "{$this->layoutsPath}/{$layout}.html";

        if (!file_exists($viewFile)) {
            throw new FileNotFoundException("View not found: $viewFile");
        }

        if (!file_exists($layoutFile)) {
            throw new FileNotFoundException("Layout not found: $layoutFile");
        }

        $viewTemplate = file_get_contents($viewFile);
        $viewRendered = $this->replacePlaceholders($viewTemplate, $data);

        $layoutTemplate = file_get_contents($layoutFile);
        $data['content'] = $viewRendered;
        $fullHtml = $this->replacePlaceholders($layoutTemplate, $data);

        return new Response(200, ['Content-Type' => 'text/html'], $fullHtml);
    }

    private function replacePlaceholders(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', (string)$value, $template);
        }
        return $template;
    }
}
