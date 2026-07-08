<?php

namespace App\Concerns;

use DOMDocument;
use DOMElement;
use DOMNode;

trait SanitizesHtml
{
    /**
     * Tag yang diizinkan, sesuai keluaran editor Quill di form berita.
     *
     * @var list<string>
     */
    private static array $allowedTags = [
        'p', 'br', 'h2', 'h3', 'h4', 'h5', 'strong', 'b', 'em', 'i', 'u', 's',
        'a', 'img', 'ol', 'ul', 'li', 'blockquote', 'pre', 'code', 'span',
    ];

    /**
     * Tag berbahaya yang dibuang beserta seluruh isinya.
     *
     * @var list<string>
     */
    private static array $droppedTags = [
        'script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'link', 'meta', 'base',
    ];

    /**
     * Atribut yang diizinkan per tag (di luar 'class' dan 'style' yang divalidasi terpisah).
     *
     * @var array<string, list<string>>
     */
    private static array $allowedAttributes = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height'],
    ];

    /**
     * Bersihkan HTML dari editor: buang tag/atribut di luar whitelist,
     * skema URL berbahaya, dan event handler.
     */
    private function sanitizeHtml(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $dom->loadHTML(
            '<?xml encoding="UTF-8"><div id="sanitize-root">'.$html.'</div>',
            LIBXML_NOERROR | LIBXML_NOWARNING
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $dom->getElementById('sanitize-root');

        if (! $root) {
            return '';
        }

        $this->sanitizeChildren($root);

        $clean = '';

        foreach ($root->childNodes as $child) {
            $clean .= $dom->saveHTML($child);
        }

        return $clean;
    }

    private function sanitizeChildren(DOMNode $node): void
    {
        // Iterasi mundur karena daftar anak berubah saat node dihapus/di-unwrap.
        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
            $child = $node->childNodes->item($i);

            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::$droppedTags, true)) {
                $node->removeChild($child);

                continue;
            }

            $this->sanitizeChildren($child);

            if (! in_array($tag, self::$allowedTags, true)) {
                // Unwrap: pertahankan isi, buang tag-nya.
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);

                continue;
            }

            $this->sanitizeAttributes($child, $tag);
        }
    }

    private function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowed = self::$allowedAttributes[$tag] ?? [];

        for ($i = $element->attributes->length - 1; $i >= 0; $i--) {
            $attribute = $element->attributes->item($i);
            $name = strtolower($attribute->nodeName);
            $value = $attribute->nodeValue ?? '';

            if ($name === 'class' || $name === 'style') {
                $filtered = $name === 'class' ? $this->filterClasses($value) : $this->filterStyles($value);

                $filtered === ''
                    ? $element->removeAttribute($attribute->nodeName)
                    : $element->setAttribute($name, $filtered);

                continue;
            }

            $isUnsafeUrl = in_array($name, ['href', 'src'], true) && ! $this->isSafeUrl($value);

            if (! in_array($name, $allowed, true) || $isUnsafeUrl) {
                $element->removeAttribute($attribute->nodeName);
            }
        }

        if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    /**
     * Hanya izinkan class bawaan Quill (ql-*).
     */
    private function filterClasses(string $value): string
    {
        $classes = array_filter(
            preg_split('/\s+/', trim($value)) ?: [],
            fn (string $class): bool => preg_match('/^ql-[\w-]+$/', $class) === 1
        );

        return implode(' ', $classes);
    }

    /**
     * Hanya izinkan deklarasi warna dari toolbar Quill (color, background-color).
     */
    private function filterStyles(string $value): string
    {
        $safe = [];

        foreach (explode(';', $value) as $declaration) {
            if (preg_match(
                '/^\s*(color|background-color)\s*:\s*(#[0-9a-fA-F]{3,8}|rgba?\([\d\s.,%]+\)|[a-zA-Z]+)\s*$/',
                $declaration,
                $matches
            )) {
                $safe[] = strtolower($matches[1]).': '.$matches[2];
            }
        }

        return implode('; ', $safe);
    }

    /**
     * Blokir skema berbahaya seperti javascript: dan data:.
     */
    private function isSafeUrl(string $url): bool
    {
        $url = trim($url);

        if ($url === '' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url !== '';
        }

        return preg_match('/^(https?:\/\/|mailto:)/i', $url) === 1;
    }
}
