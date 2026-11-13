# v0.5.0
## 11/13/2026

1. [](#new)
    * Support Plausible's site-specific tracking script URLs introduced in October 2025.

2. [](#improved)
    * Preserve legacy script URL generation for existing and older self-hosted installations.
    * Mark the custom-event and outbound-link options as legacy compatibility settings.
    * Add a Modern/Legacy selector that dynamically shows only the relevant settings.
    * Output the site-specific Modern tag or generated Legacy tag based on the selected mode.
    * Initialize the Modern script with the inline `window.plausible` queue shim, `plausible.init` fallback, and `plausible.init()` call; leave Legacy mode unchanged.
    * Update installation instructions for Plausible's current Site Installation workflow.

3. [](#bugfix)
    * Allow saving either tracking mode in Admin Classic when the inactive mode's required value is blank.

# v0.4.0
## 09/15/2026

1. [](#improved)
    * Add Grav 2.0 and Admin Next compatibility while retaining Grav 1.7 support.
    * Register frontend events in a way that works with the Grav 2.0 request lifecycle.
    * Add ICU translation namespaces for Admin Next.
    * Raise the minimum supported PHP version to 7.3.6.

# v0.3.4
## 09/05/2022

1. [](#bugfix)
    * Wrapped `Yes` and `No` in quotes. Fixes #5. 

2. [](#improved)
    * All fields in admin UI now translate properly.

# v0.3.3
## 09/05/2022

1. [](#bugfix)
    * Fix incorrect keys in `languages.yaml`

2. [](#improved)
    * Add boolean validation to `blueprints.yaml`
    * Fix 301 redirects to plausible docs in `languages.yaml`

# v0.3.2
## 08/01/2021

1. [](#bugfix)
    * Fix self hosted configuration variables (thanks @markatk)

# v0.3.1
## 07/20/2021

1. [](#bugfix)
    * Fix early return if custom domain not set
    * Fix wrong parameter name

2. [](#improved)
    * Remove dead code

# v0.3.0
##  07/20/2021

1. [](#new)
    * Allow for self-hosted/custom domain configuration (thanks @mascali33 and @rootarch)

2. [](#improved)
    * Tidied up languages.yaml

# v0.2.4
##  05/15/2021

1. [](#bugfix)
    * Fix languages.yaml syntax (thanks @mascali33)

# v0.2.3
##  01/13/2021

1. [](#improved)
    * Added translations from [DeepL](https://www.deepl.com/en/pro-license#free)

# v0.2.2
##  11/22/2020

1. [](#improved)
    * Updated README to refer to Shortcode Assets plugin

# v0.2.1
##  11/21/2020

1. [](#improved)
    * Updated README
# v0.2.0
##  11/21/2020

1. [](#improved)
    * Added `custom_event_goals` configuration.
    * Added `outbound_link_tracking` configuration.
    * Added configuration RE linking to a public dashboard.

# v0.1.0
##  11/20/2020

1. [](#new)
    * Plugin started...
