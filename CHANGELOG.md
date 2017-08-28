# Changelog

## In development

* Fixed search input fields (now they work).
* Change max zoom-level on map search.
* Show "my-location" button.
* Added "Forgot your password" link to login page.

## v1.1.6

* Security update to drupal/core 8.3.7.
* Fixed empty price field in confirm step of create activity when no price was set.

## v1.1.5

* Fixed issues with activity create flows, where state was not submitted.

## v1.1.4

* SUPPORT-1159: Fixed bug on user display page

## v1.1.3

* Fixed translation of "Register" under "node/{nid}".

## v1.1.2

* Fixed issue with owner check on clone activity.

## v1.1.1

* SUPPORT-1145: Fixed render errors on create activity forms
* Updated core to 8.3.5
* Updated contrib modules

## 1.1.0

@TODO

## v1.0.7

* Disabled "Internal Page Cache" module (it always caches pages for anonymous users)
* Disabled views caching.

## v1.0.6

* Changed views caching to time based.

## v1.0.5
* Removed module missing errors
* Updated drupal core
* Updated contrib modules
* Removed missing modules error
* Added specific textfilter for page
* Fixed pagination issue

## v1.0.4

* Added icon checkmark to info sections and fixed styling if there is no sub text.
* Changed icon on user profile.
* Changed icon in attendees section on activity.
* Hide headers if there are no results on user and activity pages. 
* Removed hover on upload image so the upload button is visible.
* Fix location button in IE 11.
* Fixed question mark icon.
* Added text to last step of create activity if no image is selected.
* Added drush translation commands to platform build.
* Added button to create activity for anonymous users.
* Added redirect for anonymous users for access denied page.
* Removed help text for text area fields

## v1.0.3

* Changed activities on frontpage to show upcoming instead of recent activities.
* Added link to email in Floating Help.
* Fixed Javascript error in Safari (ecmascript 5 - const keyword in twig library).
* Added list to activity with signed up users.
* Removed price and participants from activity teaser.
* Fixed zip code auto-fill area to work with https.
* Fixed issue with missing link value in info sections content form.
* Added danish language and translations

## v1.0.2

* Changed site name.
* Removed number of registered users when not owner of activity.
* Removed title from "Sign up required", from activity create.
* Fixed helpbox safari 9x.
* Fixed non-translatable string in mail subject when contacting activity owner.

## v1.0.1

* Fixed a bug where the password could not be changed.

## v1.0.0

* Release 1.0.0.

## v1.0.0-beta5

* Removed empty radio button from activity edit page.
* Fixed categories for activities, so that activities did not reference term with tid = 0.
* Fixed confirm page in activity create so values are not keys to entities.
* Removed error messages from activity map when geocoder could not handle address.
* Added contact activity owner form.
* Changed block header text in footer.
* Added 403 page: /ingen-adgang.
* Removed containing div of region-top from page_manager with a patch.

## v1.0.0-beta4

* Added adfs button.

## v1.0.0-beta3

* Fixed translation issues with register page, and activity view.
* Added Webmaster role and changed Editor permissions.
* Fixed styling of cookie message and link.
* Added contact form to activities.
* Fixed styling of page.
* Enabled Metatag: Open Graph module and added tags.
* Enabled search filters on activity map.
* Added adfs login module for testing.

## v1.0.0-beta2

* Fixed translation of labels in activity sidebar.
* Fixed category filter to not show duplicates.
* Ignores errors from geocoder calls.
* Fixed conditions for when filters should be shown.
* Added that user should be logged in to see "create activity" button in hero.

## v1.0.0-beta1

* Added fallback image to activities.
* Added buttons to edit, clone and delete own activities.
* Added cookie message popup.
* Added map for activities listing page.
* Fixed ellipsis text.
* Fixed create activity flow.
* Fixed activities page.
* Fixed translation issues.
* Cleaned up activity urls.

## v1.0.0-alpha6

* Added ellipsis for activity teaser heading text.
* Added permission for creating activity.

## v1.0.0-alpha5

* Fixed pagination arrows.
* Fixed /activity block, for searching through activities.
* Added free text search.
* Fixed filters styling.
* Addes sorting by next activity.
* Fixed checkbox styling.
* Added "Free" text when price it not set or set to 0.

## v1.0.0-alpha4

* Added register for external users.
* Fixed theme negotiation to edit pages, but not activities.
* Added js Time field widget in activity multistep.
* Added share-by-email for activities.
* Fixed user pages.
* Added "my activities" and "my registered activities" to /user/* pages.
* Fixed format of date display.
* Removed PRLP.

## v1.0.0-alpha3

* Added multistep activity form.
* Added facebook create/login user.
* Fixed grids.
* Added facebook share activity.

## v1.0.0-alpha2

* Drupal connection to pattern-lab.
* Pattern-lab cleanup.

## v1.0.0-alpha1

* Development environment.
* platform.sh setup.
