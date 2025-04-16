# Nolej AI for Moodle LMS
Moodle integration plugin for [Nolej AI](https://nolej.io/).

![Nolej logo](pix/nolej.svg)

## Introduction
Nolej AI, developed by Neuronys, offers several advantages, including AI-driven
courseware that can quickly convert documents, videos, and audio into dynamic
active learning content. It facilitates skill development and personalized
learning paths, saving educators significant time and enhancing engagement through
interactive content creation tools, gamification, and social learning.

To use the plugin, start by [registering on Nolej AI](https://live.nolej.io/signup) website.
Upon registration, you'll receive an API key. Simply insert this key into the plugin to
enable its features. It's a quick step to enhance your experience. Let's get started! :rocket:

Please note that while the plugin itself is free, registration on Nolej website for
an API key is a separate process. Your support is appreciated!

## :globe_with_meridians: Supported languages
Help us expand language support! If your favorite language isn't here, contribute and make this project truly global.
Your input is not just welcome - it's essential! :rocket:

This plugin currently supports the following languages:

* :uk: English
* :fr: French
* :it: Italian
* :de: German
* :es: Spanish
* :portugal: Portuguese
* :netherlands: Dutch

## Requirements
* Moodle 4.1+ (tested with version `20221128`)
* H5P plugin `mod_h5pactivity`, included in Moodle since 3.9.
  This plugin does not share data with the older [`mod_hvp`](https://moodle.org/plugins/mod_hvp),
  so they can be both installed at the same time.
  You can also [migrate h5p activities to the new plugin](https://docs.moodle.org/405/en/H5P_migration_tool).

## Installation

### Installing via uploaded ZIP file

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

### Installing manually

The plugin can be also installed by putting the contents of this directory to

    ```
    {your/moodle/dirroot}/local/nolej
    ```

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    ```sh
    $ php admin/cli/upgrade.php
    ```

to complete the installation from the command line.


After the installation, Moodle will redirect you to the plugin setting page,
where you have to put the API Key and select where Nolej should store the generated H5P activities.

- Save in the course where the Nolej module was created:
This option links the generated H5P activities to the course where the Nolej module was originally created, even if the user switches to another course before starting the generation. It's ideal when you want to keep the content centralized in the source course for consistency.
- Save in the course where the activity generation is started:
With this option, the plugin uses the currently active course at the moment of generation as the reference for storage. This is useful when Nolej modules are created in one course but the activities need to be generated in a different course, such as in customized or shared teaching contexts.
- Save in the central "Nolej" container:
The activities are saved in a subfolder of a centralized shared context called Nolej, separate from individual courses. This helps avoid overloading course content banks and provides an organized structure for generated resources. However, careful permission management is required: users may not be able to access the generated activities if they lack the necessary permissions for the central context..

## After installation

### How to use the plugin?
You can read [here the documentation on how to use the plugin](docs/how-to.md).

### Where is the library page?
Depending on the theme installed on your platform, the navigation menu to the libray of Nolej may not appear.

If that's your case, [here's a guide on how to create a link to the library, using Moodle blocks](docs/navigation-block.md).

