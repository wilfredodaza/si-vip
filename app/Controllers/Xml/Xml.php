<?php


namespace App\Controllers\Xml;


class Xml extends DocumentXml
{

    protected $errors = [];
    protected $postion = 0;
    protected $format = [];
    public $typeDocument = 0;

    public function __construct($path)
    {

        $content                    =   file_get_contents($path);
        $this->xml                  =   simplexml_load_string($content,'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        $this->namespaces           =   $this->xml->getNamespaces(true);


    }

    public function assignmentDocument()
    {
        if(isset($this->xml->children($this->namespaces['cbc'])->InvoiceTypeCode)) {
            $typeDocumentId = $this->xml->children($this->namespaces['cbc'])->InvoiceTypeCode;
            return $this->typeDocument($typeDocumentId, $this->xml, $this->namespaces);
        }else if($typeDocumentId = $this->xml->children($this->namespaces['cbc'])->CreditNoteTypeCode) {
            return $this->typeDocument($typeDocumentId, $this->xml, $this->namespaces);
        }else if($this->xml->children($this->namespaces['cac'])->Attachment) {
            $data =  $this->attachedDocumentXml();
            $this->xml = $data[0];
            $this->namespaces = $data[1];
            return $this->assignmentDocument();
        }else{
            $typeDocumentId = 92;
            return $this->typeDocument($typeDocumentId, $this->xml, $this->namespaces);
        }



    }

    protected function attachedDocumentXml()
    {
        $text           = $this->xml->children($this->namespaces['cac'])->Attachment->ExternalReference->children($this->namespaces['cbc'])->Description;
        $xml            = simplexml_load_string($text,'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        $namespaces     = $xml->getNamespaces(true);
        return  [$xml, $namespaces];
    }

    public function typeDocument($code, $xml, $namespace)
    {

        switch($code) {
            case '01':
                $document = new InvoiceXml($xml, $namespace);
                break;
            case '02':
                $document = new InvoiceXml($xml, $namespace);
                break;
            case '91':
                $document = new CreditNoteXml($xml, $namespace);
                break;
            case '92':
                $document = new DebitNoteXml($xml, $namespace);
                break;
        }

        $data = $document->package();
        $this->inputs = $document->inputs;
        return $data;

    }

    public function getInputs() {
        return $this->inputs;
    }

}