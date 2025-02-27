<?php

namespace Weglot\Parser\Check\Dom;

use Weglot\Client\Api\Enum\WordType;
use Weglot\Util\Text as TextUtil;

class Text extends AbstractDomChecker
{
    const DOM = 'text';

    const PROPERTY = 'innertext';

    const WORD_TYPE = WordType::TEXT;

    protected function check()
    {
        return 'script' != $this->node->parent()->tag
            && 'style' != $this->node->parent()->tag
            && 'noscript' != $this->node->parent()->tag
            && 'code' != $this->node->parent()->tag
            && !is_numeric(TextUtil::fullTrim($this->node->innertext))
            && !preg_match('/^\d+%$/', TextUtil::fullTrim($this->node->innertext))
            && !str_contains($this->node->innertext, '[vc_')
            && !str_contains($this->node->innertext, '<?php');
    }
}
