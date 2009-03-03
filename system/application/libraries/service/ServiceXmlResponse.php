<?php
/**
 * Class ServiceXmlReponse
 */

/**
 * Creates an XML document that can be used for a Service response.
 *  
 * @author Mattijs Hoitink <mattijs@ibuildings.nl
 */
class ServiceXmlResponse
{
    
    /**
     * DOM document used for the response
     * @var DOMDocument
     */
    protected $_domDocument = null;
    
    /**
     * Root element for the XML document
     * @var DOMNode
     */
    protected $_documentRoot = null;
    
    
    public function __construct()
    {
        $this->_createDomDocument();
    }
    
    /**
     * Converts an array to DOM elements and add's it to the 
     * XML document
     * @param array $data
     */
    public function addArray($data, $tag)
    {
        $element = $this->_domDocument->createElement($tag);
        foreach($data as $key => $value) {
            $element->appendChild($this->_returnDomElement($value, $key));
        }
        
        $this->_documentRoot->appendChild($element);
    }
    
    /**
     * Adds a string to the xml document
     * @param string $string
     * @param string $tag
     */
    public function addString($string, $tag)
    {
        $element = $this->_domDocument->createElement($tag);
        $element->appendChild($this->_domDocument->createTextNode($string));
        
        $this->_documentRoot->appendChild($element);
    }
    
    /**
     * Returns the reponse as XML
     * @return string
     */
    public function getResponse()
    {
        return $this->_domDocument->saveXML();
    }
    
    /**
     * Creates a new DOM document
     */
    protected function _createDomDocument()
    {
        $doc = new DOMDocument('1.0');
        $doc->formatOuput = true;
        
        $root = $doc->createElement('response');
        $doc->appendChild($root);
        
        $this->_documentRoot = $root;
        $this->_domDocument = $doc;
    }
    
    /**
     * Returns an array as DOMNodes
     * @param array $data
     * @param string $itemTag
     * @return DOMNode
     */
    protected function _returnDomElement($data, $itemTag = null) 
    {
        $doc = $this->_domDocument;
        $element = $doc->createElement($itemTag);
    
        if(!is_array($data)) {
            $element->appendChild($doc->createTextNode($data));
            
        } else {
            foreach($data as $key => $value) {
                $element->appendChild($this->_returnDomElement($value, $key));
            }
        }
    
        return $element;
    }
    
}