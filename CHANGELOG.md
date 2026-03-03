## [25/11/2025] Version 4.1.0

Feature:

* Subdivision of the list of orders/labels to be processed during bulk label printing with the Mondial Relay API v2.

Fix:
* Fixed the display of the error message on the configuration verification page.

## [28/10/2025] Version 4.0.6

Feature:

* Added the ability to ignore an order status in the CRON task.
* Orders older than 3 months will not be processed in the CRON task.

## [16/10/2025] Version 4.0.5

Feature:

* Add PrestaShop Source in API 2.
* Add hookActionGetOrderShipments for easy communication with payment modules.

## [02/09/2025] Version 4.0.4

Fix:

* Make segmentation working without ps_account

## [28/08/2025] Version 4.0.3

Fix:

* Allow dot in city name for label generation for API 2.


## [11/08/2025] Version 4.0.2

Fix:

* Allow dot in city name for label generation.
* Fix update query in 3.6.1 upgrade file.
* Fix tables names in SQL query used in orderStatusUpdate task.


## [08/08/2025] Version 4.0.1

Feature:

* SegmentIo integration on install, uninstall, enable, disabled runUpgradeModule and beforeUpdateOptions functions

Fix:

* Fix API error 97 : using md5 instead of sha256.
* Fix cloudsync error.
* Fix cron task : check if address exist before delete.
* Remove developement depandancies.

## [08/07/2025] Version 4.0.0

Feature :

* Add compatibility for Prestashop 9
* Add cleaning feature for selected relay when order is delivered in orderStatusUpdate Cron task

## [04/06/2025] Version 3.6.2

Fix :

* Fix error when updating the module from a version higher than 3.3.0
* Fix error when changing widget mode
* Add France as default widget if the requested country is not compatible with Mondial Relay

## [14/05/2025] Version 3.6.1

Fix :

* Multiple UI fix

## [05/05/2025] Version 3.6.0

Feature :

* Add data sharing with Prestashop Eventbus

## [05/05/2025] Version 3.5.10

Fix :

* Open delivery for Poland
* Fix "Select a Point Relais" error when Point Relais is selected
* Increase prestashop version requirements for ps_account to 1.7.3.1
* Removal of require from home delivery field in account settings

## [27/02/2025] Version 3.5.9

Fix :

* Fix delete double API call on checkout and get data of Widget

## [11/02/2025] Version 3.5.8

Fix :

* Fix API validates connection using the form’s test mode before saving settings.

## [04/02/2025] Version 3.5.7

Fix :

* Fix missing AdminMondialrelayHelpController.

## [22/01/2025] Version 3.5.6

Fix : 

* Label generation issue on order view page.

## [22/01/2025] Version 3.5.5

Fix :

* Add 2 files in config folder for security.

## [20/01/2025] Version 3.5.4

Fix :

* Fix of the error create by namespace conflicts.

## [17/01/2025] Version 3.5.3

Fix :

* Fix of the compatibility error with PsAccount module for 1.7.0 to 1.7.3 versions.


## [07/01/2025] Version 3.5.2

Fix :

* Fix of the error create by trailing comma in GenerateLabelsActions.

## [20/12/2024] Version 3.5.1

Fix :

* Fix of the display of the language of the mondialrelay widget in relation to the language of the shop.

## [16/12/2024] Version 3.5.0

Feature :

* Add prestashop account module connection.

## [11/12/2024] Version 3.4.10

Fixes :

* Ability to select a relay after manually creating an order

## [09/12/2024] Version 3.4.9

Fixes :

* Fix label printing for Api 2 credentials

## [04/12/2024] Version 3.4.8

Fixes :

* Fix of the upgrade file to add the "24R" enum in the delivery mode parameters

## [22/11/2024] Version 3.4.7

Fixes :

* Fix of errors in some vendor files

## [24/10/2024] Version 3.4.6

Fixes :

* Prestashop Addons compliences, code structure, licence, htaccess ...

## [24/07/2024] Version 3.4.5

Fixes :

* Adjustement of the regex expedition restriction

## [09/07/2024] Version 3.4.4

Fixes :

* Fix error during install on php8 and prestashop 8 (SQLSTATE[42000] error)
* You can't generate labels for home delivery with api 1 credentials anymore
* Rename and add traduction about account fields

## [09/07/2024] Version 3.4.3

Fixes :

* Fix id_order null in selected_relay when order existing

## [08/07/2024] Version 3.4.2

Fixes and improvements:

* Fix JS - display relay point
* Fix admin order button was blocked
* Fix display admin order label generation button only for MR orders

## [20/05/2024] Version 3.4.1

Fixes and improvements:

* Fix error on print label for expedition lower than 8 number

## [20/05/2024] Version 3.4.0

Fixes and improvements:

* Add Hom delivery with the implements of API 2

## [10/10/2023] Version 3.3.12

Fixes and improvements:

* Fix delivery_mode which cause and error in relay widget

## [19/09/2023] Version 3.3.11

Fixes and improvements:

* DRI carriers has been deleted.
* Fix tracking url http link to https

## [11/08/2023] Version 3.3.10

Fixes and improvements:

* data sent to webservice to generate home delivery label

## [27/07/2023] Version 3.3.9

Fix:

* data used to generate label

## [17/07/2023] Version 3.3.8

Fix:

* change visibility of self called function

## [30/06/2023] Version 3.3.7

Fix:

* method to retrieves iso_code for tracking url

## [27/04/2023] Version 3.3.6

Fix:

* compiled js file

## [27/03/2023] Version 3.3.5

Fix:

- add autoload call to fix namespace issue on 3.3.0 upgrade

## [17/03/2023] Version 3.3.4

Fix:

- Fix a bad display of relay point because of php version

## [02/03/2023] Version 3.3.3

Fix:

- delivery_type property upgrade file


## Version 3.3.2

Fix:

- Resize logo into 50px max

## Version 3.3.1

Fix:

- Use relay image on home delivery option

## Version 3.3.0

Feature:

- Add new "Locker" carrier's mode

Fixes and improvements:

- Change logo to InPost/MondialRelay one

- Change shared main text

- Update translations

## Version 3.2.6

Fixes and improvements:

* Add tab translation

## Version 3.2.5

Fixes and improvements:

* Add a button to track the order on order details front page

## Version 3.2.4

Fixes and improvements:

* Prestashop 8.0 compatibility

## Version 3.2.3

Fixes and improvements:

- Fix compatibility with module Cdiscount by feedbiz

## Version 3.2.2

Fixes and improvements:

- take all country into acount during check webservice connection 

## Version 3.2.1

Fix

- Api url change

## Version 3.2.0

New feature:

- Add type of branding inPost and parameter on carrier
- Change display of the widget

## Version 3.1.6

Fixes and improvements:

- The orders from marketplaces are now correctly created.

## Version 3.1.5

Fixes and improvements:

- Remove the "COLLATE" parameter on the "CREATE TABLE" MySQL queries because it may cause errors with MySQL 8.x
