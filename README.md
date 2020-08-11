# Blur No-Alt

Socially engineer your WordPress content developers to write alt text for images. This is a WordPress 5 plugin that blurs images in the WordPress editing interface if there is no alt text on the image, or if the alt text is just the filename of the image (evaluating for .jpg, .png, or .gif). Your **users** still see the image just fine, but the **editor** will see a blurred image in the dashboard. Hovering over the image removes the blur effect.

Note: This will blur all images in the editing UI that don't have alt text -- even decorative images that SHOULD NOT have alt text, or images that are described elsewhere in the text. If you have such an image, you can give the img element the class "noalt" and it will not blur.

To use this plugin: Create a sub-directory in your wp-content/plugins directory called, well, you can call it anything, but how about Blur_No-Alt? And then put these two files into it. 

Then, go into your WordPress dashboard and look at your Plugins list. Activate the plugin, and you're good to go.

This plugin creates an options screen under the "Settings" menu item. It lets you toggle on and off the informative message at the top of each editing window.

Credit for this idea goes to Mark Whittaker at Southern Utah University, who mentioned this strategy in a conversation in the #accessibility channel on the [HighEdWeb](https://www.highedweb.org/) Slack.
