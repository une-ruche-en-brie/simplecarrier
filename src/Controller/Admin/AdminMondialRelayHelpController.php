<?php
/*
 * This file is part of Simple Carrier module
 *
 * Copyright(c) Nicolas Roudaire  https://www.une-ruche-en-brie.fr/
 * Licensed under the OSL version 3.0 license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MondialRelay\MondialRelay\Controller\Admin;

use Context;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class AdminMondialRelayHelpController extends PrestaShopAdminController
{
    public function indexAction(RouterInterface $router): Response
    {
        $context = Context::getContext();

        $tpl_params = [];
        $tpl_params['prestashop_performance_url'] = $router->generate('admin_performance');
        $tpl_params['logs_link'] = $router->generate('admin_performance');

        $tpl_params['account_settings_link'] = $context->link->getAdminLink('AdminMondialrelayAccountSettings');
        $tpl_params['advanced_settings_link'] = $context->link->getAdminLink('AdminMondialrelayAdvancedSettings');
        $tpl_params['mondialrelay_carrier_settings_link'] = $context->link->getAdminLink('AdminMondialrelayCarriersSettings');
        $tpl_params['prestashop_carrier_settings_link'] = $context->link->getAdminLink('AdminCarriers');
        $tpl_params['store_contact_link'] = $context->link->getAdminLink('AdminStores') . '#store_fieldset_contact';

        return $this->render('@Modules/mondialrelay/views/templates/admin/mondialrelay_help/index.html.twig', $tpl_params);
    }
}
