# Gitlab Markdown Adapter Plugin

The **Gitlab Markdown Adapter** Plugin is for [Grav CMS](http://github.com/getgrav/grav).

It adapts Gitlab's markdown to Grav and its plugins
([Diagram](https://github.com/Seao/grav-plugin-diagrams),
[MathJax](https://github.com/Sommerregen/grav-plugin-mathjax)).

See [the CHANGELOG](CHANGELOG.md) for a list of the features.

## Installation

Installing the Gitlab Markdown Adapter plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

> This plugin has not been submitted yet, so this is not available.

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install gitlab-markdown-adapter

This will install the Gitlab Markdown Adapter plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/gitlab-markdown-adapter`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `gitlab-markdown-adapter`. You can find these files on [GitHub](https://github.com/goutte/grav-plugin-gitlab-markdown-adapter) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/gitlab-markdown-adapter
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) and Diagrams to operate.

### Admin Plugin

If you use the admin plugin, you can install directly through the admin plugin by browsing the `Plugins` tab and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/gitlab-markdown-adapter/gitlab-markdown-adapter.yaml` to `user/config/plugins/gitlab-markdown-adapter.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the admin plugin, a file with your configuration, and named gitlab-markdown-adapter.yaml will be saved in the `user/config/plugins/` folder once the configuration is saved in the admin.

## Usage

Using Gitlab-flavored markdown should now work.

## Credits

Thanks to the Grav team and Community ❤ 

This plugin was hacked during a [Night of Citizen Code](http://nuitcodecitoyen.org/).

## To Do

- [ ] Write a test-suite
- [ ] Implement EVERYTHING
- [ ] Squash ALL bugs
- [ ] Add options to enable/disable behaviors

_Contributions welcome!_

