<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

final class RichContentSanitizer
{
    private const TAGS = ['p', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'strong', 'em', 'blockquote', 'a', 'br'];

    public function sanitize(?string $html): ?string
    {
        if (! filled($html)) {
            return null;
        }
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div id="cms-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $document->getElementById('cms-root');
        if (! $root) {
            return e(strip_tags($html));
        }
        $this->clean($root, $document);
        $result = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $result .= $document->saveHTML($child);
        }

        return trim($result);
    }

    private function clean(DOMNode $node, DOMDocument $document): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);
                if (! in_array($tag, self::TAGS, true)) {
                    if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'svg'], true)) {
                        $node->removeChild($child);

                        continue;
                    }
                    $text = $document->createTextNode($child->textContent);
                    $node->replaceChild($text, $child);

                    continue;
                }
                $href = $tag === 'a' ? trim($child->getAttribute('href')) : null;
                foreach (iterator_to_array($child->attributes) as $attribute) {
                    $child->removeAttribute($attribute->name);
                }
                if ($tag === 'a') {
                    if ($href && SafeCmsUrl::isSafe($href)) {
                        $child->setAttribute('href', $href);
                        if (preg_match('#^https?://#i', $href)) {
                            $child->setAttribute('target', '_blank');
                            $child->setAttribute('rel', 'noopener noreferrer');
                        }
                    }
                }
                $this->clean($child, $document);
            }
        }
    }
}
