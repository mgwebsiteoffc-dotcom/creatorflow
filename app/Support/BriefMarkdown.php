<?php

namespace App\Support;

/**
 * Tiny, safe Markdown renderer for campaign briefs.
 *
 * Supports:
 *   - # H1 · ## H2 · ### H3
 *   - **bold** and *italic* / __bold__ / _italic_
 *   - `inline code`
 *   - [link text](https://url)
 *   - unordered lists ("- " / "* " / "• ")
 *   - ordered lists ("1. ")
 *   - blockquotes ("> ")
 *   - horizontal rule ("---")
 *   - paragraphs separated by blank lines
 *
 * All output is HTML-escaped before formatting is applied, so it's safe
 * to render as {!! ... !!} in a Blade view.
 */
class BriefMarkdown
{
    public static function render(?string $markdown): string
    {
        if ($markdown === null || trim($markdown) === '') {
            return '';
        }

        $text = str_replace(["\r\n", "\r"], "\n", $markdown);
        $lines = explode("\n", $text);

        $html = '';
        $listStack = []; // stack of 'ul' | 'ol'
        $inBlockquote = false;
        $paragraphBuf = [];

        $flushParagraph = function () use (&$paragraphBuf, &$html) {
            if (empty($paragraphBuf)) return;
            $joined = implode(' ', $paragraphBuf);
            $html .= '<p>'.static::inline($joined).'</p>';
            $paragraphBuf = [];
        };

        $closeLists = function () use (&$listStack, &$html) {
            while (! empty($listStack)) {
                $html .= '</'.array_pop($listStack).'>';
            }
        };

        $closeBlockquote = function () use (&$inBlockquote, &$html) {
            if ($inBlockquote) {
                $html .= '</blockquote>';
                $inBlockquote = false;
            }
        };

        foreach ($lines as $raw) {
            $line = rtrim($raw);

            // Blank line separates blocks
            if ($line === '') {
                $flushParagraph();
                $closeLists();
                $closeBlockquote();
                continue;
            }

            // Horizontal rule
            if (preg_match('/^\s*(---|\*\*\*|___)\s*$/', $line)) {
                $flushParagraph(); $closeLists(); $closeBlockquote();
                $html .= '<hr>';
                continue;
            }

            // Heading
            if (preg_match('/^(#{1,3})\s+(.+)$/', $line, $m)) {
                $flushParagraph(); $closeLists(); $closeBlockquote();
                $level = strlen($m[1]);
                $html .= '<h'.$level.'>'.static::inline($m[2]).'</h'.$level.'>';
                continue;
            }

            // Blockquote
            if (preg_match('/^>\s?(.*)$/', $line, $m)) {
                $flushParagraph(); $closeLists();
                if (! $inBlockquote) { $html .= '<blockquote>'; $inBlockquote = true; }
                $html .= '<p>'.static::inline($m[1]).'</p>';
                continue;
            }

            // Ordered list
            if (preg_match('/^\s*\d+\.\s+(.+)$/', $line, $m)) {
                $flushParagraph(); $closeBlockquote();
                if (empty($listStack) || end($listStack) !== 'ol') {
                    if (! empty($listStack)) { $html .= '</'.array_pop($listStack).'>'; }
                    $html .= '<ol>'; $listStack[] = 'ol';
                }
                $html .= '<li>'.static::inline($m[1]).'</li>';
                continue;
            }

            // Unordered list
            if (preg_match('/^\s*[-*•]\s+(.+)$/', $line, $m)) {
                $flushParagraph(); $closeBlockquote();
                if (empty($listStack) || end($listStack) !== 'ul') {
                    if (! empty($listStack)) { $html .= '</'.array_pop($listStack).'>'; }
                    $html .= '<ul>'; $listStack[] = 'ul';
                }
                $html .= '<li>'.static::inline($m[1]).'</li>';
                continue;
            }

            // Any other line — accumulate into paragraph
            $closeLists();
            $closeBlockquote();
            $paragraphBuf[] = $line;
        }

        $flushParagraph();
        $closeLists();
        $closeBlockquote();

        return $html;
    }

    protected static function inline(string $text): string
    {
        // Escape first for XSS safety.
        $t = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Inline code (backticks) — protect content first with placeholders
        $codes = [];
        $t = preg_replace_callback('/`([^`]+)`/', function ($m) use (&$codes) {
            $codes[] = '<code>'.$m[1].'</code>';
            return "\x01CODE".(count($codes) - 1)."\x01";
        }, $t);

        // Links: [text](https://url)
        $t = preg_replace_callback('/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/i', function ($m) {
            $url = $m[2];
            return '<a href="'.$url.'" target="_blank" rel="noopener noreferrer">'.$m[1].'</a>';
        }, $t);

        // Bold: **text** or __text__
        $t = preg_replace('/\*\*([^*\n]+)\*\*/', '<strong>$1</strong>', $t);
        $t = preg_replace('/__([^_\n]+)__/', '<strong>$1</strong>', $t);

        // Italic: *text* or _text_
        $t = preg_replace('/(?<![\*\w])\*([^*\n]+)\*(?!\w)/', '<em>$1</em>', $t);
        $t = preg_replace('/(?<![_\w])_([^_\n]+)_(?!\w)/', '<em>$1</em>', $t);

        // Restore inline code
        $t = preg_replace_callback('/\x01CODE(\d+)\x01/', fn ($m) => $codes[(int) $m[1]] ?? '', $t);

        // Auto-link bare URLs (not inside <a>)
        $t = preg_replace_callback('/(?<![">])\b(https?:\/\/[^\s<]+)/i', function ($m) {
            return '<a href="'.$m[1].'" target="_blank" rel="noopener noreferrer">'.$m[1].'</a>';
        }, $t);

        return $t;
    }
}
