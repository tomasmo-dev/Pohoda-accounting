<?php
// TODO - change header to a assoc array and create constructor for it
function RetrieveXml($invoice_id, $invoice_no, $invoice_created_d, $invoice_date, $invoice_due_date, 
                     $description, $client_bank_account_no, $acc_company_name, $acc_full_name,
                     $acc_city, $acc_address, $acc_zip, $acc_ico, $acc_vat,
                     $invoice_items)
{

$ico = "26916789"; // ico flying academy
$invoiceType = "issuedInvoice"; // constant from Pohoda for this usecase
$paymentType = "draft"; // constant from Pohoda for this usecase

$items = GetXmlItems($invoice_items);
$items_string = implode("\n", $items);

$xml = <<<XML_DOC
<?xml version="1.0" encoding="UTF-8"?>
<dat:dataPack version="2.0" id="Usr01" ico="{$ico}"  application="atpl" note="import" xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd" xmlns:inv="http://www.stormware.cz/schema/version_2/invoice.xsd" xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd" >
    <dat:dataPackItem version="2.0" id="INVOICE {$invoice_id}">
        <inv:invoice version="2.0">
            <inv:invoiceHeader>
                <inv:invoiceType>{$invoiceType}</inv:invoiceType>
                <inv:number>
                    <typ:numberRequested>{$invoice_no}</typ:numberRequested>
                </inv:number>
                <inv:date>{$invoice_created_d}</inv:date>
                <inv:dateTax>{$invoice_date}</inv:dateTax>
                <inv:dateDue>{$invoice_due_date}</inv:dateDue>
                
                <inv:text>{$description}</inv:text>
                <inv:paymentType>
                    <typ:paymentType>{$paymentType}</typ:paymentType>
                </inv:paymentType>
                <inv:account>
                    <typ:accountNo>{$client_bank_account_no}</typ:accountNo>
                </inv:account>
                <inv:partnerIdentity>
                    <typ:address>
                    <typ:company>{$acc_company_name}</typ:company>
                    <typ:name>{$acc_full_name}</typ:name>
                    <typ:city>{$acc_city}</typ:city>
                    <typ:street>{$acc_address}</typ:street>
                    <typ:zip>{$acc_zip}</typ:zip>
                    <typ:ico>{$acc_ico}</typ:ico>
                    <typ:dic>{$acc_vat}</typ:dic>
                    </typ:address>
                </inv:partnerIdentity>
            </inv:invoiceHeader>
            <inv:invoiceDetail>
                {$items_string}
            </inv:invoiceDetail>
        </inv:invoice>
    </dat:dataPackItem>
</dat:dataPack>
XML_DOC;


return $xml;
}

function GetXmlItems($items)
{
    $itms = array();

    foreach ($items as $item) {

        $quantity = 1;

        $stax = $item['stax']; // tax
        $exbe = $item['exbe']; // price

        $total = $exbe + $stax; // price + tax

        $xml_item = "<inv:invoiceItem>
                        <inv:text>{$item['revenue_type']}</inv:text>
                        <inv:quantity>{$quantity}</inv:quantity>
                        <inv:rateVAT>{$stax}</inv:rateVAT>
                        <inv:homeCurrency>
                            <typ:unitPrice>{$exbe}</typ:unitPrice>
                            <typ:priceVAT>{$stax}</typ:priceVAT>
                            <typ:priceSum>{$total}</typ:priceSum>
                        </inv:homeCurrency>
                    </inv:invoiceItem>";

        array_push($itms, $xml_item);
    }

    return $itms;
}
?>