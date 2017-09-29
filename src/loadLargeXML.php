<?php

namespace Vallsjm;

use \XMLReader;
use \DomDocument;

class loadLargeXML {
    protected $_file;
    protected $_xml;
    protected $_callbacks;
    
    public function __construct($file = null) {
        $this->_file = $file;
        $this->_xml = null;
        $this->_callbacks = array();
    }
    
    public function openURL($url) {
        $this->_file = $url;
    }

    public function openXML($xml) {
        $this->_xml = $xml;
    }
    
    public function addListener($tagname, $callback) {
        if (!isset($this->_callbacks[$tagname]))
            $this->_callbacks[$tagname] = array();

        array_push($this->_callbacks[$tagname], $callback);
    }
    
    public function parse() {
        $matches = 0;
        $reader = new XMLReader();
        if ($this->_xml) {
            $reader->xml($this->_xml);   
        } else {
            $reader->open($this->_file);
        }    
        $pila = array();
        $halt = false;
        while (!$halt && $reader->read()) {
            switch ($reader->nodeType) {
                case (XMLREADER::ELEMENT):
                    $pos = $reader->localName;
                    array_push($pila, $pos);
                    if (isset($this->_callbacks[$pos])) {
                        $node = $reader->expand();
                        $dom = new DomDocument();
                        $n = $dom->importNode($node,true);
                        $dom->appendChild($n);
                        $sxe = simplexml_import_dom($n);
                        $xpath = '//' . implode('/', $pila);
                        foreach ($this->_callbacks[$pos] as $listener) {
                            $matches++;
                            $returnValue = $listener($sxe, $xpath);
                            if($returnValue === false){
                                $halt = true;        
                            }
                        }
                    }
                break;
                case (XMLREADER::END_ELEMENT):
                    $pos = $reader->localName;
                    array_pop($pila);
                break;     
            }
        }
        return $matches;    
    }    
}
