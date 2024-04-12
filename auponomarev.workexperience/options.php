<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}
/**
 * @var CMain $APPLICATION
 * @var string $REQUEST_METHOD
 * @var string $RestoreDefaults
 * @var string $Update
 * @var string $mid
 */

$module_id = "auponomarev.workexperience";
IncludeModuleLangFile(__FILE__);
$SUP_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($REQUEST_METHOD == "GET" && $SUP_RIGHT >= "W" && $RestoreDefaults <> '' && check_bitrix_sessid()) {
    COption::RemoveOption($module_id);
}

if ($REQUEST_METHOD == "POST" && $SUP_RIGHT >= "W" && $Update <> '' && check_bitrix_sessid()) {
    COption::SetOptionString($module_id, "assignUser", $_POST['assignUser']);
    COption::SetOptionString($module_id, "Iblock_id", $_POST['Iblock_id']);
    COption::SetOptionString($module_id, "beforeDays", $_POST['beforeDays']);
    COption::SetOptionString($module_id, "checkDay", $_POST['checkDay']);
    COption::SetOptionString($module_id, "workexperiencefieldid", $_POST['workexperiencefieldid']);
}

$assignUser = COption::GetOptionString($module_id, "assignUser");
$Iblock_id = COption::GetOptionString($module_id, "Iblock_id");
$beforeDays = COption::GetOptionString($module_id, "beforeDays");
$checkDay = COption::GetOptionString($module_id, "checkDay");
$workexperiencefieldid = COption::GetOptionString($module_id, "workexperiencefieldid");

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
    array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin(); ?>
<form method="POST"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>">
    <?= bitrix_sessid_post() ?>
    <?php
    $tabControl->BeginNextTab(); ?>
    <tr>
        <td style="vertical-align:top; width:50%;"><?= GetMessage("auponomarev.workexperience_assignUser") ?></td>
        <td style="vertical-align:top; width:50%;">
            <input type="text" size="40" value="<?= htmlspecialcharsbx($assignUser) ?>" name="assignUser">
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top; width:50%;"><?= GetMessage("auponomarev.workexperience_Iblock_id") ?></td>
        <td style="vertical-align:top; width:50%;">
            <input type="text" size="40" value="<?= htmlspecialcharsbx($Iblock_id) ?>" name="Iblock_id">
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top; width:50%;"><?= GetMessage("auponomarev.workexperience_beforeDays") ?></td>
        <td style="vertical-align:top; width:50%;">
            <input type="text" size="40" value="<?= htmlspecialcharsbx($beforeDays) ?>" name="beforeDays">
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top; width:50%;"><?= GetMessage("auponomarev.workexperience_checkDay") ?></td>
        <td style="vertical-align:top; width:50%;">
            <input type="text" size="40" value="<?= htmlspecialcharsbx($checkDay) ?>" name="checkDay">
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top; width:50%;"><?= GetMessage("auponomarev.workexperience_workexperiencefieldid") ?></td>
        <td style="vertical-align:top; width:50%;">
            <input type="text" size="40" value="<?= htmlspecialcharsbx($workexperiencefieldid) ?>" name="workexperiencefieldid">
        </td>
    </tr>
    
    <?php
    $tabControl->BeginNextTab(); ?>
    <?php
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php"); ?>
    <?php
    $tabControl->Buttons(); ?>
    <script language="JavaScript">
        function RestoreDefaults() {
            if (confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                window.location = "<?= $APPLICATION->GetCurPage(
                )?>?RestoreDefaults=Y&lang=<?= LANG?>&mid=<?= urlencode($mid)?>&<?= bitrix_sessid_get()?>";
        }
    </script>
    <?php if($SUP_RIGHT < "W"): ?>
    
    <?php else: ?>
    <input type="submit" name="Update"
           value="<?= GetMessage("auponomarev.workexperience_SUP_SAVE") ?>">
    <input type="hidden" name="Update" value="Y">
    <input type="button" OnClick="RestoreDefaults();"
        value="<?= GetMessage("auponomarev.workexperience_RESTORE_DEFAULTS") ?>"
    >
    <?php endif; ?>
    <?php
    $tabControl->End(); ?>
</form>