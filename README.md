# Blur No-Alt

Author: [Kerri Hicks](https://kerri.is)  
Contributors: [Mark Root-Wiley](https://MRWweb.com)  
Stable tag: 0.92  
Tested up to: 6.0  
License: GPLv3 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html  

Blur images in the WordPress editor interface if they don't have alt text. Socially engineer your WordPress content developers to write alt text for images.

(Looking for something like this, but for Drupal? Take a look at [Carole Mah's Blur No-Alt **for Drupal** fork](https://github.com/c-e-mah/blur_no_alt).)

(Looking for a more recently updated version? Take a look at [Mark's fork](https://github.com/mrwweb/wp-blur-missing-alt-text).)

## Description

This plugin blurs images in the WordPress Block Editor interface if there is no alt text on the image, or if the alt text contains .jpg, .png, or .gif. **Site visitors** still see the image just fine, but **editors** will see a blurred image in the dashboard. Hovering or selecting the image removes the blur effect.

Most styles work in all "modern browsers" (not Internet Explorer). Media & Text blocks with the "crop" setting only work in very recent browsers that support the `:has()` psuedo selector.

### Note: Unblurring Decorative Images

Note: This will blur __all__ images in the editing UI that don't have alt text — even decorative images that should not have alt text, or images that are described elsewhere in the text. If you have such an image, you can give the img element the class `noalt` and it will not blur.

### Settings

This plugin creates a "Blur No-Alt Message Display" options screen in the "Settings" with one option to toggle the informative message at the top of the block editor screen.

## Installation

To use this plugin:

1. Create a sub-directory in your wp-content/plugins directory called, well, you can call it anything, but how about Blur_No-Alt?
2. put all of these files into it. 
3. Go into your WordPress dashboard and look at your Plugins list. Activate the plugin, and you're good to go.

## Credits

Credit for this idea goes to Mark Whittaker at Southern Utah University, who mentioned the idea of a blurring strategy in a conversation in the #accessibility channel on the [HighEdWeb](https://www.highedweb.org/) Slack.

## Changelog

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
