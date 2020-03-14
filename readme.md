#Vox Teneo tests.

How to install for new user:
- clone the project using `git clone git@github.com:andriansyahnc/vt.git`
- go to the directory`cd vt`
- install dependency using `composer install`
- copy the default settings using `cp web/sites/default/default.settings.php web/sites/settings.php`
- install the project using `vendor/bin/drush si --existing-config`
- import the db using `vendor/bin/drush sql-cli < vox_teneo_20200314_055754.sql`
- start the server using `vendor/bin/drush runserver`
