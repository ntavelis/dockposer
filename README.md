## How to debug package with xdebug

### Environment setup
1. Given the following phpstorm config:
![image](assets/phpstorm_xdebug_settings.png)

Note: here we name the server configuration as `application`, this name should match the `PHP_IDE_CONFIG="serverName=application"` in the command for debugging.
Note: it is a good idea to uncheck this configuration from phpstorm, settings -> langauges & frameworks -> debug -> uncheck: Force break at first line when no path mapping specified.
2. Create another project on the same level as this package. So you have the below directory structure:
`/home/antavelis/webprojects/dockposer` -> clone of the package inside here
`/home/antavelis/webprojects/demoapp` -> a php application with a composer.json file

3. In the demoapp location require the local version of the dockpose package:

```json
{
    "type": "project",
    "license": "proprietary",
    "require": {
        "php": "^7.2.5",
        "ext-ctype": "*",
        "ext-iconv": "*",
        "ntavelis/dockposer": "dev-master"
    },
    "repositories": [
        {
            "type": "path",
            "url": "../dockposer"
        }
    ]
}

``` 

Note: the url path must point to the location of the package. Composer will symlink the package and it will recognize it as a plugin.

3. You will need docker, please install docker before preceding.

### Execute the debug command
1. Navigate to the package's root directory, e.g `/home/antavelis/webprojects/dockposer`
2. ```cd ./docker```
3. Setup a break point in phpstorm, somewhere in the code, e.g. in the class `src/DockposerPlugin.php`
4. Execute:
```shell script
docker-compose run --rm -e PHP_IDE_CONFIG="serverName=application" -e COMPOSER_ALLOW_XDEBUG=true phpserver-dockposer bash -c 'cd /srv/app/demoapp; php -d xdebug.remote_host=172.17.0.1 /usr/local/bin/composer install' 
```
Note: This environmental variable `COMPOSER_ALLOW_XDEBUG=true` instructs composer to NOT disable xdebug, so that we can debug our application. 