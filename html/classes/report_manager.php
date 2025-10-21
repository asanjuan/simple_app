<?php

require_once 'lib/mpdf/vendor/autoload.php';

class TemplateEngine {
    protected $template;
    protected $data;

    public function __construct($template) {
        $this->template = $template;
    }

    public function render($data) {
        $this->data = $data;
        $output = $this->template;
        
        // Procesar estructuras
        //while (strpos($output, 'repeat=') !== false) {
            $output = $this->processRepeat($output);
        //}
        
        $output = $this->processIf($output);
        $output = $this->processVariables($output);

        return $output;
    }

    protected function processVariables($template) {
        return preg_replace_callback('/{(\w+(\.\w+)?)(\|\w+)?}/', function ($matches) {
            $keys = explode('.', $matches[1]);
            $value = $this->data[$keys[0]] ?? '';
            
            if (isset($keys[1]) && is_array($value)) {
                $value = $value[$keys[1]] ?? '';
            }
            $value = $this->format_report_value($value);
            
            return $value;
        }, $template);
    }

    protected function processRepeat($template) {
        return preg_replace_callback('/<([a-zA-Z0-9]+) data-repeat="(\w+) as (\w+)"(.*?)>(.*?)<\/\1>/s', function ($matches) {
            $tag = $matches[1];
            $list = $this->data[$matches[2]] ?? [];
            $itemVar = $matches[3];
            $attributes = $matches[4];
            $block = $matches[5];
            $result = '';
            
			foreach ($list as $item) {
                $tempEngine = new self($block);
                $renderedBlock = $tempEngine->render(array_merge($this->data, [$itemVar => $item]));
                $result .= "<$tag$attributes>$renderedBlock</$tag>";
            }
            
            return $result;
        }, $template);
    }

    protected function processIf($template) {
        return preg_replace_callback('/<([a-zA-Z0-9]+) if="(\w+(\.\w+)?)"(.*?)>(.*?)<\/\1>/s', function ($matches) {
            $tag = $matches[1];
            $keys = explode('.', $matches[2]);
            $value = $this->data[$keys[0]] ?? null;
            
            if (isset($keys[1]) && is_array($value)) {
                $value = $value[$keys[1]] ?? false;
            }
            
            return $value ? "<$tag{$matches[4]}>{$matches[5]}</$tag>" : '';
        }, $template);
    }

    function format_report_value($valor)
    {
        
        if (is_numeric($valor) && is_float($valor+0) ) {
            
            $valor = number_format($valor, 2);
        } elseif (is_numeric($valor)) {
            
            $valor = number_format($valor, 0);

        } 
        
        return $valor;
    }
}



