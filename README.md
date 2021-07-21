# Plausible Analytics Plugin

The **Plausible Analytics** Plugin is an extension for [Grav CMS](http://github.com/getgrav/grav). Integrate Plausible Analytics into your Grav site and configure it from your admin panel.

This plugin currently doesn't support self-hosted Plausible instances. Shouldn't be too hard to implement, but I prefer to pay Plausible their *very* reasonable rates to support that project.

## Installation

Installing the Plausible Analytics plugin using the GPM (Grav Package Manager) cli, the Grav admin panel, or manually. The GPM and admin panel methods are generally to be preferred.

### GPM

From the root of your Grav site, enter the following command:

```bash
$ bin/gpm install plausible
```

### Admin Panel

Please find the latest instructions on how to add new plugins to your Grav site [via the admin panel](https://learn.getgrav.org/17/admin-panel/plugins). In the filter box, search for "Plausible" to narrow your search.


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
data_domain: null
custom_event_goals: false
outbound_link_tracking: false
public_dashboard_visible: false
public_dashboard_url: null
custom_plausible_domain: "https://plausible.io/"
self_hosting: false
custom_domain: null
```
- `enabled: true|false` toggles whether the Plausible Analytics plugin is turned on or off.
- `active: true|false` toggles whether Plausible Analytics is enabled site-wide or not.
- `data_domain: String?` is the domain you wish to track in Plausible Analytics; needs to be registered with Plausible.
- `custom_event_goals: true|false` toggles whether the global JavaScript function `plausible` is registered, enabling you to trigger custom events.
- `outbound_link_tracking: true|false` toggles whether the Plausible script snippet that gets loaded is capable of tracking outbound clicks.
- `public_dashboard_visible: true|false` toggles whether to output a comment in the source of the page comment with a link to your public website stats dashboard.
- `public_dashboard_url: String?` is the full URL of your public dashboard, as set out in [https://docs.plausible.io/visibility]().
  - To actually output a comment in the HTML source, `public_dashboard_visible` must be `true` **and** `public_dashboard_url` must be a non-empty string.
  - If these conditions are met, then (e.g.,) `<!-- Plausible Analytics public dashboard URL : https://plausible.io/your-domain-here.com -->` is injected before your page content.
- `self_hosting: true|false` toggles whether to enable 
- `custom_domain: String?` is the scheme, hostname, and optionally, the port to your self-hosted Plausible instance.
  - To use a self-hosted instance, `self_hosting` must be `true` **and** `custom_domain` must be a non-empty string.

Note that if you use the Admin Plugin, a file with your configuration named `plausible.yaml` will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## Usage

### Basic

1. Register for an account with [Plausible Analytics](https://plausible.io). No restrictions and no credit card required for their 30-day trial period.
2. Add the domain you wish to track to your Plausible account.
3. Add the same domain to the `data_domain` attribute in `user/config/plugins/plausible.yaml`. It's also available as the Domain field in the Admin panel.

### Advanced
#### Custom Event Goals

Refer to the [Plausible docs on Custom event goals](https://docs.plausible.io/custom-event-goals) for more details.

1. Set `custom_event_goals` to `true`. (This takes care of the tracking setup step described in Plausible's docs.)
2. Setup your event listeners to track whatever custom events you're interested in. One way to do this is with the [Custom JS Plugin](https://github.com/dimayakovlev/grav-plugin-custom-js) by @dimayakovlev. [Shortcode Assets](https://github.com/getgrav/grav-plugin-shortcode-assets), by the core Grav team, would work, too.
3. Follow the Plausible documentation to configure your custom event goal tracking in Plausible.
#### Outbound Link Tracking

1. Set `outbound_link_tracking` to `true`.
2. [Follow the Plausible documentation to create a custom event goal](https://docs.plausible.io/outbound-link-click-tracking#step-2-create-a-custom-event-goal-in-your-plausible-analytics-account) with the name `Outbound Link: Click`.

#### Self-Hosting Plausible and Custom Domain

1. [Follow the Plausible documentation to install and configure your self-hosted instance](https://plausible.io/docs/self-hosting).
2. Set `self_hosting` to `true`.
3. Add the URL to your self-hosted instance, omitting the trailing slash(`/`) and `js/plausible`.

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


