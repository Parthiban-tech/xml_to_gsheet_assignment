# Export data to Google Sheet

### About

This is a command-line application, based on the [Symfony CLI component](https://symfony.com/doc/current/components/console.html). The application process a local or remote(ftp) XML file and push the data of that XML file to a Google Spreadsheet via the [Google Sheets API](https://developers.google.com/sheets/).

### Technology Used

* PHP 8.0.12
* Symfony CLI version 4.26.8
  
### Setup

* Create [Google service account](https://support.google.com/a/answer/7378726?hl=en) and download JSON file which will have all the details required to use Google API services.

* Enable Google Sheets API and Google Drive API.

* Next step is to **setup environment**. File `env` contains all the variables required for application.

* Primarily, For Google service account have to set the following env variable. Place the downloaded JSON file in repository and give a path of it in `GC_AUTH_CONFIG`.

```php
GC_AUTH_CONFIG=credentials.json
```

* For accessing files from remote server(ftp) set following env details

```php
FTP_HOST=
FTP_USER=
FTP_PASSWORD=
```

* Build docker container

```
docker-compose build
docker-compose up -d
```
  
 
### Run export command

* For local export run this command inside the php container

```
php bin/console app:export-data --source=local employee.xml
```

* For remote export run this command inside the php container

```
php bin/console app:export-data --source=ftp coffee_feed.xml
```


### Run tests

* Run the following commad inside the container to check the test case result.

```
./vendor/bin/phpunit tests/
```

### Logs

* Logs for the application are stored in the 'environment'.log file. **Information log** and **Error log** are written in 'environment'.log and 'environment'_errors.log files respectively which resides in the path `var/log/`


