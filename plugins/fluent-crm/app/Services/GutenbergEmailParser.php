<?php

namespace FluentCrm\App\Services;
/**
 * Gutenberg Block Parser for Email
 * Converts Gutenberg blocks to email-compatible HTML
 */

class GutenbergEmailParser {
    
    /**
     * Parse Gutenberg blocks and convert to email HTML
     * 
     * @param string $content The post content with Gutenberg blocks
     * @return string Email-compatible HTML
     */
    public function parse($content) {
        // Parse blocks using WordPress function if available
        if (function_exists('parse_blocks')) {
            $blocks = parse_blocks($content);
        } else {
            // Fallback: use custom parser
            $blocks = $this->parseBlocksManually($content);
        }
        
        return $this->renderBlocks($blocks);
    }
    
    /**
     * Manual block parser (fallback if parse_blocks not available)
     */
    private function parseBlocksManually($content) {
        $blocks = [];
        $pattern = '/<!--\s+wp:([a-z][a-z0-9_-]*\/)?([a-z][a-z0-9_-]*)\s+(\{.*?\})?\s+(\/)?-->/';
        
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
        
        $lastOffset = 0;
        foreach ($matches[0] as $index => $match) {
            $blockName = ($matches[1][$index][0] ?? '') . $matches[2][$index][0];
            $attrs = $matches[3][$index][0] ?? '{}';
            $isSelfClosing = !empty($matches[4][$index][0]);
            
            $blockStart = $match[1] + strlen($match[0]);
            
            // Find closing tag if not self-closing
            if (!$isSelfClosing) {
                $closingPattern = '/<!--\s+\/wp:' . preg_quote($blockName, '/') . '\s+-->/';
                if (preg_match($closingPattern, $content, $closeMatch, PREG_OFFSET_CAPTURE, $blockStart)) {
                    $innerHTML = substr($content, $blockStart, $closeMatch[0][1] - $blockStart);
                    $lastOffset = $closeMatch[0][1] + strlen($closeMatch[0][0]);
                } else {
                    $innerHTML = '';
                }
            } else {
                $innerHTML = '';
            }
            
            $blocks[] = [
                'blockName' => $blockName,
                'attrs' => json_decode($attrs, true) ?? [],
                'innerHTML' => trim($innerHTML),
                'innerBlocks' => []
            ];
        }
        
        return $blocks;
    }
    
    /**
     * Render blocks to email HTML
     */
    private function renderBlocks($blocks, $nested = false) {
        $html = '';
        
        foreach ($blocks as $block) {
            if (empty($block['blockName'])) {
                // Classic content or unrecognized block
                if (!empty($block['innerHTML'])) {
                    $html .= $this->wrapInTable($block['innerHTML']);
                }
                continue;
            }
            
            $html .= $this->renderBlock($block);
        }
        
        return $html;
    }
    
    /**
     * Render individual block
     */
    private function renderBlock($block) {
        $blockName = $block['blockName'];
        $attrs = $block['attrs'] ?? [];
        $innerHTML = $block['innerHTML'] ?? '';
        $innerBlocks = $block['innerBlocks'] ?? [];
        
        // Handle different block types
        switch ($blockName) {
            case 'core/paragraph':
                return $this->renderParagraph($innerHTML, $attrs);
                
            case 'core/heading':
                return $this->renderHeading($innerHTML, $attrs);
                
            case 'core/image':
                return $this->renderImage($attrs, $innerHTML);
                
            case 'core/list':
                return $this->renderList($innerHTML, $innerBlocks, $attrs);
                
            case 'core/list-item':
                return $this->renderListItem($innerHTML, $attrs);
                
            case 'core/quote':
                return $this->renderQuote($innerHTML, $attrs);
                
            case 'core/button':
                return $this->renderButton($innerHTML, $attrs);
                
            case 'core/buttons':
                return $this->renderButtons($innerBlocks, $attrs);
                
            case 'core/columns':
                return $this->renderColumns($innerBlocks, $attrs);
                
            case 'core/column':
                return $this->renderColumn($innerBlocks, $attrs, $innerHTML);
                
            case 'core/separator':
                return $this->renderSeparator($attrs);
                
            case 'core/spacer':
                return $this->renderSpacer($attrs);
                
            case 'core/group':
                return $this->renderGroup($innerBlocks, $attrs, $innerHTML);
                
            case 'core/cover':
                return $this->renderCover($innerBlocks, $attrs, $innerHTML);
                
            case 'core/table':
                return $this->renderTable($innerHTML, $attrs);
                
            case 'core/social-links':
                return $this->renderSocialLinks($innerBlocks, $attrs, $innerHTML);
                
            case 'core/social-link':
                return ''; // Handled by parent social-links
                
            default:
                // Fallback for unrecognized blocks
                if (!empty($innerHTML)) {
                    return $this->wrapInTable($innerHTML);
                } elseif (!empty($innerBlocks)) {
                    return $this->renderBlocks($innerBlocks, true);
                }
                return '';
        }
    }
    
    /**
     * Render paragraph block
     */
    private function renderParagraph($content, $attrs) {
        $content = trim($content);
        
        // Skip empty paragraphs
        if (empty($content) || $content === '<p></p>') {
            return '';
        }
        
        $align = $attrs['align'] ?? 'left';
        $fontSize = $attrs['fontSize'] ?? '';
        $style = $attrs['style'] ?? [];
        
        $styles = "margin: 0 0 16px 0; padding: 0; line-height: 1.6;";
        $styles .= "text-align: {$align};";
        
        // Font size
        if ($fontSize === 'large') {
            $styles .= " font-size: 20px;";
        } elseif ($fontSize === 'medium') {
            $styles .= " font-size: 18px;";
        } elseif ($fontSize === 'small') {
            $styles .= " font-size: 14px;";
        }
        
        if (!empty($style['color']['background'])) {
            $styles .= " background-color: {$style['color']['background']};";
        }
        if (!empty($style['color']['text'])) {
            $styles .= " color: {$style['color']['text']};";
        }
        
        // Extract content if it's wrapped in <p> tags
        if (preg_match('/<p[^>]*>(.*?)<\/p>/s', $content, $matches)) {
            $innerContent = $matches[1];
        } else {
            $innerContent = $content;
        }
        
        return $this->wrapInTable("<p style=\"{$styles}\">{$innerContent}</p>");
    }
    
    /**
     * Render heading block
     */
    private function renderHeading($content, $attrs) {
        $level = $attrs['level'] ?? 2;
        $align = $attrs['align'] ?? 'left';
        $style = $attrs['style'] ?? [];
        
        $fontSize = [
            1 => '32px',
            2 => '28px',
            3 => '24px',
            4 => '20px',
            5 => '18px',
            6 => '16px'
        ][$level] ?? '24px';
        
        $styles = "margin: 0 0 16px 0; padding: 0; font-weight: bold;";
        $styles .= " font-size: {$fontSize}; line-height: 1.3;";
        $styles .= " text-align: {$align};";
        
        if (!empty($style['color']['text'])) {
            $styles .= " color: {$style['color']['text']};";
        }
        
        // Extract content if it's wrapped in heading tags
        if (preg_match('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/s', $content, $matches)) {
            $innerContent = $matches[1];
        } else {
            $innerContent = $content;
        }
        
        return $this->wrapInTable("<h{$level} style=\"{$styles}\">{$innerContent}</h{$level}>");
    }
    
    /**
     * Render image block
     */
    private function renderImage($attrs, $innerHTML = '') {
        $url = $attrs['url'] ?? '';
        $alt = $attrs['alt'] ?? '';
        $width = $attrs['width'] ?? '';
        $height = $attrs['height'] ?? '';
        $align = $attrs['align'] ?? 'center';
        $id = $attrs['id'] ?? '';
        
        // If URL is not in attrs, try to extract from innerHTML
        if (empty($url) && !empty($innerHTML)) {
            if (preg_match('/src=["\']([^"\']+)["\']/', $innerHTML, $matches)) {
                $url = $matches[1];
            }
        }
        
        // Extract alt if not in attrs
        if (empty($alt) && !empty($innerHTML)) {
            if (preg_match('/alt=["\']([^"\']*)["\']/', $innerHTML, $matches)) {
                $alt = $matches[1];
            }
        }
        
        if (empty($url)) {
            return '';
        }
        
        $imgStyles = "display: block; max-width: 100%; height: auto; border: 0;";
        if ($width) {
            $imgStyles .= " width: {$width}px;";
        }
        
        $alignStyle = $align === 'center' ? 'margin: 0 auto;' : '';
        $textAlign = 'center';
        
        $img = "<img src=\"{$url}\" alt=\"{$alt}\" style=\"{$imgStyles}\" />";
        
        return $this->wrapInTable("<div style=\"text-align: {$textAlign}; {$alignStyle} margin-bottom: 16px;\">{$img}</div>");
    }
    
    /**
     * Render list block
     */
    private function renderList($content, $innerBlocks, $attrs) {
        $ordered = $attrs['ordered'] ?? false;
        $tag = $ordered ? 'ol' : 'ul';
        
        $styles = "margin: 0 0 16px 0; padding-left: 30px; line-height: 1.6;";
        
        // If we have innerBlocks, render them
        if (!empty($innerBlocks)) {
            $listItems = '';
            foreach ($innerBlocks as $block) {
                if ($block['blockName'] === 'core/list-item') {
                    $listItems .= $this->renderListItem($block['innerHTML'], $block['attrs'] ?? []);
                }
            }
            return $this->wrapInTable("<{$tag} style=\"{$styles}\">{$listItems}</{$tag}>");
        }
        
        // Otherwise, use innerHTML
        if (preg_match('/<(ul|ol)[^>]*>(.*?)<\/(ul|ol)>/s', $content, $matches)) {
            $innerContent = $matches[2];
        } else {
            $innerContent = $content;
        }
        
        return $this->wrapInTable("<{$tag} style=\"{$styles}\">{$innerContent}</{$tag}>");
    }
    
    /**
     * Render list item
     */
    private function renderListItem($content, $attrs) {
        $styles = "margin-bottom: 8px;";
        
        // Extract content if wrapped in <li> tags
        if (preg_match('/<li[^>]*>(.*?)<\/li>/s', $content, $matches)) {
            $innerContent = $matches[1];
        } else {
            $innerContent = $content;
        }
        
        return "<li style=\"{$styles}\">{$innerContent}</li>";
    }
    
    /**
     * Render quote block
     */
    private function renderQuote($content, $attrs) {
        $styles = "margin: 20px 0; padding: 15px 20px; border-left: 4px solid #ccc;";
        $styles .= " background-color: #f9f9f9; font-style: italic;";
        
        return $this->wrapInTable("<blockquote style=\"{$styles}\">{$content}</blockquote>");
    }
    
    /**
     * Render buttons container
     */
    private function renderButtons($innerBlocks, $attrs) {
        $layout = $attrs['layout'] ?? [];
        $justifyContent = $layout['justifyContent'] ?? 'center';
        
        $alignMap = [
            'left' => 'left',
            'center' => 'center',
            'right' => 'right',
            'space-between' => 'center'
        ];
        
        $textAlign = $alignMap[$justifyContent] ?? 'center';
        
        $buttonsHtml = '';
        foreach ($innerBlocks as $button) {
            if ($button['blockName'] === 'core/button') {
                $buttonsHtml .= $this->renderButton($button['innerHTML'], $button['attrs'] ?? []);
            }
        }
        
        return $this->wrapInTable("<div style=\"text-align: {$textAlign}; margin: 20px 0;\">{$buttonsHtml}</div>");
    }
    
    /**
     * Render button block
     */
    private function renderButton($content, $attrs) {
        // Parse button from innerHTML
        preg_match('/<a[^>]*href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/s', $content, $matches);
        
        $url = $matches[1] ?? '#';
        $text = strip_tags($matches[2] ?? 'Button');
        
        $backgroundColor = $attrs['backgroundColor'] ?? '';
        $textColor = $attrs['textColor'] ?? '';
        $style = $attrs['style'] ?? [];
        $className = $attrs['className'] ?? '';
        
        // Default colors
        $bgColor = '#0073aa';
        $txtColor = '#ffffff';
        
        if (!empty($style['color']['background'])) {
            $bgColor = $style['color']['background'];
        } elseif (!empty($backgroundColor)) {
            // Map WordPress color slugs to actual colors
            $bgColor = $this->getColorFromSlug($backgroundColor);
        }
        
        if (!empty($style['color']['text'])) {
            $txtColor = $style['color']['text'];
        } elseif (!empty($textColor)) {
            $txtColor = $this->getColorFromSlug($textColor);
        }
        
        // Check if it's an outline button
        $isOutline = strpos($className, 'is-style-outline') !== false;
        
        if ($isOutline) {
            $buttonStyles = "display: inline-block; padding: 12px 24px; margin: 5px;";
            $buttonStyles .= " background-color: transparent; color: {$bgColor};";
            $buttonStyles .= " border: 2px solid {$bgColor};";
            $buttonStyles .= " text-decoration: none; border-radius: 4px; font-weight: bold;";
        } else {
            $buttonStyles = "display: inline-block; padding: 12px 24px; margin: 5px;";
            $buttonStyles .= " background-color: {$bgColor}; color: {$txtColor};";
            $buttonStyles .= " text-decoration: none; border-radius: 4px; font-weight: bold;";
        }
        
        return "<a href=\"{$url}\" style=\"{$buttonStyles}\">{$text}</a>";
    }
    
    /**
     * Render columns block
     */
    private function renderColumns($innerBlocks, $attrs) {
        if (empty($innerBlocks)) {
            return '';
        }
        
        $columnCount = count($innerBlocks);
        $columnWidth = floor(100 / $columnCount);
        
        $columnsHtml = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 20px 0;"><tr>';
        
        foreach ($innerBlocks as $column) {
            $columnsHtml .= '<td width="' . $columnWidth . '%" style="vertical-align: top; padding: 10px;">';
            $columnsHtml .= $this->renderBlock($column);
            $columnsHtml .= '</td>';
        }
        
        $columnsHtml .= '</tr></table>';
        
        return $columnsHtml;
    }
    
    /**
     * Render column block
     */
    private function renderColumn($innerBlocks, $attrs, $innerHTML) {
        $html = '';
        
        if (!empty($innerBlocks)) {
            $html = $this->renderBlocks($innerBlocks, true);
        } elseif (!empty($innerHTML)) {
            $html = $innerHTML;
        }
        
        return $html;
    }
    
    /**
     * Render cover block
     */
    private function renderCover($innerBlocks, $attrs, $innerHTML) {
        $url = $attrs['url'] ?? '';
        $dimRatio = $attrs['dimRatio'] ?? 50;
        $overlayColor = $attrs['overlayColor'] ?? '';
        $style = $attrs['style'] ?? [];
        
        // Extract image URL from innerHTML if not in attrs
        if (empty($url) && !empty($innerHTML)) {
            if (preg_match('/src=["\']([^"\']+)["\']/', $innerHTML, $matches)) {
                $url = $matches[1];
            }
        }
        
        $opacity = $dimRatio / 100;
        
        $coverStyles = "position: relative; min-height: 300px; background-size: cover; background-position: center;";
        if ($url) {
            $coverStyles .= " background-image: url('{$url}');";
        }
        
        $overlayStyles = "position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,{$opacity});";
        
        $contentStyles = "position: relative; z-index: 1; padding: 40px 20px; color: #ffffff;";
        
        // Render inner content
        $innerContent = '';
        if (!empty($innerBlocks)) {
            foreach ($innerBlocks as $block) {
                $innerContent .= $this->renderBlock($block);
            }
        }
        
        // For email, we'll simplify this to just show the image and content
        if ($url) {
            $html = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 20px 0;">';
            $html .= '<tr><td style="padding: 0;"><img src="' . $url . '" alt="" style="display: block; width: 100%; max-width: 100%; height: auto;" /></td></tr>';
            if (!empty($innerContent)) {
                $html .= '<tr><td style="padding: 20px; background-color: rgba(0,0,0,' . $opacity . '); color: #ffffff;">' . $innerContent . '</td></tr>';
            }
            $html .= '</table>';
            return $html;
        }
        
        return $this->wrapInTable($innerContent);
    }
    
    /**
     * Render separator block
     */
    private function renderSeparator($attrs) {
        $styles = "border: none; border-top: 1px solid #ccc; margin: 30px 0;";
        
        return $this->wrapInTable("<hr style=\"{$styles}\" />");
    }
    
    /**
     * Render spacer block
     */
    private function renderSpacer($attrs) {
        $height = $attrs['height'] ?? 50;
        
        return $this->wrapInTable("<div style=\"height: {$height}px;\"></div>");
    }
    
    /**
     * Render group block
     */
    private function renderGroup($innerBlocks, $attrs, $innerHTML) {
        $style = $attrs['style'] ?? [];
        $layout = $attrs['layout'] ?? [];
        $backgroundColor = '';
        
        if (!empty($style['color']['background'])) {
            $backgroundColor = "background-color: {$style['color']['background']};";
        }
        
        $groupStyles = "padding: 20px; {$backgroundColor}";
        
        // Check if it's a flex layout
        $isFlex = isset($layout['type']) && $layout['type'] === 'flex';
        
        if ($isFlex) {
            $groupStyles .= " display: flex; flex-wrap: wrap; gap: 10px;";
            $justifyContent = $layout['justifyContent'] ?? 'flex-start';
            $groupStyles .= " justify-content: {$justifyContent};";
        }
        
        $content = '';
        if (!empty($innerBlocks)) {
            $content = $this->renderBlocks($innerBlocks, true);
        } elseif (!empty($innerHTML)) {
            $content = $innerHTML;
        }
        
        return $this->wrapInTable("<div style=\"{$groupStyles}\">{$content}</div>");
    }
    
    /**
     * Render table block
     */
    private function renderTable($content, $attrs) {
        $styles = "width: 100%; border-collapse: collapse; margin: 20px 0;";
        $cellStyles = "border: 1px solid #ddd; padding: 12px; text-align: center;";
        
        // Extract table content
        if (preg_match('/<table[^>]*>(.*?)<\/table>/s', $content, $matches)) {
            $tableContent = $matches[1];
            
            // Add styles to table cells
            $tableContent = preg_replace('/<td([^>]*)>/', '<td$1 style="' . $cellStyles . '">', $tableContent);
            $tableContent = preg_replace('/<th([^>]*)>/', '<th$1 style="' . $cellStyles . ' font-weight: bold;">', $tableContent);
            
            return $this->wrapInTable("<table style=\"{$styles}\">{$tableContent}</table>");
        }
        
        return $this->wrapInTable($content);
    }
    
    /**
     * Render social links block
     */
    private function renderSocialLinks($innerBlocks, $attrs, $innerHTML) {
        $socialHtml = '<div style="margin: 20px 0; text-align: center;">';
        
        // Parse social links from innerHTML
        preg_match_all('/<li[^>]*class="wp-social-link[^"]*"[^>]*>.*?<a[^>]*href=["\']([^"\']*)["\'][^>]*>.*?<\/li>/s', $innerHTML, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[0] as $index => $match) {
                $url = $matches[1][$index];
                
                // Determine service from class or URL
                $service = 'link';
                if (strpos($match, 'wordpress') !== false || strpos($url, 'wordpress.org') !== false) {
                    $service = 'wordpress';
                } elseif (strpos($match, 'facebook') !== false || strpos($url, 'facebook.com') !== false) {
                    $service = 'facebook';
                } elseif (strpos($match, 'github') !== false || strpos($url, 'github.com') !== false) {
                    $service = 'github';
                } elseif (strpos($match, 'twitter') !== false || strpos($url, 'twitter.com') !== false) {
                    $service = 'twitter';
                } elseif (strpos($match, 'linkedin') !== false || strpos($url, 'linkedin.com') !== false) {
                    $service = 'linkedin';
                } elseif (strpos($match, 'instagram') !== false || strpos($url, 'instagram.com') !== false) {
                    $service = 'instagram';
                }
                
                $icon = $this->getSocialIcon($service);
                
                $socialHtml .= '<a href="' . $url . '" style="display: inline-block; margin: 0 5px; text-decoration: none;">';
                $socialHtml .= '<span style="display: inline-block; width: 40px; height: 40px; background-color: #0073aa; color: #fff; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px;">';
                $socialHtml .= $icon;
                $socialHtml .= '</span>';
                $socialHtml .= '</a>';
            }
        }
        
        $socialHtml .= '</div>';
        
        return $this->wrapInTable($socialHtml);
    }
    
    /**
     * Get social media icon
     */
    private function getSocialIcon($service) {
        $icons = [
            'facebook' => 'f',
            'twitter' => '🐦',
            'linkedin' => 'in',
            'instagram' => '📷',
            'github' => '⚙',
            'wordpress' => 'W',
            'link' => '🔗'
        ];
        
        return $icons[$service] ?? '🔗';
    }
    
    /**
     * Get color from WordPress color slug
     */
    private function getColorFromSlug($slug) {
        // Common WordPress theme colors
        $colors = [
            'theme-palette-color-1' => '#000000',
            'theme-palette-color-2' => '#0073aa',
            'theme-palette-color-3' => '#229fd8',
            'theme-palette-color-4' => '#eee',
            'black' => '#000000',
            'white' => '#ffffff',
            'primary' => '#0073aa',
            'secondary' => '#23282d',
        ];
        
        return $colors[$slug] ?? '#0073aa';
    }
    
    /**
     * Wrap content in email-safe table structure
     */
    private function wrapInTable($content) {
        if (empty(trim($content))) {
            return '';
        }
        
        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td style="padding: 0;">
            {$content}
        </td>
    </tr>
</table>
HTML;
    }
    
    /**
     * Generate complete email HTML with wrapper
     */
    public function generateEmailHtml($content, $title = '') {
        $parsedContent = $this->parse($content);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{$title}</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }
        table {
            border-collapse: collapse;
        }
        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        a {
            color: #0073aa;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" class="email-container" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff;">
                    <tr>
                        <td style="padding: 40px 30px;">
                            {$parsedContent}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}

// Usage Example:
/*
$parser = new GutenbergEmailParser();

// Option 1: Parse blocks only (returns HTML fragment)
$post_content = get_post_field('post_content', $post_id);
$emailHtml = $parser->parse($post_content);

// Option 2: Generate complete email HTML with wrapper
$completeEmail = $parser->generateEmailHtml($post_content, 'Email Title');

// Send email
wp_mail($to, $subject, $completeEmail, ['Content-Type: text/html; charset=UTF-8']);
*/