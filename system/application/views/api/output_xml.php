<?php

// Parse the data array to an DOM object
$doc = new DOMDocument('1.0');

$root = $doc->createElement('response');
$doc->appendChild($root);

foreach($data as $tag => $value){
    $root->appendChild(returnDomElement($value, $tag, $doc));
}

$doc->formatOuput = true;

// Output the XML to the client
header('Content-Type: text/xml');
echo $doc->saveXML();






function returnDomElement($data, $itemTag = null, $doc) 
{
    
    $element = $doc->createElement($itemTag);

    if(!is_array($data)) {
        $element->appendChild($doc->createTextNode($data));
        
    } else {
        foreach($data as $key => $value) {
            $element->appendChild(returnDomElement($value, $key, $doc));
        }
    }

    return $element;
}

function appendDomElements($data, $itemTag, $parentNode, $replacementTag = '') 
{
    global $doc;

    $element = $doc->createElement($itemTag);
    if(!is_array($data)) {
        $element->appendChild($doc->createTextNode($data));
    } else {
        foreach($data as $key => $value) {
            if(is_numeric($key)) {
                $key = $replacementTag;
            }
            appendDomElements($value, $key, $element, $replacementTag);
        }
    }

    $parentNode->appendChild($element);
}

