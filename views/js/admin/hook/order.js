/*!
 * NOTICE OF LICENSE
 * 
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */!function(e){var r={};function t(n){if(r[n])return r[n].exports;var o=r[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,t),o.l=!0,o.exports}t.m=e,t.c=r,t.d=function(e,r,n){t.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:n})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,r){if(1&r&&(e=t(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(t.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var o in e)t.d(n,o,function(r){return e[r]}.bind(null,o));return n},t.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(r,"a",r),r},t.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},t.p="",t(t.s=7)}({7:function(e,r){
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
$(document).ready((function(){document.querySelector("#generateMrLabel")&&document.querySelector("#generateMrLabel").addEventListener("click",(function(e){document.querySelector("#generateMrLabel").disabled=!0,console.log(document.querySelector("#generateMrLabel").dataset.relay);var r={ajax:!0,action:"generateLabel",id_mondialrelay_selected_relay:document.querySelector("#generateMrLabel").dataset.relay};$.ajax({url:document.querySelector("#mondialrelay-action").value,method:"GET",data:r,dataType:"json",success:function(e){console.log(document.querySelector(".mondialrelay-body")),document.querySelector(".mondialrelay-body").insertAdjacentHTML("beforeend",e.responseText)},error:function(e){console.log(e.responseText),document.querySelector(".mondialrelay-body").insertAdjacentHTML("beforeend",e.responseText)}})}))}))}});