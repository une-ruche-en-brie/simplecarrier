/*!
 * NOTICE OF LICENSE
 * 
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */!function(e){var n={};function t(o){if(n[o])return n[o].exports;var r=n[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,t),r.l=!0,r.exports}t.m=e,t.c=n,t.d=function(e,n,o){t.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:o})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,n){if(1&n&&(e=t(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(t.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var r in e)t.d(o,r,function(n){return e[n]}.bind(null,r));return o},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},t.p="",t(t.s=5)}({1:function(e,n,t){"use strict";t.d(n,"c",(function(){return o})),t.d(n,"d",(function(){return r})),t.d(n,"a",(function(){return c})),t.d(n,"b",(function(){return a}));
/**
 * NOTICE OF LICENSE
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
var o=function(e){$(e||"#ajaxBox").fadeOut((function(){$(this).empty()}))},r=function(e){$(e||"#ajaxBox").fadeIn()},c=function(e,n){$(n||"#ajaxBox").queue((function(){$(this).append($("#ajax_confirmation").clone().attr("id",null).removeClass("hide alert-success").addClass("alert-danger").html(e)),$(this).dequeue()}))},a=function(e,n){e.confirmations.forEach((function(e){!function(e,n){$(n||"#ajaxBox").queue((function(){$(this).append($("#ajax_confirmation").clone().attr("id",null).removeClass("hide").html(e)),$(this).dequeue()}))}(e,n)})),e.error.forEach((function(e){c(e,n)}))}},5:function(e,n,t){"use strict";t.r(n);var o=t(1),r=MONDIALRELAY_ACCOUNTSETTINGS,c=MONDIALRELAY_ACCOUNTSETTINGS_API2;
/**
 * NOTICE OF LICENSE
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */$(document).on("click","#mondialrelay_check-connection",(function(){var e=this,n=e.form,t={ajax:!0,action:"checkConnection"};r.checkConnectionFields.forEach((function(e){t[e]=$(n).find('[name="'.concat(e,'"]')).val()})),o.c(),e.disabled=!0,$.ajax({url:r.accountSettingsUrl,method:"POST",data:t,dataType:"json",success:function(e){o.b(e),o.d()},error:function(e){o.a(MONDIALRELAY_MESSAGES.unknown_error),o.d()},complete:function(){e.disabled=!1}})})),$(document).on("click","#mondialrelay_check-connection-api2",(function(){var e=this,n=e.form,t={ajax:!0,action:"checkConnectionApi2"};c.checkConnectionFields.forEach((function(e){t[e]=$(n).find('[name="'.concat(e,'"]')).val()})),o.c(),e.disabled=!0,$.ajax({url:c.accountSettingsUrl,method:"POST",data:t,dataType:"json",success:function(e){o.b(e),o.d()},error:function(e){o.a(MONDIALRELAY_MESSAGES.unknown_error),o.d()},complete:function(){e.disabled=!1}})})),$(document).ready((function(){document.querySelector(".mondialrelay_home_delivery").addEventListener("change",(function(e){1==document.querySelector(".mondialrelay_home_delivery").value?document.querySelectorAll(".api2_form").forEach((function(e){e.parentNode.parentNode.parentNode.classList.remove("hide")})):document.querySelectorAll(".api2_form").forEach((function(e){e.parentNode.parentNode.parentNode.classList.add("hide")}))})),1==document.querySelector(".mondialrelay_home_delivery").value?document.querySelectorAll(".api2_form").forEach((function(e){e.parentNode.parentNode.parentNode.classList.remove("hide")})):document.querySelectorAll(".api2_form").forEach((function(e){e.parentNode.parentNode.parentNode.classList.add("hide")}))}))}});