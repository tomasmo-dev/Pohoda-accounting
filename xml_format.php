<?php

function RetrieveXml($invoice_id, $invoice_created_d, $invoice_date, $invoice_due_date, 
                     $description){
$ico = "26916789"; // ico flying academy
$invoiceType = "issuedInvoice"; // constant from Pohoda for this usecase
$paymentType = "draft"; // constant from Pohoda for this usecase


$xml = <<<XML_DOC
<?xml version="1.0" encoding="UTF-8"?>
<dat:dataPack version="2.0" id="Usr01" ico="{$ico}"  application="atpl" note="import" xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd" xmlns:inv="http://www.stormware.cz/schema/version_2/invoice.xsd" xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd" >
    <dat:dataPackItem version="2.0" id="INVOICE {$invoice_id}">
        <inv:invoice version="2.0">
            <inv:invoiceHeader>
                <inv:invoiceType>{$invoiceType}</inv:invoiceType>
                <inv:number>
                    <typ:numberRequested>[invoice.invoice_no; noerr]</typ:numberRequested>
                </inv:number>
                <inv:date>{$invoice_created_d}</inv:date>
                <inv:dateTax>{$invoice_date}</inv:dateTax>
                <inv:dateDue>{$invoice_due_date}</inv:dateDue>
                
                <inv:text>{$description}</inv:text>
                <inv:paymentType>
                    <typ:paymentType>{$paymentType}</typ:paymentType>
                </inv:paymentType>
                <inv:account>
                    <typ:accountNo>[client.bank_account; noerr]</typ:accountNo>
                </inv:account>
                <inv:partnerIdentity>
                    <typ:address>
                    <typ:company>[account.company_name; noerr]</typ:company>
                    <typ:name>[account.first_name; noerr] [account.surname; noerr]</typ:name>
                    <typ:city>[account.city; noerr]</typ:city>
                    <typ:street>[account.address; noerr]</typ:street>
                    <typ:zip>[account.zip; noerr]</typ:zip>
                    <typ:ico>[account.ico; noerr]</typ:ico>
                    <typ:dic>[account.vat; noerr]</typ:dic>
                    </typ:address>
                </inv:partnerIdentity>
            </inv:invoiceHeader>
            <inv:invoiceDetail>
                <inv:invoiceItem>
                    <inv:text>[invoice_item.description; ope=max:90; block=inv:invoiceItem]</inv:text>
                    <inv:quantity>[invoice_item.qnt]</inv:quantity>
                    <inv:rateVAT>[invoice_item.pohoda_vat_rate; noerr]</inv:rateVAT>
                    <inv:homeCurrency>
                        <typ:unitPrice>[invoice_item.unit_price]</typ:unitPrice>
                        <typ:priceVAT>[invoice_item.vat_rate]</typ:priceVAT>
                        <typ:priceSum>[invoice_item.total_amount]</typ:priceSum>
                    </inv:homeCurrency>
                </inv:invoiceItem>
            </inv:invoiceDetail>
        </inv:invoice>
    </dat:dataPackItem>
</dat:dataPack>
XML_DOC;


return $xml;
}
?>