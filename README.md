# Plausible Analytics Plugin

The **Plausible Analytics** Plugin is an extension for [Grav CMS](http://github.com/getgrav/grav). Integrate Plausible Analytics into your Grav site and configure it from your admin panel.

The plugin supports Grav 1.7 and Grav 2.0, including configuration through Admin Classic and Admin Next. It also supports Plausible Cloud, self-hosted Plausible instances, and custom script domains.

## Installation

Installing the Plausible Analytics plugin using the GPM (Grav Package Manager) cli, the Grav admin panel, or manually. The GPM and admin panel methods are generally to be preferred.

### GPM

From the root of your Grav site, enter the following command:

```bash
$ bin/gpm install plausible
```

### Admin Panel

Install plugins from the Plugins section in your Grav administration panel. In the filter box, search for "Plausible" to narrow your search.


### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `plausible`. You can find these files on [GitHub](https://github.com/iainsgillis/grav-plugin-plausible) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/plausible
	
> NOTE: This plugin is a modular component for Grav which may require other plugins to operate, please see its [blueprints.yaml file on GitHub](https://github.com/iainsgillis/grav-plugin-plausible/blob/master/blueprints.yaml).

## Configuration

Before configuring this plugin, you should copy the `user/plugins/plausible/plausible.yaml` to `user/config/plugins/plausible.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
active: true
script_mode: legacy
script_url: null
data_domain: null
custom_event_goals: false
outbound_link_tracking: false
public_dashboard_visible: false
public_dashboard_url: null
self_hosting: false
custom_domain: null
```
- `enabled: true|false` toggles whether the Plausible Analytics plugin is turned on or off.
- `active: true|false` toggles whether Plausible Analytics is enabled site-wide or not.
- `script_mode: modern|legacy` selects the tracking script format. Existing configurations default to `legacy` so upgrades remain backward compatible.
- `script_url: String?` is the Modern site-specific script `src` URL from Plausible's **Site Settings → General → Site Installation** screen.
- `data_domain: String?` is the registered domain used by the Legacy script.
- `custom_event_goals: true|false` registers the global `plausible()` queue shim in Legacy mode.
- `outbound_link_tracking: true|false` loads the outbound-link variant of the Legacy script.
- `public_dashboard_visible: true|false` toggles whether to output a comment in the source of the page comment with a link to your public website stats dashboard.
- `public_dashboard_url: String?` is the full URL of your public dashboard, as set out in [https://docs.plausible.io/visibility](https://docs.plausible.io/visibility).
  - To actually output a comment in the HTML source, `public_dashboard_visible` must be `true` **and** `public_dashboard_url` must be a non-empty string.
  - If these conditions are met, then (e.g.,) `<!-- Plausible Analytics public dashboard URL : https://plausible.io/your-domain-here.com -->` is injected before your page content.
- `self_hosting: true|false` toggles whether to load the tracking script from a custom Plausible host.
- `custom_domain: String?` is the scheme, hostname, and optionally, the port to your self-hosted Plausible instance.
  - To use a self-hosted instance, `self_hosting` must be `true` **and** `custom_domain` must be a non-empty string.

Note that if you use the Admin Plugin, a file with your configuration named `plausible.yaml` will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## Usage

### Basic

1. Register for an account with [Plausible Analytics](https://plausible.io) and add your site.
2. Select **Modern** or **Legacy** under **Tracking script** in the plugin settings.
3. For Modern mode, copy only the site-specific script's `src` URL from **Site Settings → General → Site Installation** in Plausible and paste it into **Tracking script URL**.
4. For Legacy mode, enter the registered domain and configure any legacy options shown below it.

In Modern mode, the plugin emits the supplied external script with `async` plus the inline initialization: the `window.plausible` queue shim, a `plausible.init` fallback, and the `plausible.init()` call. Do not paste Plausible's full snippet into your theme or page as well; the plugin supplies both the loader and initialization. Legacy mode is unchanged.

The mode selector displays only the settings used by the selected script. The mode-specific URL and domain fields are optional in the Admin form so hidden fields do not block saving in Admin Classic. Tracking requires a script URL in Modern mode or a domain in Legacy mode; leaving the selected mode's value blank saves the configuration but emits no tracking script.

Configurations created before `script_mode` was introduced continue in Legacy mode until explicitly changed. See [Plausible's script update guide](https://plausible.io/docs/script-update-guide) when migrating.

### Advanced
#### Custom Event Goals

The updated Plausible script supports custom events automatically. Follow the [custom event documentation](https://plausible.io/docs/custom-event-goals) to trigger and configure events. The `custom_event_goals` option remains available only as a queue shim for legacy integrations that expect the plugin to define `window.plausible`.

#### Outbound Link Tracking

For the updated site-specific script, enable **Outbound links** under Plausible's site settings. Changes take effect without updating the script URL, and Plausible manages the `Outbound Link: Click` goal automatically. The plugin's `outbound_link_tracking` option applies only when using its generated legacy script URL.

#### Self-Hosting Plausible and Custom Domain

If your self-hosted installation provides a complete tracking-script URL, set it in `script_url`. For older installations, keep `script_url` empty, set `self_hosting` to `true`, and set `custom_domain` to the instance URL without a trailing slash or `/js/plausible`.

## Credits

[Plausible Analytics](https://plausible.io) is itself an open source project, licensed under GNU Affero General Public License Version 3 (AGPLv3). 

Hat-tip to [@divinerites](https://github.com/divinerites/plausible-hugo) for the idea to add public dashboard information to the page source.

## Ideas/Todos

- [ ] Handle Plausible's [exclusions](https://plausible.io/docs/excluding-pages)
- [ ] Add option to disable tracking entirely per-page via frontmatter? 
- [ ] Add capability to add JS to frontmatter (as a list of block scalars?) for custom event tracking

### Done

- [x] Add translations to `languages.yaml`.
- [x] Allow self-hosting configuration


