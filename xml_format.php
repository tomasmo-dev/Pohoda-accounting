<?php
// TODO - change header to a assoc array and create constructor for it
function RetrieveXml($invoice_id, $varSym, $invoice_created_d, $invoice_date, $invoice_acc_d, $invoice_due_date, 
                     $custId, $description, $acc_company_name, $acc_full_name,
                     $acc_city, $acc_address, $acc_zip, $acc_ico, $acc_vat,
                     $invoice_items)
{
// varsym = YEARMONTHID

$ico = "26916789"; // ico flying academy
$invoiceType = "issuedInvoice"; // constant from Pohoda for this usecase
$paymentType = "draft"; // constant from Pohoda for this usecase

$parameterVal = "CZ".$custId;

$items = GetXmlItems($invoice_items); // array of string items
$items_string = implode("\n", $items); // concatonates items with newlines

$xml = <<<XML_DOC
<?xml version="1.0" encoding="UTF-8"?>
<dat:dataPack version="2.0" id="Usr01" ico="{$ico}"  application="atpl" note="import" xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd" xmlns:inv="http://www.stormware.cz/schema/version_2/invoice.xsd" xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd" >
    <dat:dataPackItem version="2.0" id="INVOICE {$invoice_id}">
        <inv:invoice version="2.0">
            <inv:invoiceHeader>
                <inv:invoiceType>{$invoiceType}</inv:invoiceType>
                <inv:symVar>{$varSym}</inv:symVar>
                <inv:date>{$invoice_created_d}</inv:date>
                <inv:dateTax>{$invoice_date}</inv:dateTax>
                <inv:dateAccounting>{$invoice_acc_d}</inv:dateAccounting>
                <inv:dateDue>{$invoice_due_date}</inv:dateDue>
                <inv:accounting>
                    <typ:ids>602LETSKOLA</typ:ids>
                </inv:accounting>
                <inv:classificationVAT>
                    <typ:ids>UDA5</typ:ids>
                </inv:classificationVAT>
                <inv:centre>
                    <typ:ids>ATO</typ:ids>
                </inv:centre>
                <inv:parameters>
                    <typ:parameter>
                    <typ:name>VPrMyFboID</typ:name>
                    <typ:textValue>{$parameterVal}</typ:textValue>
                    </typ:parameter>
                </inv:parameters>
                <inv:text>{$description}</inv:text>
                <inv:paymentType>
                    <typ:paymentType>{$paymentType}</typ:paymentType>
                </inv:paymentType>
                <inv:account>
                    <typ:ids>KBCZ</typ:ids>
                    <typ:accountNo>51-942250257</typ:accountNo>
                    <typ:bankCode>0100</typ:bankCode>
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

        $rateVat = $stax == 0 ? "none" : "high"; // tax rate

        $total = $exbe + $stax; // price + tax

        $xml_item = "<inv:invoiceItem>
                        <inv:text>Pilot training</inv:text>
                        <inv:quantity>{$quantity}</inv:quantity>
                        <inv:rateVAT>{$rateVat}</inv:rateVAT>
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


function RetrieveInternalXml($year, $month, $custId, $date_c, $date_Tax, $date_Accounting, $date_KHDPH,
                             $full_name, $city, $address, $total_price){

    $note = ""; // "Uživatelský export, Datum = Červen"; asi prazdne?
    $id = "INTERNAL {$year}-{$month}-{$custId}"; // "INTERNAL ROK-MESIC-ID"; "Usr01 (001)"
    $varsym = $year.$month.$custId; // variable symbol "ROKMESICID"; "2017061"

    $date = $date_c; // "ROK-MESIC-DEN";
    $dateTax = $date_Tax; // "ROK-MESIC-POSLEDNI DEN MESICE";
    $dateAccounting = $date_Accounting; // "ROK-MESIC-POSLEDNI DEN MESICE";
    $dateKHDPH = $date_KHDPH; // "ROK-MESIC-POSLEDNI DEN MESICE";

    $text = "Úhrada FV varsym {$varsym}, pilot training";
    $text_detail = "Úhrada FV varsym {$varsym}"; // bez ", pilot training"?

$xml = <<<XML_DOC
<?xml version="1.0" encoding="UTF-8"?>
<dat:dataPack version="2.0" id="Usr01" ico="26916789" application="atpl"  note="{$note}" xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd">
    <dat:dataPackItem version="2.0" id="{$id}">
        <int:intDoc version="2.0" xmlns:int="http://www.stormware.cz/schema/version_2/intDoc.xsd">
            <int:intDocHeader xmlns:rsp="http://www.stormware.cz/schema/version_2/response.xsd" xmlns:rdc="http://www.stormware.cz/schema/version_2/documentresponse.xsd" xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd" xmlns:lst="http://www.stormware.cz/schema/version_2/list.xsd" xmlns:lStk="http://www.stormware.cz/schema/version_2/list_stock.xsd" xmlns:lAdb="http://www.stormware.cz/schema/version_2/list_addBook.xsd" xmlns:lCen="http://www.stormware.cz/schema/version_2/list_centre.xsd" xmlns:lAcv="http://www.stormware.cz/schema/version_2/list_activity.xsd" xmlns:acu="http://www.stormware.cz/schema/version_2/accountingunit.xsd" xmlns:inv="http://www.stormware.cz/schema/version_2/invoice.xsd" xmlns:vch="http://www.stormware.cz/schema/version_2/voucher.xsd" xmlns:stk="http://www.stormware.cz/schema/version_2/stock.xsd" xmlns:ord="http://www.stormware.cz/schema/version_2/order.xsd" xmlns:ofr="http://www.stormware.cz/schema/version_2/offer.xsd" xmlns:enq="http://www.stormware.cz/schema/version_2/enquiry.xsd" xmlns:vyd="http://www.stormware.cz/schema/version_2/vydejka.xsd" xmlns:pri="http://www.stormware.cz/schema/version_2/prijemka.xsd" xmlns:bal="http://www.stormware.cz/schema/version_2/balance.xsd" xmlns:pre="http://www.stormware.cz/schema/version_2/prevodka.xsd" xmlns:vyr="http://www.stormware.cz/schema/version_2/vyroba.xsd" xmlns:pro="http://www.stormware.cz/schema/version_2/prodejka.xsd" xmlns:con="http://www.stormware.cz/schema/version_2/contract.xsd" xmlns:adb="http://www.stormware.cz/schema/version_2/addressbook.xsd" xmlns:prm="http://www.stormware.cz/schema/version_2/parameter.xsd" xmlns:lCon="http://www.stormware.cz/schema/version_2/list_contract.xsd" xmlns:ctg="http://www.stormware.cz/schema/version_2/category.xsd" xmlns:ipm="http://www.stormware.cz/schema/version_2/intParam.xsd" xmlns:str="http://www.stormware.cz/schema/version_2/storage.xsd" xmlns:idp="http://www.stormware.cz/schema/version_2/individualPrice.xsd" xmlns:sup="http://www.stormware.cz/schema/version_2/supplier.xsd" xmlns:prn="http://www.stormware.cz/schema/version_2/print.xsd" xmlns:lck="http://www.stormware.cz/schema/version_2/lock.xsd" xmlns:isd="http://www.stormware.cz/schema/version_2/isdoc.xsd" xmlns:sEET="http://www.stormware.cz/schema/version_2/sendEET.xsd" xmlns:act="http://www.stormware.cz/schema/version_2/accountancy.xsd" xmlns:bnk="http://www.stormware.cz/schema/version_2/bank.xsd" xmlns:sto="http://www.stormware.cz/schema/version_2/store.xsd" xmlns:grs="http://www.stormware.cz/schema/version_2/groupStocks.xsd" xmlns:acp="http://www.stormware.cz/schema/version_2/actionPrice.xsd" xmlns:csh="http://www.stormware.cz/schema/version_2/cashRegister.xsd" xmlns:bka="http://www.stormware.cz/schema/version_2/bankAccount.xsd" xmlns:ilt="http://www.stormware.cz/schema/version_2/inventoryLists.xsd" xmlns:nms="http://www.stormware.cz/schema/version_2/numericalSeries.xsd" xmlns:pay="http://www.stormware.cz/schema/version_2/payment.xsd" xmlns:mKasa="http://www.stormware.cz/schema/version_2/mKasa.xsd" xmlns:gdp="http://www.stormware.cz/schema/version_2/GDPR.xsd" xmlns:est="http://www.stormware.cz/schema/version_2/establishment.xsd" xmlns:cen="http://www.stormware.cz/schema/version_2/centre.xsd" xmlns:acv="http://www.stormware.cz/schema/version_2/activity.xsd" xmlns:afp="http://www.stormware.cz/schema/version_2/accountingFormOfPayment.xsd" xmlns:vat="http://www.stormware.cz/schema/version_2/classificationVAT.xsd" xmlns:rgn="http://www.stormware.cz/schema/version_2/registrationNumber.xsd" xmlns:ftr="http://www.stormware.cz/schema/version_2/filter.xsd" xmlns:asv="http://www.stormware.cz/schema/version_2/accountingSalesVouchers.xsd" xmlns:arch="http://www.stormware.cz/schema/version_2/archive.xsd" xmlns:req="http://www.stormware.cz/schema/version_2/productRequirement.xsd" xmlns:mov="http://www.stormware.cz/schema/version_2/movement.xsd" xmlns:rec="http://www.stormware.cz/schema/version_2/recyclingContrib.xsd" xmlns:srv="http://www.stormware.cz/schema/version_2/service.xsd" xmlns:rul="http://www.stormware.cz/schema/version_2/rulesPairing.xsd" xmlns:lwl="http://www.stormware.cz/schema/version_2/liquidationWithoutLink.xsd" xmlns:dis="http://www.stormware.cz/schema/version_2/discount.xsd" xmlns:lqd="http://www.stormware.cz/schema/version_2/automaticLiquidation.xsd">
                <int:symVar>{$varsym}</int:symVar>
                <int:date>{$date}</int:date>
                <int:dateTax>{$dateTax}</int:dateTax>
                <int:dateAccounting>{$dateAccounting}</int:dateAccounting>
                <int:dateKHDPH>{$dateKHDPH}</int:dateKHDPH>
                <int:accounting>
                    <typ:ids>325PREDPL.P.</typ:ids>
                </int:accounting>
                <int:classificationVAT>
                    <typ:ids>UN</typ:ids>
                    <typ:classificationVATType>nonSubsume</typ:classificationVATType>
                </int:classificationVAT>
                <int:text>{$text}</int:text>
                <int:partnerIdentity>
                    <typ:address>
                        <typ:name>{$full_name}</typ:name>
                        <typ:city>{$city}</typ:city>
                        <typ:street>{$address}</typ:street>
                    </typ:address>
                </int:partnerIdentity>
                <int:liquidation>false</int:liquidation>
                <int:centre>
                    <typ:ids>ATO</typ:ids>
                </int:centre>
                <int:activity>
                    <typ:ids></typ:ids>
                </int:activity>
                <int:lock1>false</int:lock1>
                <int:lock2>false</int:lock2>
                <int:markRecord>false</int:markRecord>
                <int:parameters></int:parameters>
            </int:intDocHeader>
            <int:intDocDetail xmlns:rsp="http://www.stormware.cz/schema/version_2/response.xsd" xmlns:rdc="http://www.stormware.cz/schema/version_2/documentresponse.xsd" xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd" xmlns:lst="http://www.stormware.cz/schema/version_2/list.xsd" xmlns:lStk="http://www.stormware.cz/schema/version_2/list_stock.xsd" xmlns:lAdb="http://www.stormware.cz/schema/version_2/list_addBook.xsd" xmlns:lCen="http://www.stormware.cz/schema/version_2/list_centre.xsd" xmlns:lAcv="http://www.stormware.cz/schema/version_2/list_activity.xsd" xmlns:acu="http://www.stormware.cz/schema/version_2/accountingunit.xsd" xmlns:inv="http://www.stormware.cz/schema/version_2/invoice.xsd" xmlns:vch="http://www.stormware.cz/schema/version_2/voucher.xsd" xmlns:stk="http://www.stormware.cz/schema/version_2/stock.xsd" xmlns:ord="http://www.stormware.cz/schema/version_2/order.xsd" xmlns:ofr="http://www.stormware.cz/schema/version_2/offer.xsd" xmlns:enq="http://www.stormware.cz/schema/version_2/enquiry.xsd" xmlns:vyd="http://www.stormware.cz/schema/version_2/vydejka.xsd" xmlns:pri="http://www.stormware.cz/schema/version_2/prijemka.xsd" xmlns:bal="http://www.stormware.cz/schema/version_2/balance.xsd" xmlns:pre="http://www.stormware.cz/schema/version_2/prevodka.xsd" xmlns:vyr="http://www.stormware.cz/schema/version_2/vyroba.xsd" xmlns:pro="http://www.stormware.cz/schema/version_2/prodejka.xsd" xmlns:con="http://www.stormware.cz/schema/version_2/contract.xsd" xmlns:adb="http://www.stormware.cz/schema/version_2/addressbook.xsd" xmlns:prm="http://www.stormware.cz/schema/version_2/parameter.xsd" xmlns:lCon="http://www.stormware.cz/schema/version_2/list_contract.xsd" xmlns:ctg="http://www.stormware.cz/schema/version_2/category.xsd" xmlns:ipm="http://www.stormware.cz/schema/version_2/intParam.xsd" xmlns:str="http://www.stormware.cz/schema/version_2/storage.xsd" xmlns:idp="http://www.stormware.cz/schema/version_2/individualPrice.xsd" xmlns:sup="http://www.stormware.cz/schema/version_2/supplier.xsd" xmlns:prn="http://www.stormware.cz/schema/version_2/print.xsd" xmlns:lck="http://www.stormware.cz/schema/version_2/lock.xsd" xmlns:isd="http://www.stormware.cz/schema/version_2/isdoc.xsd" xmlns:sEET="http://www.stormware.cz/schema/version_2/sendEET.xsd" xmlns:act="http://www.stormware.cz/schema/version_2/accountancy.xsd" xmlns:bnk="http://www.stormware.cz/schema/version_2/bank.xsd" xmlns:sto="http://www.stormware.cz/schema/version_2/store.xsd" xmlns:grs="http://www.stormware.cz/schema/version_2/groupStocks.xsd" xmlns:acp="http://www.stormware.cz/schema/version_2/actionPrice.xsd" xmlns:csh="http://www.stormware.cz/schema/version_2/cashRegister.xsd" xmlns:bka="http://www.stormware.cz/schema/version_2/bankAccount.xsd" xmlns:ilt="http://www.stormware.cz/schema/version_2/inventoryLists.xsd" xmlns:nms="http://www.stormware.cz/schema/version_2/numericalSeries.xsd" xmlns:pay="http://www.stormware.cz/schema/version_2/payment.xsd" xmlns:mKasa="http://www.stormware.cz/schema/version_2/mKasa.xsd" xmlns:gdp="http://www.stormware.cz/schema/version_2/GDPR.xsd" xmlns:est="http://www.stormware.cz/schema/version_2/establishment.xsd" xmlns:cen="http://www.stormware.cz/schema/version_2/centre.xsd" xmlns:acv="http://www.stormware.cz/schema/version_2/activity.xsd" xmlns:afp="http://www.stormware.cz/schema/version_2/accountingFormOfPayment.xsd" xmlns:vat="http://www.stormware.cz/schema/version_2/classificationVAT.xsd" xmlns:rgn="http://www.stormware.cz/schema/version_2/registrationNumber.xsd" xmlns:ftr="http://www.stormware.cz/schema/version_2/filter.xsd" xmlns:asv="http://www.stormware.cz/schema/version_2/accountingSalesVouchers.xsd" xmlns:arch="http://www.stormware.cz/schema/version_2/archive.xsd" xmlns:req="http://www.stormware.cz/schema/version_2/productRequirement.xsd" xmlns:mov="http://www.stormware.cz/schema/version_2/movement.xsd" xmlns:rec="http://www.stormware.cz/schema/version_2/recyclingContrib.xsd" xmlns:srv="http://www.stormware.cz/schema/version_2/service.xsd" xmlns:rul="http://www.stormware.cz/schema/version_2/rulesPairing.xsd" xmlns:lwl="http://www.stormware.cz/schema/version_2/liquidationWithoutLink.xsd" xmlns:dis="http://www.stormware.cz/schema/version_2/discount.xsd" xmlns:lqd="http://www.stormware.cz/schema/version_2/automaticLiquidation.xsd">
                <int:intDocItem>
                    <int:text>{$text_detail}</int:text>
                    <int:quantity>1.0</int:quantity>
                    <int:coefficient>1.0</int:coefficient>
                    <int:payVAT>false</int:payVAT>
                    <int:rateVAT>none</int:rateVAT>
                    <int:discountPercentage>0.0</int:discountPercentage>
                    <int:homeCurrency>
                        <typ:unitPrice>{$total_price}</typ:unitPrice>
                        <typ:price>{$total_price}</typ:price>
                        <typ:priceVAT>0</typ:priceVAT>
                        <typ:priceSum>{$total_price}</typ:priceSum>
                    </int:homeCurrency>
                    <int:symPar>{$varsym}</int:symPar>
                    <int:PDP>false</int:PDP>
                    <int:parameters></int:parameters>
                </int:intDocItem>
            </int:intDocDetail>
        </int:intDoc>
    </dat:dataPackItem>
</dat:dataPack>
XML_DOC;

return $xml;
}

?>