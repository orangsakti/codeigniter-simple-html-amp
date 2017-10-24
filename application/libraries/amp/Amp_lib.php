<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('converter/ConverterFactory.php');

class Amp_lib
{
    private $document;
    private $converter;

    public function __construct(array $params)
    {
        if(!isset($params['css_prefix'])) {
            $params['css_prefix'] = 'amp-custom';
        }
        $this->converter = new ConverterFactory($params);
    }

    public function createDocument($html)
    {
        $this->document = new DOMDocument();
        libxml_use_internal_errors(true);
        $this->document->loadHTML('<?xml encoding="UTF-8">' . $html);
        $this->document->encoding = 'UTF-8';
        libxml_clear_errors();
    }

    public function convert()
    {
        # Getting all elements
        if (!($root = $this->document->getElementsByTagName('html')->item(0))) {
            throw new \InvalidArgumentException('Invalid HTML was provided');
        }

        $this->convertChildren($root);

        $this->removeProhibited();
        return [
                    'content' => $this->sanitize($this->document->saveHTML()),
                    'header'  => $this->converter->getHeader(),
                    'footer'  => $this->converter->getFooter(),
                    'style'   => $this->converter->getStyle(),
                    ];
    }

    private function convertChildren($element)
    {
        if ($element->hasChildNodes()) {
            $children = [];
            if ($element->childNodes->length > 0) {
                /** @var \DOMNode $node */
                foreach ($element->childNodes as $node) {
                    $children[] = $node;
                }
            }
            foreach ($children as $child) {
                $this->convertChildren($child);
            }
        }

        $this->convertToAmp($element);
    }

    private function convertToAmp($element)
    {
         $this->converter->convert($element);
    }

    private function removeProhibited()
    {
        // TODO: Config-based
        $xpath = '//' . implode('|//', [
            'script',
        ]);
        $elements = (new \DOMXPath($this->document))->query($xpath);
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            if ($element->parentNode !== null) {
                $element->parentNode->removeChild($element);
            }
        }
        // Remove anchors with javascript in the href
        $anchors = (new \DOMXPath($this->document))
            ->query('//a[contains(@href, "javascript:")]');
        foreach ($anchors as $a) {
            if ($a->parentNode !== null) {
                $a->parentNode->removeChild($a);
            }
        }
    }

    private function sanitize($html)
    {
        $html = preg_replace('/<!DOCTYPE [^>]+>/', '', $html);
        $unwanted = array('<html>', '</html>', '<body>', '</body>', '<head>', '</head>', '<?xml encoding="UTF-8">', '&#xD;');
        $html = str_replace($unwanted, '', $html);
        $html = trim($html, "\n\r\0\x0B");
        return $html;
    }
}
