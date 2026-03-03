<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to 3.2.6
 * @param Mondialrelay $module
 * @return bool
 * @throws PrestaShopDatabaseException
 */
function upgrade_module_3_2_6($module)
{
    $admin_tabs = $module->moduleAdminControllers;
    foreach ($admin_tabs as $admin_tab) {
        $tab_instance = null;
        foreach ($admin_tab['name'] as $iso => $tab_name) {
            // Check if language is set
            if (!($id_language = Language::getIdByIso($iso))) {
                continue;
            }
            if ($tab_instance == null) {
                $tab_instance = Tab::getInstanceFromClassName($admin_tab['class_name']);
            }
            // Check if the language translation is already set on tab
            if (array_key_exists($id_language, $tab_instance->name) && $tab_instance->name[$id_language] == $tab_name) {
                continue;
            }
            $tab_instance->name[$id_language] = $tab_name;
        }
        if ($tab_instance != null) {
            $tab_instance->save();
        }
    }

    return true;
}
