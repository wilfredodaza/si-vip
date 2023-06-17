<?php


namespace App\Controllers\Xml;


use App\Controllers\Api\Auth;
use App\Models\Countries;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Language;
use App\Models\Municipalities;
use App\Models\TypeDocument;
use App\Models\TypeDocumentIdentifications;
use App\Models\TypeItemIdentification;
use App\Models\TypeRegimes;
use App\Models\UnitMeasure;


class InvoiceXml extends DocumentXml
{

    public function __construct($xml, $namespace)
    {
        $this->xml           = $xml;
        $this->namespaces    = $namespace;
    }



    public function header()
    {
        try {
            $typeDocument = new TypeDocument();
        
            if($paymentExchangeRate = $this->xml->children($this->namespaces['cac'])->PaymentExchangeRate) {
                $currency   = new Currency();
                $currencys  = $currency->where(['code'  => $this->zero($paymentExchangeRate->children($this->namespaces['cbc'])->SourceCurrencyCode)])
                    ->get()
                    ->getResult();

                if(count($currencys) > 0) {
                    $this->format['idcurrency']      = $currencys[0]->id;
                }
                $this->format['calculationrate']     =  $this->zero($paymentExchangeRate->children($this->namespaces['cbc'])->CalculationRate, 1, 'calculationrate');
                $this->format['calculationratedate'] =  $this->zero($paymentExchangeRate->children($this->namespaces['cbc'])->Date, date('Y-m-d'), 'calculationratedate');

            }

            
            $typeDocumentId                     = $typeDocument->asObject()->where(['code' => $this->zero($this->xml->children($this->namespaces['cbc'])->InvoiceTypeCode)])->asObject()->get()->getResult()[0];
            $this->format['prefix']             = $this->zero($this->xml->children($this->namespaces['ext'])->UBLExtensions->UBLExtension->ExtensionContent->children($this->namespaces['sts'])->DianExtensions->InvoiceControl->AuthorizedInvoices->Prefix, '');
            $this->format['number']             = str_replace($this->format['prefix'], '', $this->zero($this->xml->children($this->namespaces['cbc'])->ID));
            $this->format['resolution_number']  = $this->zero($this->xml->children($this->namespaces['ext'])->UBLExtensions->UBLExtension->ExtensionContent->children($this->namespaces['sts'])->DianExtensions->InvoiceControl->InvoiceAuthorization, null);
            $this->format['date']               = $this->zero($this->xml->children($this->namespaces['cbc'])->IssueDate, null, 'date');
            $this->format['time']               = explode('-', $this->zero($this->xml->children($this->namespaces['cbc'])->IssueTime, null))[0];
            $this->format['type_document_id']   = $typeDocumentId->id;
            $this->format['notes']              = $this->zero($this->xml->children($this->namespaces['cbc'])->Note, null);
            $this->format['uuid']               = $this->zero($this->xml->children($this->namespaces['cbc'])->UUID, null);


 
        }catch (\Exception $e) {
            $this->errors['error'][$this->position] = 'Error en encabezado de la factura.';
            $this->position++;
        }

    }

    public function company()
    {
        try {
             if ($this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->TaxLevelCode->attributes()->listName)) {

                $typeRegime = new TypeRegimes();
                $typeRegimes = $typeRegime->where(['code' => $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->TaxLevelCode->attributes()->listName)])
                    ->asObject()
                    ->get()
                    ->getResult();
                if (count($typeRegimes) > 0) {
                    $this->format['company']['type_regime_id'] = $typeRegimes[0]->id;
                } else {
                    $this->format['company']['type_regime_id'] = null;
                }
            }


            $municipality = new Municipalities();
            if ($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PhysicalLocation->Address) {

                $municipalities = $municipality->where('code', (int)$this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PhysicalLocation->Address->children($this->namespaces['cbc'])->ID)->get()->getResult();
                if (count($municipalities) > 0) {
                    $this->format['company']['municipality_id'] = $municipalities[0]->id;
                } else {
                    $this->format['company']['municipality_id'] = null;
                }
            }else if(isset($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyTaxScheme->RegistrationAddress)) {
                $municipalities = $municipality->where('code', (int) $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyTaxScheme->RegistrationAddress->children($this->namespaces['cbc']))->ID)->get()->getResult();
                if (count($municipalities) > 0) {
                    $this->format['company']['municipality_id'] = $municipalities[0]->id;
                } else {
                    $this->format['company']['municipality_id'] = null;
                }

            }


            if ($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->CompanyID) {
                $typeDocument = new TypeDocumentIdentifications();
                $typeDocuments = $typeDocument->where(['code' => $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->CompanyID->attributes()->schemeName)])
                    ->asObject()
                    ->get()
                    ->getResult();
                if (count($typeDocuments) > 0) {
                    $this->format['company']['type_document_identification_id'] = $typeDocuments[0]->id;
                } else {
                    $this->format['company']['type_document_identification_id'] = null;
                }
            }


            $this->format['company']['identification_number'] = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->CompanyID, NULL);
            $this->format['company']['dv'] = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->CompanyID->attributes()->schemeID, NULL);
           if($this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyName, NULL)) {
               $this->format['company']['name'] = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyName->children($this->namespaces['cbc'])->Name, NULL);
           }else {
               $this->format['company']['name'] = $this->zero($this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->RegistrationName, NULL));
           }

            $this->format['company']['phone'] = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->Contact->children($this->namespaces['cbc'])->Telephone, NULL);
            $this->format['company']['address'] = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyTaxScheme->RegistrationAddress->AddressLine->children($this->namespaces['cbc'])->Line, NULL);
            $this->format['company']['email'] = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->Contact->children($this->namespaces['cbc'])->ElectronicMail, NULL, 'company::email');
            $this->format['company']['merchant_registration'] = isset($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyLegalEntity->CorporateRegistrationScheme) ? $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->Party->PartyLegalEntity->CorporateRegistrationScheme->children($this->namespaces['cbc'])->Name, NULL): null;
            $this->format['company']['type_organization_id'] = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingSupplierParty->children($this->namespaces['cbc'])->AdditionalAccocompany, NULL);


        }catch (\Exception $e) {

            $this->errors['error'][$this->position] = $e->getMessage();
            $this->position++;
        }

    }

    public function customer()
    {

        try {

            if ($this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->TaxLevelCode->attributes()->listName)) {
                $typeRegime = new TypeRegimes();
                $typeRegimes = $typeRegime->where(['code' => $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->TaxLevelCode->attributes()->listName)])
                    ->asObject()
                    ->get()
                    ->getResult();
                count($typeRegimes) > 0 ? $this->format['customer']['type_regime_id'] = $typeRegimes[0]->id : '';
            }


            $municipality           = new Municipalities();
            if (isset($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PhysicalLocation)) {
                $this->format['customer']['address']                = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PhysicalLocation->Address->AddressLine->children($this->namespaces['cbc'])->Line, NULL);
                $municipalities         = $municipality->where('code', (int)$this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PhysicalLocation->Address->children($this->namespaces['cbc'])->ID))->get()->getResult();
                if (count($municipalities) > 0) {
                    $this->format['customer']['municipality_id'] = $municipalities[0]->id;
                } else {
                    $this->format['customer']['municipality_id'] = null;
                }

            }else if(isset($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party)) {

                $this->format['customer']['address']                = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->RegistrationAddress->AddressLine->children($this->namespaces['cbc'])->Line, NULL);
                $municipalities = $municipality->where('code', (int) $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->RegistrationAddress->children($this->namespaces['cbc'])->ID) )
                    ->get()
                    ->getResult();
                if (count($municipalities) > 0) {
                    $this->format['customer']['municipality_id'] = $municipalities[0]->id;
                } else {
                    $this->format['customer']['municipality_id'] = null;
                }

            }


            if ($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->CompanyID) {
                $typeDocument = new TypeDocumentIdentifications();
                $typeDocuments = $typeDocument->where(['code' => $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->CompanyID->attributes()->schemeName)])
                    ->asObject()
                    ->get()
                    ->getResult();
                if(count($typeDocuments) > 0 ) {
                    $this->format['customer']['type_document_identification_id'] = $typeDocuments[0]->id;
                } else {
                    $this->format['customer']['type_document_identification_id'] = null;
                }

            }




            $this->format['customer']['identification_number']  = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->CompanyID, NULL);
            $this->format['customer']['dv']                     = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->CompanyID->attributes()->schemeID, NULL);
           // $this->format['customer']['name']                   = isset($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyName)? $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyName->children($this->namespaces['cbc'])->Name, NULL) : null;
            if($this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyName, NULL)) {
                $this->format['customer']['name']  = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyName->children($this->namespaces['cbc'])->Name, NULL);
            }else {
                $this->format['customer']['name']  = $this->zero($this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyTaxScheme->children($this->namespaces['cbc'])->RegistrationName, NULL));
            }

        //    $this->format['customer']['phone']                  = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->Contact->children($this->namespaces['cbc'])->Telephone, NULL);
       //     $this->format['customer']['email']                  = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->Contact->children($this->namespaces['cbc'])->ElectronicMail, NULL);

            $this->format['customer']['merchant_registration']  = isset($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyLegalEntity->CorporateRegistrationScheme) ? $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->Party->PartyLegalEntity->CorporateRegistrationScheme->children($this->namespaces['cbc'])->Name, NULL): NULL;
            $this->format['customer']['type_organization_id']   = $this->zero($this->xml->children($this->namespaces['cac'])->AccountingCustomerParty->children($this->namespaces['cbc'])->AdditionalAccountID, NULL);
        }catch (\Exception $e) {
            $this->errors['error'][$this->position] = 'Error en datos del cliente.';
            $this->position++;
        }
    }

    public function paymentForm()
    {
        try {
            if($this->xml->children($this->namespaces['cac'])->PaymentMeans) {
                $this->format['payment_form']['payment_form_id']    = $this->zero($this->xml->children($this->namespaces['cac'])->PaymentMeans->children($this->namespaces['cbc'])->ID, NULL);
                $this->format['payment_form']['payment_method_id']  = $this->zero($this->xml->children($this->namespaces['cac'])->PaymentMeans->children($this->namespaces['cbc'])->PaymentMeansCode, NULL );
                $this->format['payment_form']['payment_due_date']   = $this->zero($this->xml->children($this->namespaces['cac'])->PaymentMeans->children($this->namespaces['cbc'])->PaymentDueDate, NULL);
                $this->format['payment_form']['duration_measure']   = isset($invoice->PaymentTerms) ? $this->zero($invoice->PaymentTerms->children($this->namespaces['cac'])->SettlementPeriod->children($this->namespaces['cbc'])->DurationMeasure , NULL) : 0;
            }
        }catch (\Exception $e) {
            $this->errors['error'][$this->position] = 'Error en método de pago.'.$e;
            $this->position++;
        }
    }

    public function allowanceCharges()
    {
        try {
            $i = 0;
            foreach ($this->xml->children($this->namespaces['cac'])->AllowanceCharge as $item) {
                $this->format['allowance_charges'][$i]['discount_id']                = $this->zero($item->children($this->namespaces['cbc'])->ID, NULL);
                $this->format['allowance_charges'][$i]['charge_indicator']           = $this->zero($item->children($this->namespaces['cbc'])->ChargeIndicator, NULL);
                $this->format['allowance_charges'][$i]['allowance_charge_reason']    = $this->zero($item->children($this->namespaces['cbc'])->AllowanceChargeReason, 'DESCUENTO GENERAL');
                $this->format['allowance_charges'][$i]['amount']                     = $this->zero($item->children($this->namespaces['cbc'])->Amount, NULL);
                $this->format['allowance_charges'][$i]['base_amount']                = $this->zero($item->children($this->namespaces['cbc'])->BaseAmount, NULL);
                $i++;
            }

        }catch(\Exception $e) {
            $this->errors['error'][$this->position] = 'Error en descuentos y cargos generales.';
            $this->position++;
        }

    }

    public function legalMonetaryTotals()
    {
        try {
            $this->format['legal_monetary_totals']['line_extension_amount']     = $this->zero($this->xml->children($this->namespaces['cac'])->LegalMonetaryTotal->children($this->namespaces['cbc'])->LineExtensionAmount, '0.00');
            $this->format['legal_monetary_totals']['tax_exclusive_amount']      = $this->zero($this->xml->children($this->namespaces['cac'])->LegalMonetaryTotal->children($this->namespaces['cbc'])->TaxExclusiveAmount, '0.00');
            $this->format['legal_monetary_totals']['tax_inclusive_amount']      = $this->zero($this->xml->children($this->namespaces['cac'])->LegalMonetaryTotal->children($this->namespaces['cbc'])->TaxInclusiveAmount, '0.00');
            $this->format['legal_monetary_totals']['allowance_total_amount']    = $this->zero($this->xml->children($this->namespaces['cac'])->LegalMonetaryTotal->children($this->namespaces['cbc'])->AllowanceTotalAmount, '0.00');
            $this->format['legal_monetary_totals']['charge_total_amount']       = $this->zero($this->xml->children($this->namespaces['cac'])->LegalMonetaryTotal->children($this->namespaces['cbc'])->ChargeTotalAmount, '0.00');
            $this->format['legal_monetary_totals']['pre_paid_amount']           = $this->zero($this->xml->children($this->namespaces['cac'])->LegalMonetaryTotal->children($this->namespaces['cbc'])->PrepaidAmount, '0.00');
            $this->format['legal_monetary_totals']['payable_amount']            = $this->zero($this->xml->children($this->namespaces['cac'])->LegalMonetaryTotal->children($this->namespaces['cbc'])->PayableAmount, '0.00');
        }catch (\Exception $e) {
            $this->errors[$this->position] = 'Error en los totales de la factura.';
            $this->position++;
        }

    }

    public function taxTotals()
    {
        try{
            $i = 0;
            foreach ($this->xml->children($this->namespaces['cac'])->TaxTotal as $item) {
                $this->format['tax_totals'][$i]['tax_id']           = (int)  $this->zero($item->TaxSubtotal->TaxCategory->TaxScheme->children($this->namespaces['cbc'])->ID);
                $this->format['tax_totals'][$i]['tax_amount']       = $this->zero($item->TaxSubtotal->children($this->namespaces['cbc'])->TaxAmount);
                $this->format['tax_totals'][$i]['percent']          = $this->zero($item->TaxSubtotal->TaxCategory->children($this->namespaces['cbc'])->Percent);
                $this->format['tax_totals'][$i]['taxable_amount']   = $this->zero($item->TaxSubtotal->children($this->namespaces['cbc'])->TaxableAmount);
                $i++;
            }
        }catch (\Exception $e){
            $this->errors['error'][$this->position] = 'Error en los impuestos.';
            $this->position++;
        }
    }

    public function invoiceLines()
    {
        try {
            $i = 0;
            foreach ($this->xml->children($this->namespaces['cac'])->InvoiceLine as $item) {
                if ($this->zero($item->Price->children($this->namespaces['cbc'])->BaseQuantity->attributes()->unitCode)) {
                    $unitMeasure = new UnitMeasure();
                    $unitMeasures = $unitMeasure->asObject()->where(['code' => $this->zero($item->Price->children($this->namespaces['cbc'])->BaseQuantity->attributes()->unitCode)])
                        ->get()
                        ->getResult();
                    count($unitMeasures) > 0 ? $this->format['invoice_lines'][$i]['unit_measure_id'] = $unitMeasures[0]->id : '';
                }
                $this->format['invoice_lines'][$i]['invoiced_quantity']         = $this->zero($item->children($this->namespaces['cbc'])->InvoicedQuantity);
                $this->format['invoice_lines'][$i]['line_extension_amount']     = $this->zero($item->children($this->namespaces['cbc'])->LineExtensionAmount);
                $this->format['invoice_lines'][$i]['free_of_charge_indicator']  = $this->zero($item->children($this->namespaces['cbc'])->FreeOfChargeIndicator);

                $l = 0;
                foreach ($item->children($this->namespaces['cac'])->AllowanceCharge as $item2) {
                    $this->format['invoice_lines'][$i]['allowance_charges'][$l]['charge_indicator']             = $this->zero($item2->children($this->namespaces['cbc'])->ChargeIndicator, false);
                    $this->format['invoice_lines'][$i]['allowance_charges'][$l]['allowance_charge_reason']      = $this->zero($item2->children($this->namespaces['cbc'])->AllowanceChargeReason,'Descuento Aplicado');
                    $this->format['invoice_lines'][$i]['allowance_charges'][$l]['amount']                       = $this->zero($item2->children($this->namespaces['cbc'])->Amount);
                    $this->format['invoice_lines'][$i]['allowance_charges'][$l]['base_amount']                  = $this->zero($item2->children($this->namespaces['cbc'])->BaseAmount);
                    $l++;
                }


                $t = 0;
                foreach ($item->TaxTotal as $item3) {

                    $this->format['invoice_lines'][$i]['tax_totals'][$t]['tax_id']          = (int)$this->zero($item3->TaxSubtotal->TaxCategory->TaxScheme->children($this->namespaces['cbc'])->ID);
                    $this->format['invoice_lines'][$i]['tax_totals'][$t]['tax_amount']      = $this->zero($item3->TaxSubtotal->children($this->namespaces['cbc'])->TaxAmount);
                    $this->format['invoice_lines'][$i]['tax_totals'][$t]['percent']         = $this->zero($item3->TaxSubtotal->TaxCategory->children($this->namespaces['cbc'])->Percent);
                    $this->format['invoice_lines'][$i]['tax_totals'][$t]['taxable_amount']  = $this->zero($item3->TaxSubtotal->children($this->namespaces['cbc'])->TaxableAmount);
                    $t++;
                }

                if ($this->zero($item->Item->StandardItemIdentification)) {
                    $typeItemIdentifications = new TypeItemIdentification();
                    $typeItemIdentification = $typeItemIdentifications->where(['code' => (int)$this->zero($item->Item->StandardItemIdentification->children($this->namespaces['cbc'])->ID->attributes()->schemeID)])
                        ->asObject()
                        ->get()
                        ->getResult();

                    count($typeItemIdentification) > 0 ? $this->format['invoice_lines'][$i]['type_item_identification_id'] = $typeItemIdentification->id : '';

                }
                $this->format['invoice_lines'][$i]['description']   = $this->zero($item->Item->children($this->namespaces['cbc'])->Description);
                $this->format['invoice_lines'][$i]['code']          = $item->Item->StandardItemIdentification ? $this->zero($item->Item->StandardItemIdentification->children($this->namespaces['cbc'])->ID) : null;
                $this->format['invoice_lines'][$i]['price_amount']  = $this->zero($item->Price->children($this->namespaces['cbc'])->PriceAmount);
                $this->format['invoice_lines'][$i]['base_quantity'] = $this->zero($item->Price->children($this->namespaces['cbc'])->BaseQuantity);
                $i++;

            }
        }catch (\Exception $e) {
            $this->errors[$this->position] = 'Error en los productos o servicios de la factura.';
            $this->position++;
        }

    }

    public function delivery()
    {

        try{
            if(count((array)$this->xml->children($this->namespaces['cac'])->Delivery) != 0) {
                $language = new Language();
                $languages = $language->where(['code' => $this->zero($this->xml->children($this->namespaces['cac'])->Delivery->DeliveryAddress->Country->children($this->namespaces['cbc'])->Name->attributes()->languageID)])
                    ->get()->getResult();
                count($languages) > 0 ? $this->format['delivery']['languaje_id'] = $languages[0]->id : '';
                $countries = new Countries();
                $country = $countries->where(['code' => $this->zero($this->xml->children($this->namespaces['cac'])->Delivery->DeliveryAddress->Country->children($this->namespaces['cbc'])->IdentificationCode)])
                    ->get()
                    ->getResult();
                count($country) > 0 ? $this->format['delivery']['country_id'] = $country[0]->id : '';
                $this->format['delivery']['municipality_id'] = $this->zero($this->xml->children($this->namespaces['cac'])->Delivery->DeliveryAddress->children($this->namespaces['cbc'])->ID);
                $this->format['delivery']['address'] = $this->zero($this->xml->children($this->namespaces['cac'])->Delivery->DeliveryAddress->AddressLine->children($this->namespaces['cbc'])->Line);
                $this->format['delivery']['actual_delivery_date'] = $this->zero($this->xml->children($this->namespaces['cac'])->Delivery->children($this->namespaces['cbc'])->ActualDeliveryDate);
            }
        } catch (\Exception $e) {
            $this->errors[$this->position] = 'Error en la entrega del producto.';
            $this->position++;
        }

    }

    public function prepaidPayment()
    {

        try {
            if(isset($this->xml->children($this->namespaces['cac'])->PrepaidPayment)) {
                $this->format['prepaid_payment']['idpayment']       = $this->zero($this->xml->children($this->namespaces['cac'])->PrepaidPayment->children($this->namespaces['cbc'])->ID);
                $this->format['prepaid_payment']['paidamount']      = $this->zero($this->xml->children($this->namespaces['cac'])->PrepaidPayment->children($this->namespaces['cbc'])->PaidAmount);
                $this->format['prepaid_payment']['receiveddate']    = $this->zero($this->xml->children($this->namespaces['cac'])->PrepaidPayment->children($this->namespaces['cbc'])->ReceivedDate);
                $this->format['prepaid_payment']['paiddate']        = $this->zero($this->xml->children($this->namespaces['cac'])->PrepaidPayment->children($this->namespaces['cbc'])->PaidDate);
                $this->format['prepaid_payment']['instructionid']   = $this->zero($this->xml->children($this->namespaces['cac'])->PrepaidPayment->children($this->namespaces['cbc'])->InstructionID);
            }

        }catch (\Exception $e) {
            $this->errors['error'][$this->position] = 'Error en métodos de pago.';
            $this->position++;
        }

    }

    public function withholdingTaxTotal()
    {
        try {
            $i = 0;
            foreach ($this->xml->children($this->namespaces['cac'])->WithholdingTaxTotal as $item) {
                $this->format['with_holding_tax_total'][$i]['tax_id']           = (int)$this->zero($item->TaxSubtotal->TaxCategory->TaxScheme->children($this->namespaces['cbc'])->ID);
                $this->format['with_holding_tax_total'][$i]['tax_amount']       = $this->zero($item->TaxSubtotal->children($this->namespaces['cbc'])->TaxAmount);
                $this->format['with_holding_tax_total'][$i]['percent']          = $this->zero($item->TaxSubtotal->TaxCategory->children($this->namespaces['cbc'])->Percent);
                $this->format['with_holding_tax_total'][$i]['taxable_amount']   = $this->zero($item->TaxSubtotal->children($this->namespaces['cbc'])->TaxableAmount);
                $i++;
            }
        }catch (\Exception $e) {
            $this->errors['error'][$this->position]= 'Error en las retenciones de la factura.';
            $this->position++;
        }

    }

    public function package()
    {
        $this->header();
        $this->company();
        $this->customer();
        $this->delivery();
        $this->paymentForm();
        $this->prepaidPayment();
        $this->allowanceCharges();
        $this->legalMonetaryTotals();
        $this->taxTotals();
        $this->withholdingTaxTotal();
        $this->invoiceLines();
        if(count($this->errors) == 0) {
            return $this->format;
        }else {
            return $this->errors;
        }
    }
}