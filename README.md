# Genlyd

## Pattern lab

The styleguide is created by pattern-lab. The source of the styleguide is in web/themes/genlyd_theme/source.
The generated styleguide is located in /styleguide.

#### Install pattern-lab

Run the script "pattern-lab.sh" in the vagrant.

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

## platform.sh

There are included two platform.sh apps in the project (app and styleguide).

#### Connect with platform.sh

```sh
platform    # login
git remote add platform [Project ID]@git.eu.platform.sh:[Project ID].git
```

Push to platform.sh

```sh
git push platform develop       # or master
```

