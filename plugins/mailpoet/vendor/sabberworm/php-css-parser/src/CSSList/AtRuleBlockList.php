<?php
declare(strict_types=1);
namespace Sabberworm\CSS\CSSList;
if (!defined('ABSPATH')) exit;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Property\AtRule;
class AtRuleBlockList extends CSSBlockList implements AtRule
{
 private $type;
 private $arguments;
 public function __construct(string $type, string $arguments = '', ?int $lineNumber = null)
 {
 parent::__construct($lineNumber);
 $this->type = $type;
 $this->arguments = $arguments;
 }
 public function atRuleName(): string
 {
 return $this->type;
 }
 public function atRuleArgs(): string
 {
 return $this->arguments;
 }
 public function render(OutputFormat $outputFormat): string
 {
 $formatter = $outputFormat->getFormatter();
 $result = $formatter->comments($this);
 $result .= $outputFormat->getContentBeforeAtRuleBlock();
 $arguments = $this->arguments;
 if ($arguments !== '') {
 $arguments = ' ' . $arguments;
 }
 $result .= "@{$this->type}$arguments{$formatter->spaceBeforeOpeningBrace()}{";
 $result .= $this->renderListContents($outputFormat);
 $result .= '}';
 $result .= $outputFormat->getContentAfterAtRuleBlock();
 return $result;
 }
 public function isRootList(): bool
 {
 return false;
 }
}
