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

require_once _PS_MODULE_DIR_ . '/mondialrelay/controllers/admin/AdminMondialrelayController.php';
require_once _PS_MODULE_DIR_ . '/mondialrelay/mondialrelay.php';

use MondialrelayClasslib\Install\ModuleInstaller;

class AdminMondialrelayHelpController extends AdminMondialrelayController
{
    /**
     * @see AdminController::setMedia()
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS($this->module->getPathUri() . '/views/js/admin/help.js');

        Media::addJsDef([
            'MONDIALRELAY_HELP' => [
                'helpUrl' => $this->context->link->getAdminLink('AdminMondialrelayHelp'),
            ],
        ]);
    }

    public function initContent()
    {
        $this->content .= $this->createTemplate('offers.tpl')->fetch();
        $this->content .= $this->createTemplate('setup_guide.tpl')->fetch();
        $this->content .= $this->createTemplate('quick_help.tpl')->fetch();
        $this->content .= $this->createTemplate('contact_us.tpl')->fetch();

        parent::initContent();
    }

    public function ajaxProcessCheckRequirements()
    {
        $errors = [];
        $confirmations = [];

        // Check dependencies
        if (!MondialrelayTools::checkDependencies()) {
            $errors[] = $this->module->l('SOAP and cURL should be installed on your server.', 'AdminMondialrelayHelpController');
        } else {
            $confirmations[] = $this->module->l('SOAP and cURL : OK', 'AdminMondialrelayHelpController');
        }

        // Check address
        $service = MondialrelayService::getService('Label_Generation');
        $service->checkExpeAddress();
        $addressErrors = $service->getErrors();

        if (!empty($addressErrors['generic'])) {
            $errorFormat = $this->module->l('Please kindly correct the following errors on the contact page :', 'AdminMondialrelayHelpController');
            foreach ($addressErrors['generic'] as $error) {
                $errors[] = sprintf("{$errorFormat} %s", $error);
            }
        } else {
            $confirmations[] = $this->module->l('Shop contact address : OK', 'AdminMondialrelayHelpController');
        }

        // Check hooks
        $missingHooks = MondialrelayTools::getModuleMissingHooks();
        if (!empty($missingHooks)) {
            $errorFormat = $this->module->l('The module is not registered with the following hooks : %s', 'AdminMondialrelayHelpController');
            $errors[] = sprintf($errorFormat, implode(', ', $missingHooks));
        } else {
            $confirmations[] = $this->module->l('Hooks installation : OK', 'AdminMondialrelayHelpController');
        }

        die(json_encode([
            'status' => empty($errors) ? 'success' : 'error',
            'error' => $errors,
            'confirmations' => $confirmations,
        ]));
    }


    public function ajaxProcessRegisterHooks()
    {
        $errors = [];
        $confirmations = [];
        $installer = new ModuleInstaller($this->module);
        try {
            if ($installer->registerHooks()) {
                $confirmations[] = $this->module->l('Hooks successfully registered.', 'AdminMondialrelayHelpController');
            } else {
                $errors[] = $this->module->l('An unknown error occurred while registering hooks.', 'AdminMondialrelayHelpController');
            }
        } catch (Exception $e) {
            $errors[] = $this->module->l('An error occurred while registering hooks : %error%', 'AdminMondialrelayHelpController', ['%error%' => $e->getMessage()]);
        }

        die(json_encode([
            'status' => empty($errors) ? 'success' : 'error',
            'error' => $errors,
            'confirmations' => $confirmations,
        ]));
    }
}
