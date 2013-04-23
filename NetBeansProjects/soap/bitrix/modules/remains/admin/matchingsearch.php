<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->isAdmin())  die();
CModule::IncludeModule('remains');
CModule::IncludeModule('iblock');
$matching = new matching();
$res = $matching->GetList(array($_REQUEST['by'] => $_REQUEST['sort']), array('ITEM_ID' => 0));
while ($el = $res->Fetch()) {
    if($_REQUEST['text']) 
        if(strpos($el['NAME'], $_REQUEST['text'])===false)
                continue;

    ?> <tr id="v2_<?= $el['ID']; ?>"><td>  
            <input type="radio" name="match" value="<?= $el['ID']; ?>">
        </td>
        <td> <?= $el['NAME']; ?></td>
        <td><?
    $res1 = CIBlockElement::GetByID($el["SUPPLIER_ID"]);
    if ($ar_res = $res1->GetNext())
        echo $ar_res['NAME'];
    ?></td>
        <td><button class="deleteBtn" data-id="<?= $el['ID']; ?>">Удалить</button>
        </td>
    </tr>  
<? }