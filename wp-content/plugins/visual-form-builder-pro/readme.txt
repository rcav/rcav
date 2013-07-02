=== Visual Form Builder Pro ===
Contributors: mmuro
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 2.3.4

Visual Form Builder Pro is an affordable WordPress plugin that helps you build beautiful, fully functional forms in only a few minutes without writing PHP, CSS, or HTML.

== Release Notes ==

**Version 2.3.4 — Jun 28, 2013**

* Add dateFormat option for Date fields
* Add admin blue styles
* Add vfb_address_labels_placement filter
* Add Czech language
* Add Malaysian Ringgit and Turkish Lira currency to PayPal settings
* Update form delete process to now delete all collected entries for that form
* Update CSS with more default styles to override potential theme problems
* Update form saving to check for max_input_vars and display error message
* Update saving field name, description, options, and default value to trim whitespace
* Update Legend output to only display bottom border when text is available
* Update PayPal assigned prices in admin to escape values properly
* Fix bug on Delete link for form order view
* Fix form list bulk delete
* Fix server side validation for hidden and required conditional fields
* Fix bug where the Hidden field Post URL was not being properly saved
* Fix bug where Payments running total box would not appear if hidden by conditional logic
* Remove old "Display Forms" help image and just use text


**Version 2.3.3 — Jun 06, 2013**

* Add Print button to Entry Detail view
* Add Duplicate link to Form List view
* Add vfb_skip_email email filter which allows email to be skipped
* Add meta keyword for plugin version number
* Add Form Designer and Payments add-ons to export/import
* Add additional language transaltions
* Add Display Entry option to Confirmation section
* Update HTML field to use CKEditor instead of Quicktags
* Update CSS to include :focus styles
* Update Entry Detail to link File Uploads
* Update color picker JS to use included file instead of wp-admin version
* Update vfb_submissions_off_message filter to include entries count and entries allowed
* Update conditional logic and form output to use simpler IDs
* Fix bug during Export for duplicate field names
* Fix bug on Export page where Page to Export option may not appear
* Fix bug on Export page where Fields were not limited to 1000 in an edge case
* Fix bug where details would not display when updating Entry
* Fix bug where delete link did not work in Form List view
* Fix bug where Instructions data would not appear in email when included in vfb_removed_field_types


**Version 2.3.2 — May 08, 2013**

* Update server-side validation check for required conditional fields that may be hidden
* Fix bug where referer URL did not match domains that prepended www
* Fix Form Items z-index when scrolling
* Fix bug where sidebar width would expand when scrolled and an item was dropped onto the form

**Version 2.3.1 — May 07, 2013**

* Update form migration design
* Update required field server-side validation to display field name
* Fix bug where referer URL was not compatible with certain permalink structures

**Version 2.3 — May 05, 2013**

* Add additional View link on Entries instead of replacing Edit link based on permissions
* Add forms searching to admin
* Add vfb_form_success_message, vfb_before_form_output, vfb_after_form_output, vfb_notify_message, vfb_removed_field_types filters
* Add Post/Page URL to Hidden Field
* Add HTTP Referer security check
* Update Forms List design
* Update forms type switching (now under forms search box)
* Update behavior to allow deselecting Default values on Select/Radio/Checkbox options
* Update dashboard widget to only display if user has view/edit entries capabilities
* Update style of \"Add Form\" button above post/page visual editor
* Update Numbers field to allow either Digits or Number validation and sanitize as float instead of int
* Update languages
* Update validation to check if required fields are blank
* Fix bug in languages for quoted strings
* Fix errant closing span output on min/max/range fields
* Fix bug during Export for certain encoded characters

**Version 2.2.6 — Apr 26, 2013**

* Add additional View link on Entries instead of replacing Edit link based on permissions
* Update behavior to allow deselecting Default values on Select/Radio/Checkbox options
* Fix bug for Entries when using translations

**Version 2.2.5 — Apr 18, 2013**

* Add over 20 language translations
* Add customizable Header Text to email design
* Add 'Pages to Export' option when more than 1000 entries detected for a single form
* Add vfb_redirect_query_args filter to allow custom query arguments to be appended to redirects during Confirmation
* Add logged on user_id to entries. Default is always 1.
* Add more private custom validation methods
* Add vfb_pre_get_entries_formlist filter for Entries months dropdown
* Update and normalize Entries list queries
* Update Export when large amount of fields are present
* Update color pickers in email design to new Iris picker
* Update admin to require WordPress 3.5
* Update Entries Display to only print text instead of options when user does not have edit permissions
* Update email function to wrap lines longer than 70 words to meet email standards
* Allow Hidden field to use vfb_field_default filter
* Allow links to be used in Radio and Checkbox options
* Fix missing akismet and entry_approved during the XML export
* Fix minor output escaping on Conditional Logic screen
* Fix bug affecting radio button Allow Other not being sent properly

**Version 2.2.4 — Mar 25, 2013**

* Add Entry ID to entries list
* Add entry details Print stylesheet
* Add filters for Submissions Off and Unique Entries only messages
* Add right-to-left CSS styles in admin and front-end
* Fix bug where scheduling may throw an error
* Fix bug for conditional logic values to handle quotes
* Fix bug where Checkbox/Select/Radio options were not properly escaped
* Update entries detail 'Delete' to 'Trash' method
* Update saving to redirect when duplicating forms
* Update export to not interfere with WordPress export when selecting 'All'
* Update checks for Create Post add-on
* Display help message when no PayPal fields are detected

**Version 2.2.3 — Mar 20, 2013**

* Fix bug where email header image was not being saved properly
* Fix bug where templating may not work in certain cases
* Fix bug where word count went missing
* Update CSS to override some TwentyTwelve styles
* Minor update for Payments add-on

**Version 2.2.2 — Mar 12, 2013**

* Add message asking to save before navigation away from page
* Add links to toolbar for Display Entries and Form Design add-ons, if active
* Update icons
* Update some AJAX calls to improve reliability
* Fix bug affecting loading entries fields in Export in some cases
* Fix bug affecting field keys with unicode characters
* Fix bug so only trashed and spammed entires are not exported
* Rollback HTML5 date type to text field to prevent duplicate date pickers in Chrome

**Version 2.2.1 — Mar 02, 2013**

* Add dashboard widget for recent entries
* Fix minor bug in Excel export
* Fix bug affecting tooltips
* Fix bug where left/right aligned labels and content were not displaying correctly
* Fix bug where usernames were not forced to be lowercase or validated against such
* Fix bug affecting links in descriptions

**Version 2.2 — Feb 21, 2013**

* Add a 'Allow only unique entries per IP' feature
* Add Reply-To to email headers for better compatibility with some email servers
* Add new Fields selection in Export
* Add 'View' link to Spam'd entries
* Add vfb_skip_empty_fields filter to allow empty fields to be skipped in the email and DB saving
* Add vfb_override_email and vfb_override_email_notify variable filters to allow custom email function
* Add vfb_field_default filter
* Add DONOTCACHEPAGE constant to fix occasional nonce errors for caching plugin users
* Add templating to notification message
* Add saving image animation to conditional logic
* Add error message to conditional logic saving if database error
* Update CSV export to be more reliable
* Update Excel export to be more reliable
* Update certain input field types to HTML5 input types
* Update vfb_address_labels filter to allow control over Address field
* Update PayPal redirect to use wp_redirect instead of jQuery
* Update Display Forms metabox to include Shortcode and Template Tag
* Update Form Preview to now be fully functional
* Update conditional logic saving to keep thickbox displayed until manually dismissed
* Update default value in Name field to try and break apart first/last names automatically
* Update Paging field to force required or invalid fields to be completed before continuing
* Update widget and template tag to render shortcode instead of calling form output directly
* Sanitize IP address before inserting into database
* Fix bug when saving entries
* Fix bug where Address field sanitization stripped break tags
* Fix bug in Instructions description where HTML tags were encoded in admin
* Fix bug preventing array'd form items from being using in templating
* Fix bug that allowed validation dropdown to be active in certain predefined fields
* Fix bug for misnamed Instructions CSS class
* Deprecate use of CDN for certain files in favor of locally hosted versions
* Deprecate Export Selected in favor of more reliable exporting on the Export screen

**Version 2.1.2 — Jan 21, 2013**

* Fix API call affecting WordPress plugins screen (could take 12-24 hours to resolve)
* Properly load i18n file with plugins_loaded action

**Version 2.1.1 — Jan 15, 2013**

* Fix bug where some server PHP configurations do not properly check method_exists and can crash forms

**Version 2.1 — Jan 14, 2013**

* Add Akismet support
* Add Merge Tag screen option to help with templating
* Add more actions to email and confirmation functions
* Add integration support for new Add-Ons
* Update Analytics charts
* Update Analytics to include a Date filter
* Update Add New page, require Email details
* Update query for getting form data
* Update All Forms meta boxes link layout
* Update some JS files to local files instead of CDN
* Update Help tab to mirror Documentation on website; display on all pages now
* Register styles before enqueuing
* Properly hook update DB and SQL install to plugins_loaded action
* Increase size of field_rule from TEXT to LONGTEXT
* Fix Bulk Add when deleting options and/or Allow Other
* Fix bug for some form item descriptions where HTML tags were not encoded on display
* Fix bug for incorrect capability check on Import and Export pages
* Fix bug when duplicating forms when conditional fields are present

**Version 2.0.1 — Dec 05, 2012**

* Add filter for removing attachments from email
* Update email headers
* Fix bug where notification email did not send
* Fix textarea value formatting in HTML email

**Version 2.0 — Dec 03, 2012**

* Add Entries Allowed feature
* Add Form Schedule feature
* Add Duplicate Field feature
* Add Name field
* Add Other text input option to Radio field
* Add Word Count feature to Textarea field
* Add Tab Delimited option to Export
* Add CSS Class option to Submit button
* Add confirmation box to delete field
* Add more sanitization to form inputs
* Add new filters: Address labels, prepend confirmation message, CSV delimiter, word count message
* Update some filters to now include form ID
* Update jQuery UI CSS to pull locally instead of CDN
* Update first fieldset warning and output a more noticeable error
* Update tooltip CSS
* Update design of field item action links
* Fix bug where paragraph tags were added to Textarea in Plain Text emails
* Fix placeholder size when creating a new form item by dragging
* Fix media button to use correct action
* Fix mismatched translation strings

**Version 1.9.2 — Nov 08, 2012**

* Add widget
* Update CSS to now prefix all classes to help eliminate theme conflicts
* Update email function to force a From email that exists on the same domain
* Update form/email previews to better anticipate where wp-load.php is going to be
* Update Email Designer and Analytics pages to check if forms exist before outputting content
* Fix bug affecting File Upload field validation
* Fix bug where inline form preview would not be visible if switching to third column layout
* Fix database install to use PRIMARY KEY instead of UNIQUE KEY
* Fix bug where JS may not work in IE
* Minor code cleanups

**Version 1.9.1 — Nov 07, 2012**

* Add filter to let saving an entry optional
* Update Form Preview with new JS and CSS
* Fix bug where notification name was not being reset
* Fix bug removing errant console.log from JS
* Fix forms with the File Upload field by adding the accept() method back to the JS file
* Fix bug where form subject and title was not being escaped in the email/form preview
* Try to suppress getimagesize errors for some servers
* Fix bug where long sender emails were not saved properly in the entries table
* Fix bug where error messages were not printed during import

**Version 1.9 — Oct 16, 2012**

* Add new Conditional Logic feature
* Add new Templating feature to subjects and confirmation messages
* Add Bulk Add filter for custom lists
* Add action vfb_after_email
* Add server-side input sanitization
* Add line breaks to textarea values in email/entries
* Add new template tag function and a template action
* Update forms listing to now sort by alphabetical order
* Update JavaScripts to now pull from Microsoft AJAX instead of Google and use SSL
* Update email function to no longer use mail_header filters
* Fix username validation to match WordPress requirements
* Fix bug where a single form export would always force the first form
* Fix bug where form export would fail in Safari
* Fix NetworkError 404 that appears when viewing the Form Preview

**Version 1.8 — Aug 08, 2012**

* Add new Live Form Preview
* Add new All Forms box listing with drag and drop reordering
* Add new New Form screen
* Add new Quick Switch form selector
* Add customizable columns to admin form builder (see Screen Options tab)
* Update meta boxes to be reordered or hidden (see Screen Options tab)
* Update and clean up entry form design
* Fix bug where form rendering would behave erratically in Internet Explorer 9
* Fix bug where saving an entry in details view would redirect back out to the list view
* Minor admin CSS and JS updates

**Version 1.7.4 — Jul 24, 2012**

* Fix bug where verification would validate, whether it was set to display or not

**Version 1.7.3 — Jul 24, 2012**

* Fix bug where jQuery wasn't included for PayPal redirect
* Fix error during plain text email send

**Version 1.7.2 — Jul 19, 2012**

* Fix bug for items with duplicate ID attributes
* Fix bug where Date Picker would not select a date
* Fix misspelled function name in upgrade function
* Fix bug where HTML buttons would not be displayed
* Fix bug where images less than 600px would not be uploaded in the email design

**Version 1.7.1 — Jul 18, 2012**

* Fix bug where PayPal fields were not being set

**Version 1.7 — Jul 18, 2012**

* Add new Import and Export pages
* Add new capabilities for both import and export
* Add new VFB Pro menu to the WordPress admin toolbar
* Add IDs to each item
* Deprecate Export All from Entries Bulk Actions (to export entire forms, see new Export page)
* Update name attribute to remove field key in attempts to prevent POST limit from reaching max memory
* Update server side validation to check for required fields
* Update server side validation messages to denote which field is failing
* Fix bug where form name override was not being updated when copying a form
* Fix bug where address formatting broke in the email
* Fix bug where click-to-add form item didn't properly place at the bottom
* Minor admin CSS update

**Version 1.6.1 — Jun 27, 2012**

* Fix Add Form media button
* Update JavaScript files to only load on pages that include the shortcode
* Minor admin layout fixes

**Version 1.6 — Jun 06, 2012**

* Add sticky scroll to Form Items sidebar
* Add collapse ability to Form Items and Form Output boxes
* Add Bulk Add Options feature
* Add Header Image option to Email Design
* Fix minor bugs
* Update spam bot check to only execute when form is submitted
* Update list of spam bots
* Update entries data field from TEXT to LONGTEXT
* Update media button to now use AJAX instead of hidden HTML in footer
* Update Next Page behavior to autoscroll to the next fieldset

**Version 1.5 — May 18, 2012**

* Add ability to turn off the spam Verification section
* Add custom capabilities for user roles
* Add various filters
* Add nag message if free version of Visual Form Builder is detected and still active
* Fix bug in Analytics and Email Design where the initial form might not display
* Fix bug where certain rows in the email would not use the alt row color
* Fix bug for plain text email formatting
* Fix bug where notification email would send as HTML even if plain text was selected
* Update subnav to accommodate new custom capabilities
* Update list of spam bots
* Update spam bot check to only execute when form is submitted

**Version 1.4.1 — Apr 27, 2012**

* Fix bug where Export feature was broken
* Fix bug where server validation failed on certain data types
* Add months drop down filter to Entries list

**Version 1.4 — Apr 24, 2012**

* Add media button to Posts/Pages to easily embed forms
* Add search feature to Entries
* Add Notes field to Entries detail
* Add Default Value option to fields
* Add Default Country option to Address block
* Fix bug where Plain Text emails would not send
* Fix bug where Required option was not being set on File Upload fields
* Fix bug where Form Name was not required on Add New page
* Update plugin menus to be added the "right" way
* Update and optimize Entries query
* Update menu icon to custom form icon (thanks to Paul Armstrong Designs!)
* Update Security Check messages to be more verbose
* Update email formatting to add line breaks
* Update how the entries files are included to eliminate PHP notices
* Update output to warn users of a missing fieldset if not at the beginning
* Minor updates to CSS

**Version 1.3.1 — Mar 29, 2012**

* Fix bug that prevented URL field from passing server side validation
* Updated translation file

**Version 1.3 — Mar 22, 2012**

* Add Drag and Drop ability to add Form Items
* Add Plain Text email design option
* Add Additional Footer Text option
* Add option to remove footer link back
* Add Label Alignment option
* Add server side form validation; SPAM hardening
* Add inline Field help tooltip popups
* Update Form Settings UI
* Update File Upload field to place attachments in Media Library
* Update Field Description to allow HTML tags
* Update Field Name and CSS Classes to enforce a maxlength of 255 characters
* Fix bug preventing form deletion
* Fix bug preventing Custom Static Variable in Hidden Field
* Fix bug where Verification and Secret fields were displayed on Entries Detail page

**Version 1.2 — Mar 05, 2012**

* Add Accepts option to File Upload field
* Add Small size to field options
* Add Options Layout to Radio and Checkbox fields
* Add Field Layout to field options
* Update jQuery in admin
* Verification fields now customizable
* Verification field now can be set to not required

**Version 1.1.1 — Feb 22, 2012**

* Fix bug where adding fields was broken

**Version 1.1 — Feb 20, 2012**

* Fix bug where assigning a price to PayPal did not save
* Minor updates to CSS
* Minor updates to database structure

**Version 1.0 — Feb 07, 2012**

* 10 new Form Fields (Username, Password, Color Picker, Autocomplete, and more)
* Edit and Update Entries
* Quality HTML Email Template
* Email Designer
* Analytics
* Data & Form Migration
* PayPal Integration
* Form Paging
* No License Key
* Unlimited Use
* Automatic Updates