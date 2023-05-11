# Blur No-Alt

Author: [Kerri Hicks](https://kerri.is)  
Contributors: [Mark Root-Wiley](https://MRWweb.com)  
Stable tag: 0.93
Tested up to: 6.2  
License: GPLv3 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html  

Blur images in the WordPress editor interface if they don't have alt text. Socially engineer your WordPress content developers to write alt text for images.

(Looking for something like this, but for Drupal? Take a look at [Carole Mah's Blur No-Alt **for Drupal** fork](https://github.com/c-e-mah/blur_no_alt).)

## Description

This plugin blurs images in the WordPress Block Editor interface if there is no alt text on the image, or if the alt text contains .jpg, .png, or .gif. **Site visitors** still see the image just fine, but **editors** will see a blurred image in the dashboard. Hovering or selecting the image removes the blur effect.

As of v0.93, this plugin can optionally blur images on the front end **only for logged-in users with the `edit_pages` capability**.

Most styles work in all "modern browsers" (not Internet Explorer). Media & Text blocks with the "crop" setting only work in very recent browsers that support the `:has()` psuedo selector.

### Note: Unblurring Decorative Images

Note: This will blur __all__ images in the editing UI that don't have alt text — even decorative images that should not have alt text, or images that are described elsewhere in the text. If you have such an image, you can give the img element the class `noalt` and it will not blur.

On the front-end, images that are marked with `[aria-hidden="true"]` (or are a descendent of an element with that attribute) will not be blurred.

### Settings

This plugin's settings are found in "Settings" > "Blur No-Alt" in the WordPress admin menu. The plugin provides two settings for customizing the plugin's behavior:

- Toggle the informational message about blurred images above the title in the block editor
- Toggle whether logged-in users see blurred images when viewing the front-end of the site

When the front-end styles are enabled, developers can customize which users see the blurred images with the `blur_no_alt_front_end_blur_capability` filter (default: `edit_pages`).


## Installation

To use this plugin:

1. Create a sub-directory in your `wp-content/plugins` directory called, well, you can call it anything, but how about `Blur_No-Alt`?
2. Put all of these files into it. 
3. Go into your WordPress dashboard and look at your Plugins list. Activate the plugin, and you're good to go!
4. To customize the plugin settings, go to "Settings" > "Blur No-Alt" in the admin menu.

## Credits

Credit for this idea goes to Mark Whittaker at Southern Utah University, who mentioned the idea of a blurring strategy in a conversation in the #accessibility channel on the [HighEdWeb](https://www.highedweb.org/) Slack.

## Changelog

### v0.93 (11 May 2023)
- NEW: Option to blur images on the front-end for logged-in users with `edit_pages` capability. Option added to control this feature. Use `blur_no_alt_front_end_blur_capability` filter to adjust capability required to see blurred images.
- NEW: All styles now use `!important` to ensure they apply correctly. There are new custom properties to make it easy to customize styles for images with no alt text: `--blur-no-alt--clip-path`, `--blur-no-alt--filter`, `--blur-no-alt--outline`, `--blur-no-alt--outline`, `--blur-no-alt--transition`, `--blur-no-alt--filter--hover`, `--blur-no-alt--outline--hover`
- Switch settings to use single checkbox instead of two radio buttons
- Set version parameter for enqueued styles to ensure old versions of styles aren't cached
- Refactor plugin to follow WordPress coding standards (minor security hardening)
- Improve file organization with `css` folder
- Tested with WordPress 6.2

### v0.92 (22 June 2022)

- Introduce plugin versioning to make it easier to see changes to plugin
- Resolve notice after first installing plugin by providing default options
- Sanitize single setting value
- Improve support for media blocks in the block editor
- Make transition between blurred and unblurred less jarring
- Refactor CSS and remove rules that didn't do anything
- Fix support for unblurring images with `noalt` class
- Don't blur images in blocks with the `is-selected` class (adds implicit keyboard support)
- Improve display of message at the top of the editor
- Revise readme to mostly follow wp.org guidelines
- Add new plugin headers for clarity and to prevent accidental updates from wordpress.org repository
- Support translating all plugin strings
