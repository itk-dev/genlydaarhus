# Genlyd

## Structure

This project consists of two parts: drupal and styleguide. 

## Pattern lab

The styleguide is created by pattern-lab. The source of the styleguide is in web/themes/genlyd_theme/source.
The generated styleguide is located in /styleguide.

#### Install pattern-lab

Run the script "pattern-lab.sh" in the vagrant.

What it does is the following, from the htdocs/ folder:

```sh
composer create-project pattern-lab/edition-twig-standard pattern-lab

sed -i "s/publicDir:.*/publicDir: ..\/styleguide/g" pattern-lab/config/config.yml
sed -i "s/sourceDir:.*/sourceDir: ..\/drupal\/web\/themes\/custom\/genlyd_theme\/source/g" pattern-lab/config/config.yml
sed -i "s/cacheBusterOn:.*/cacheBusterOn: 'false'/g" pattern-lab/config/config.yml

cd pattern-lab

composer install
```

#### Generating styleguide

If everything went well you should now be able to generate the static Pattern Lab site. In the `pattern-lab` directory run:

```sh
php core/console --generate
```

#### Start the server

To start the server, in the `pattern-lab` directory run:

```sh
php core/console --server
```

#### Watch for Changes and Reload

Pattern Lab can watch for changes to files in the `source` folder and automatically rebuild the entire Pattern Lab 
website for you. Make your changes, save the file, and Pattern Lab takes care of the rest.

Install the Auto-Reload Plugin:

```sh
composer require pattern-lab/plugin-reload
```

Run the server with watch and auto reload:

```sh
php core/console --server --with-watch
```

#### Release on master

Set "cacheBusterOn" to true, and compile the styleguide. Then set "cacheBusterOn" to false again. 
This makes the timestamps on each file change.

## platform.sh

There are included two platform.sh apps in the project (app and styleguide).

#### Connect with platform.sh

```sh
platform    # login
platform project:set-remote [Project ID]
```

#### Push to platform.sh

```sh
git push platform develop       # or master
```

#### Setup drush aliases

```sh
platform drush-aliases
```
