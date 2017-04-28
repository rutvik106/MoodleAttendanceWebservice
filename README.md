# Moodle Attendance App

Moodle Attendance is a customized Android  App for managing attendance of students online on moblie phones. It Requires Moodle Host with Mobile weservice enabled and Attendance Plugin installed. Additional Requirment are Moodle Attendance webservice file must be placed into root directory of Moodle.

### Bellow are the Guidelines to setup webservice

##### Step-1

The administrator of your Moodle site (which must be version 2.4 or later) must enable mobile access as follows:

  - First, in Site administration > Advanced features, tick the enable Web Services checkbox
  - In Site administration > Plugins > Web services > Mobile tick the 'Enable web services for mobile devices' checkbox, then click the button to save changes
  ![](https://raw.githubusercontent.com/rutvik106/MoodleAttendanceWebservice/master/assets/c1.JPG)


##### Step-2

Now you must Install Moodle Attendance Plugin on your moodle system to do so first you need to download Moodle Attendance Plugin File (mod_attendance_moodle29_2015040500.zip) you can download it from [here](https://moodle.org/plugins/mod_attendance) and install it as shown bellow.

![](https://raw.githubusercontent.com/rutvik106/MoodleAttendanceWebservice/master/assets/c2.JPG)

##### Sep-3
Now you should see the plugin in the Plugins Overview Page as bellow

![](https://raw.githubusercontent.com/rutvik106/MoodleAttendanceWebservice/master/assets/c3.JPG)

##### Step-4
The last and the important thing, You must change moodle config.php file as follows

![](https://raw.githubusercontent.com/rutvik106/MoodleAttendanceWebservice/master/assets/c4.JPG)

Change $CFG->wwwroot value

if you have following value:

```sh
$CFG->wwwroot   = 'http://localhost';   or  $CFG->wwwroot   = 'http://yourwebsite';
```
change it to:
```sh
$CFG->wwwroot   = 'http://'.$_SERVER["HTTP_HOST"];
```

if you have following value:
```sh
$CFG->wwwroot   = 'http://localhost/moodle';   or  $CFG->wwwroot   = 'http://yourwebsite/moodle';
```
change it to:
```sh
$CFG->wwwroot   = 'http://'.$_SERVER["HTTP_HOST"].'/moodle';
```