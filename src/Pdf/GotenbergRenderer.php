<?php

namespace BestWishes\Pdf;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Environment;

final class GotenbergRenderer
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntrypointLookupInterface $entrypoints,
        private readonly Filesystem $filesystem,
        private readonly Environment $twig,
        #[Autowire('%kernel.project_dir%/public')]
        private readonly string $publicDirectory,
        #[Autowire('%env(GOTENBERG_URL)%')]
        private readonly string $gotenbergUrl,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function render(string $template, array $context): string
    {
        $files = [];
        $stylesheets = [];
        foreach ($this->entrypoints->getCssFiles('app') as $path) {
            $filename = basename($path);
            $css = $this->filesystem->readFile($this->publicDirectory . $path);
            // Gotenberg flattens multipart uploads into the same directory as index.html.
            $css = preg_replace_callback(
                '~url\((["\']?)(/build/(?:[\w-]+/)*([\w.-]+\.[\w-]+))\1\)~',
                function (array $matches) use (&$files): string {
                    [, , $path, $filename] = $matches;
                    $files[$filename] ??= DataPart::fromPath($this->publicDirectory . $path, $filename);

                    return 'url("' . $filename . '")';
                },
                $css
            ) ?? throw new \RuntimeException('Could not prepare the PDF stylesheet.');
            $files[$filename] = new DataPart($css, $filename, 'text/css');
            $stylesheets[] = $filename;
        }

        $context['pdfStylesheets'] = $stylesheets;
        $html = $this->twig->render($template, $context);
        $fields = [
            ['files' => new DataPart($html, 'index.html', 'text/html')],
            'emulatedMediaType' => 'screen',
            'printBackground' => 'true',
            'paperWidth' => '210mm',
            'paperHeight' => '297mm',
            'failOnResourceLoadingFailed' => 'true',
        ];
        foreach ($files as $file) {
            $fields[] = ['files' => $file];
        }

        $form = new FormDataPart($fields);
        try {
            $pdf = $this->httpClient->request(
                'POST',
                rtrim($this->gotenbergUrl, '/') . '/forms/chromium/convert/html',
                [
                    'headers' => $form->getPreparedHeaders()->toArray(),
                    'body' => $form->bodyToIterable(),
                    'max_duration' => 35,
                    'max_redirects' => 0,
                ],
            )->getContent();
        } catch (ExceptionInterface $exception) {
            throw new HttpException(Response::HTTP_BAD_GATEWAY, 'PDF generation failed.', $exception);
        }

        if (!str_starts_with($pdf, '%PDF-')) {
            throw new HttpException(Response::HTTP_BAD_GATEWAY, 'Gotenberg returned an invalid PDF.');
        }

        return $pdf;
    }
}
