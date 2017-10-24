<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inline_style_converter implements IParser, IStyle
{
    private $prefix;
    private $hasObject = false;
    private $inc       = 0;
    private $header    = '';
    private $style     = '';
    private $footer    = '';

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function parse($element)
    {
        if ($element->hasAttributes()) {
            $inline_style = $element->getAttribute('style');

            if (!empty(trim($inline_style))) {
                $className = $this->createClassName();

                $this->style[] = '.' . $className . '{' . $inline_style . '}';

                if ($element->hasAttribute('class')) {
                    # Gotta check first, for existing class attribute.
                    $className = $element->getAttribute('class') . ' ' . $className;
                    # Remove class attribute
                    $element->removeAttribute('class');
                }
                # Add new class attribute
                $element->setAttribute('class', $className);
                # Remove style attribute
                $element->removeAttribute('style');

                $this->hasObject = true;
            }
        }
    }

    public function getStyle()
    {
        $ampStyle = '';
        if ($this->hasObject) {
            $ampStyle = implode($this->style, PHP_EOL);
        }
        return $ampStyle;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    private function createClassName()
    {
        return $this->prefix . '-' . $this->inc++;
    }
}
