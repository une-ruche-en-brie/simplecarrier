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
 * Upgrade to 3.6.1
 * @param Mondialrelay $module
 * @return bool
 * @throws PrestaShopDatabaseException
 */
function upgrade_module_3_6_1($module)
{
    $module->registerHook('displayOrderConfirmation');

    // Nouvelle configuration des contrôleurs
    $controllersNameUpdate = [
        'Carriers Settings' => 'Delivery settings',
        'Paramètres des transporteurs' => 'Paramètres des livraisons',
        'Parámetros de los transportistas' => 'Configuración de entrega',
        'Parameters van de vervoerders' => 'Levering Instellingen',
    ];

    // Connexion à la base de données
    $db = Db::getInstance();
    foreach ($controllersNameUpdate as $oldName => $newName) {
        $escapedPattern = Db::getInstance()->escape('%' . $oldName . '%', true);
        $db->update('tab_lang', ['name' => pSQL($newName)], "name LIKE '$escapedPattern'");
    }

    return true;
}
