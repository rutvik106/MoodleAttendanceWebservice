<?php
/**
 * Created by Rutvik Mehta
 * GitHub Profile: https://github.com/rutvik106
 */

// This webservice is developed specially for Moodle Attendance App
// (https://play.google.com/store/apps/details?id=com.rutvik.moodleattendanceapp).
// include basic functions for attendance get,set,insert,update,delete,modify....
// includes DB connection

// Set your moodle directory
// eg: if you use "yourhostname.com/moodle" to open your modle use define('MOODLE_DIR', '/moodle');
//     if you use "yourhostname.com/xyz" to open your modle use define('MOODLE_DIR', '/xyz');
//     if you use just "yourhostname.com" to open moodle use define('MOODLE_DIR', '');
define('MOODLE_DIR', '/moodle');

// Set path to your moodle config.php file here.
require_once('config.php');

global $CFG;

$conn = mysqli_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

$sql1 = "use $CFG->dbname";
$rs2 = mysqli_query($conn, $sql1);

if (!$conn)
{
    $post_data = array('message' => 'SQL connection failed', 'comment' => 'Cannot connect to Moodle Database');
    $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
    die($post_data);
} else
{

    $query = "SELECT * FROM " . $CFG->prefix . "attendance";
    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => 'no table found in sql database', 'comment' => 'No Attendance Plugin found on Moodle Host');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        die($post_data);
    }

}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (isset($_GET['method']) && $_GET['method'] != "")
{

    switch ($_GET['method'])
    {

        case 'get_table_names':
            getTableNames($conn);
            break;

        case 'get_table_columns':
            if (isset($_GET['table_name']) && $_GET['table_name'] != "")
            {
                getTableColumns($conn, $_GET['table_name']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [table_name]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'get_table_data':
            if (isset($_GET['table_name']) && $_GET['table_name'] != "")
            {
                getTableData($conn, $_GET['table_name']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [table_name]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'login':
            if (isset($_GET['user_name']) && $_GET['user_name'] != "" && isset($_GET['password']) && $_GET['password'] != "")
            {
                login($conn, $_GET['user_name'], $_GET['password']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [user_name,password]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'test':
            if (isset($_GET['user_name']) && $_GET['user_name'] != "" && isset($_GET['password']) && $_GET['password'] != "")
            {
                test($conn, $_GET['user_name'], $_GET['password']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [user_name,password]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;


        case 'get_attendance':
            if (isset($_GET['session_id']) && $_GET['session_id'] != "" && isset($_GET['token']) && $_GET['token'] != "")
            {
                getAttendance($conn, $_GET['token'], $_GET['session_id']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [session_id]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'add_attendance':
            if (isset($_GET['session_id']) && $_GET['session_id'] != "" && isset($_GET['status_set']) && $_GET['status_set'] != "" && isset($_GET['taken_by']) && $_GET['taken_by'] != "" && isset($_GET['data']) && $_GET['data'] != "" && isset($_GET['time']) && $_GET['time'] != "")
            {
                addAttendance($conn, $_GET['session_id'], $_GET['status_set'], $_GET['taken_by'], $_GET['time'], $_GET['data']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [session_id,status_set,taken_by,time,data]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'add_session':


            if (isset($_GET['attendance_id']) && $_GET['attendance_id'] != "" && isset($_GET['session_date']) && $_GET['session_date'] != "" && isset($_GET['duration']) && $_GET['duration'] != "" && isset($_GET['time_modified']) && $_GET['time_modified'] != "" && isset($_GET['description']) && $_GET['description'] != "")
            {
                addSession($conn, $_GET['attendance_id'], $_GET['session_date'], $_GET['duration'], $_GET['time_modified'], $_GET['description']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [attendance_id,session_date,duration,time_modified,description]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'update_attendance':
            if (isset($_GET['session_id']) && $_GET['session_id'] != "" && isset($_GET['taken_by']) && $_GET['taken_by'] != "" && isset($_GET['time']) && $_GET['time'] != "" && isset($_GET['data']) && $_GET['data'] != "")
            {
                updateAttendance($conn, $_GET['session_id'], $_GET['taken_by'], $_GET['time'], $_GET['data']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [session_id,taken_by,time,data]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'get_courses':
            if (isset($_GET['token']) && $_GET['token'] != "" && isset($_GET['user_id']) && $_GET['user_id'] != "")
            {
                getCourses($conn, $_GET['token'], $_GET['user_id']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [token,user_id]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'get_sessions':
            if (isset($_GET['course_id']) && $_GET['course_id'] != "" && isset($_GET['attendance_type_id']) && $_GET['attendance_type_id'] != "")
            {
                getSessions($conn, $_GET['course_id'], $_GET['attendance_type_id']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [course_id,attendance_type_id]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'get_enrolled_students':
            if (isset($_GET['token']) && $_GET['token'] != "" && isset($_GET['course_id']) && $_GET['course_id'] != "")
            {
                getEnrolledStudents($conn, $_GET['token'], $_GET['course_id']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [token,course_id]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'delete_session':
            if (isset($_GET['user_name']) && $_GET['user_name'] != "" && isset($_GET['password']) && $_GET['password'] != "" && isset($_GET['session_id']) && $_GET['session_id'] != "")
            {
                deleteSession($conn, $_GET['user_name'], $_GET['password'], $_GET['session_id']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [user_name,password,session_id]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }
            break;

        case 'get_attendance_type':

            if (isset($_GET['course_id']) && $_GET['course_id'] != "")
            {
                getAttendanceType($conn, $_GET['course_id']);
            } else
            {
                $post_data = array('message' => 'missing parameters', 'comment' => 'require param [course_id]');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            }

            break;

        default:

            echo("invalid method name or method name not provided");

            break;

    }

} else
{
    echo "<h3>***Welcome to Moodle custome webservice, Successfully Connected to MOODLE DB available method=[get_table_names, get_table_columns, get_table_data, login, get_attendance, add_session]***</h3>" . "\n\n eg: <b>[host]</b><i>/webservice.php?method=login&user_name=rutvik&password=Reset@123</i>\n\n <h2>For any query Contact: <font color=#FF0000>+91-9409210488</font> (Rutvik D Mehta)</h2>";
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function getTableNames($conn)
{
    global $CFG;

    $query = "SHOW TABLES FROM $CFG->dbname";

    $result = mysqli_query($conn, $query);

    $count = 0;

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SHOW TABLES query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_assoc($result))
            {
                $count++;
                $data = array('no' => $count, 'name' => $row['Tables_in_moodle']);
                $post_data[] = $data;

            }
            $post_data = json_encode(array('tables' => $post_data));
            echo $post_data;

        }
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getTableColumns($conn, $tableName)
{
    $query = "SHOW COLUMNS FROM $tableName";

    $result = mysqli_query($conn, $query);

    $count = 0;

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SHOW COLUMNS query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        while ($row = mysqli_fetch_array($result))
        {
            $count++;
            $data = array('no' => $count, 'name' => $row['Field'], 'type' => $row['Type']);
            $post_data[] = $data;
        }
        $post_data = json_encode(array('columns' => $post_data));
        echo $post_data;
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getTableData($conn, $tableName)
{
    $query = "SELECT * FROM $tableName";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_assoc($result))
            {
                $encode[] = $row;
            }

            $data['rows'] = $encode;

            echo json_encode($data);
        }
    }
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function login($conn, $userName, $password)
{
    global $CFG;

    $userId = "";
    $firstName = "";
    $lastName = "";
    $fullName = "";
    $profilePic = "";
    $courses = array();

    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/login/token.php?username=$userName&password=$password&service=moodle_mobile_app";
    $data = file_get_contents($url);
    $result = json_decode($data, TRUE);
    //var_dump($result);
    if (isset($result['token']))
    {
        $token = $result['token'];
    } else
    {
        $post_data = array('message' => 'login error', 'comment' => 'invalid user name or password');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        die($post_data);
    }

    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_webservice_get_siteinfo";
    $data = file_get_contents($url);
    //$result = json_decode($data, true);
    $response = new SimpleXMLElement($data);
    //var_dump($response);

    $response->getName();

    foreach ($response->children() as $child)
    {
        //echo $child->getName()."\n";

        foreach ($child->children() as $child2)
        {
            //echo $child2->getName()."\n";
            //echo $child2['name'];
            if ($child2['name'] == "userid")
            {
                foreach ($child2->children() as $child3)
                {
                    $userId = (string)$child3;
                    //print_r($userId);
                }
            }
            if ($child2['name'] == "firstname")
            {
                foreach ($child2->children() as $child3)
                {
                    $firstName = (string)$child3;
                }
            }
            if ($child2['name'] == "lastname")
            {
                foreach ($child2->children() as $child3)
                {
                    $lastName = (string)$child3;
                }
            }
            if ($child2['name'] == "fullname")
            {
                foreach ($child2->children() as $child3)
                {
                    $fullName = (string)$child3;
                }
            }
            if ($child2['name'] == "userpictureurl")
            {
                foreach ($child2->children() as $child3)
                {
                    $profilePic = (string)$child3;
                    $profilePic = str_replace($CFG->dbhost, $_SERVER["HTTP_HOST"], $profilePic);
                }
            }
            /*foreach($child2->children() as $child3)
            {
                echo $child3->getName();
                echo $child3;
            }*/
        }
    }


    $query = "SELECT roleid FROM " . $CFG->prefix . "role_assignments WHERE userid LIKE $userId LIMIT 1";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                $roleId = $row['roleid'];
            }
            $query2 = "SELECT shortname FROM " . $CFG->prefix . "role WHERE id LIKE $roleId LIMIT 1";

            $result2 = mysqli_query($conn, $query2);

            if (!$result2)
            {
                $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            } else
            {
                if (mysqli_num_rows($result2) > 0)
                {
                    while ($row2 = mysqli_fetch_array($result2))
                    {
                        $roleShortName = $row2['shortname'];
                    }

                }
            }
        }
    }


    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_user_get_users_by_id&userids[0]=$userId";

    $data = file_get_contents($url);
    //$result = json_decode($data, true);
    $response = new SimpleXMLElement($data);
    //var_dump($response);

    $response->getName();

    foreach ($response->children() as $child)
    {
        //echo $child->getName()."\n";
        foreach ($child->children() as $child2)
        {
            //echo $child2->getName()."\n";
            foreach ($child2->children() as $child3)
            {
                //echo $child3->getName()."\n";
                if ($child3['name'] == "enrolledcourses")
                {
                    foreach ($child3->children() as $child4)
                    {
                        //echo $child4->getName()."\n";
                        foreach ($child4->children() as $child5)
                        {
                            //echo $child5->getName()."\n";
                            foreach ($child5->children() as $child6)
                            {
                                $courseId;
                                $courseFullName;
                                $courseShortName;
                                //echo $child6->getName()."\n";
                                if ($child6['name'] == "id")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseId = (string)$child7;
                                    }
                                }
                                if ($child6['name'] == "fullname")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseFullName = (string)$child7;
                                    }
                                }
                                if ($child6['name'] == "shortname")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseShortName = (string)$child7;
                                    }
                                }
                            }

                            if ($roleShortName != "student")
                            {

                                $url2 = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_enrol_get_enrolled_users&courseid=$courseId";

                                $data2 = file_get_contents($url2);
                                //$result = json_decode($data, true);
                                $response2 = new SimpleXMLElement($data2);
                                //var_dump($response);

                                $response2->getName();

                                $students = array();

                                foreach ($response2->children() as $c)
                                {
                                    //echo $c->getName()."\n";
                                    foreach ($c->children() as $c2)
                                    {
                                        //echo $c2->getName()."\n";
                                        foreach ($c2->children() as $c3)
                                        {
                                            $studentUserId;
                                            $studentFirstName;
                                            $studentLastName;
                                            $studentFullName;
                                            $studentUserName;
                                            $studentProfilePic;

                                            //echo $c3->getName()."\n";
                                            if ($c3['name'] == "userid")
                                            {
                                                foreach ($c3->children() as $c4)
                                                {
                                                    //echo $c4."\n";
                                                    $studentUserId = (string)$c4;
                                                }
                                            }
                                            if ($c3['name'] == "firstname")
                                            {
                                                foreach ($c3->children() as $c4)
                                                {
                                                    //echo $c4."\n";
                                                    $studentFirstName = (string)$c4;
                                                }
                                            }
                                            if ($c3['name'] == "lastname")
                                            {
                                                foreach ($c3->children() as $c4)
                                                {
                                                    //echo $c4."\n";
                                                    $studentLastName = (string)$c4;
                                                }
                                            }
                                            if ($c3['name'] == "fullname")
                                            {
                                                foreach ($c3->children() as $c4)
                                                {
                                                    //echo $c4."\n";
                                                    $studentFullName = (string)$c4;
                                                }
                                            }
                                            if ($c3['name'] == "username")
                                            {
                                                foreach ($c3->children() as $c4)
                                                {
                                                    //echo $c4."\n";
                                                    $studentUserName = (string)$c4;
                                                }
                                            }
                                            if ($c3['name'] == "profileimgurl")
                                            {
                                                foreach ($c3->children() as $c4)
                                                {
                                                    //echo $c4."\n";
                                                    $studentProfilePic = (string)$c4;
                                                }
                                            }
                                        }
                                        if ($studentUserId != $userId)
                                        {
                                            $students[] = array('user_id' => $studentUserId, 'first_name' => $studentFirstName, 'last_name' => $studentLastName, 'full_name' => $studentFullName, 'user_name' => $studentUserName, 'profile_pic_url' => $studentProfilePic);
                                        }
                                    }
                                }


                                $query = "SELECT * FROM " . $CFG->prefix . "attendance WHERE course LIKE $courseId";

//echo $query."<br/><br/>";

                                $result = mysqli_query($conn, $query);

                                if (!$result)
                                {
                                    $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                                    $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                                    echo $post_data;
                                } else
                                {
                                    $attendance = array();
                                    if (mysqli_num_rows($result) > 0)
                                    {
                                        while ($row = mysqli_fetch_array($result))
                                        {
                                            //echo 'inside it!!';

                                            $val = $row['id'];

                                            //echo $val."<br/><br/><br/>";

                                            $sessions2 = getSessions($conn, $courseId, $val, TRUE);

                                            //var_dump($sessions2);


                                            /*
                                            $query2="SELECT * FROM mdl_attendance_sessions WHERE attendanceid LIKE $val";



                                            $result2=mysqli_query($conn,$query2);



                                            if(!$result2)
                                            {
                                                $post_data=array('message'=>mysqli_errno($conn) . ": " . mysqli_error($conn),'comment'=>'while performing SELECT query');
                                                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                                                echo $post_data;
                                            }
                                            else
                                            {
                                                if(mysqli_num_rows($result2)>0)
                                                {
                                                    while($row2 =mysqli_fetch_array($result2))
                                                    {
                                                        $desc=strip_tags($row2['description']);
                                                        $sessions[]=array('id'=>$row2['id'],'groupid'=>$row2['groupid'],'sessdate'=>$row2['sessdate'],'duration'=>$row2['duration'],'lasttaken'=>$row2['lasttaken'],'lasttakenby'=>$row2['lasttakenby'],'timemodified'=>$row2['timemodified'],'description'=>$desc,'descriptionformat'=>$row2['descriptionformat'],'studentscanmark'=>$row2['studentscanmark']);
                                                    }

                                                }
                                                else
                                                {
                                                    //echo "INSIDE!!!";
                                                    $sessions[]=array('id'=>NULL,'groupid'=>NULL,'sessdate'=>NULL,'duration'=>NULL,'lasttaken'=>NULL,'lasttakenby'=>NULL,'timemodified'=>NULL,'description'=>NULL,'descriptionformat'=>NULL,'studentscanmark'=>NULL);
                                                }


                                            }

*/

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////                                                
                                            $query3 = "SELECT * FROM " . $CFG->prefix . "attendance_statuses WHERE attendanceid LIKE $val";

                                            $result3 = mysqli_query($conn, $query3);

                                            if (!$result3)
                                            {
                                                $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                                                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                                                echo $post_data;
                                            } else
                                            {
                                                $status = array();
                                                if (mysqli_num_rows($result3) > 0)
                                                {
                                                    while ($row3 = mysqli_fetch_array($result3))
                                                    {
                                                        $status[] = array('id' => $row3['id'], 'acronym' => $row3['acronym'], 'description' => $row3['description'], 'grade' => $row3['grade'], 'visible' => $row3['visible'], 'deleted' => $row3['deleted']);
                                                    }

                                                }


                                            }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                                            $attendance[] = array('id' => $row['id'], 'name' => $row['name'], 'grade' => $row['grade'], 'sessions' => $sessions2, 'statuses' => $status);

                                        }
                                        $courses[] = array('id' => $courseId, 'full_name' => $courseFullName, 'short_name' => $courseShortName, 'attendance' => $attendance, 'enrolled_students' => $students);

                                    } else
                                    {

                                        $courses[] = array('id' => $courseId, 'full_name' => $courseFullName, 'short_name' => $courseShortName, 'attendance' => array(), 'enrolled_students' => $students);
                                    }


                                }

                            } else
                            {


                                $query = "SELECT * FROM " . $CFG->prefix . "attendance WHERE course LIKE $courseId";

                                $result = mysqli_query($conn, $query);

                                if (!$result)
                                {
                                    $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                                    $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                                    echo $post_data;
                                } else
                                {
                                    $attendance = array();
                                    if (mysqli_num_rows($result) > 0)
                                    {
                                        while ($row = mysqli_fetch_array($result))
                                        {
                                            $attendanceId = $row['id'];

                                            $query2 = "SELECT " . $CFG->prefix . "attendance_sessions.id," . $CFG->prefix . "attendance_sessions.sessdate," . $CFG->prefix . "attendance_sessions.duration," . $CFG->prefix . "attendance_sessions.lasttaken," . $CFG->prefix . "attendance_sessions.lasttakenby," . $CFG->prefix . "attendance_sessions.lasttakenby," . $CFG->prefix . "attendance_sessions.timemodified," . $CFG->prefix . "attendance_sessions.description," . $CFG->prefix . "attendance_log.statusid," . $CFG->prefix . "attendance_log.remarks," . $CFG->prefix . "attendance_log.timetaken," . $CFG->prefix . "user.firstname," . $CFG->prefix . "user.lastname," . $CFG->prefix . "attendance_statuses.acronym," . $CFG->prefix . "attendance_statuses.description AS sdesc FROM " . $CFG->prefix . "attendance_sessions LEFT JOIN " . $CFG->prefix . "attendance_log ON " . $CFG->prefix . "attendance_sessions.id = " . $CFG->prefix . "attendance_log.sessionid LEFT JOIN " . $CFG->prefix . "user ON " . $CFG->prefix . "attendance_sessions.lasttakenby = " . $CFG->prefix . "user.id LEFT JOIN " . $CFG->prefix . "attendance_statuses ON " . $CFG->prefix . "attendance_log.statusid = " . $CFG->prefix . "attendance_statuses.id WHERE " . $CFG->prefix . "attendance_sessions.attendanceid LIKE $attendanceId AND " . $CFG->prefix . "attendance_log.studentid LIKE $userId";

                                            //echo($query2);

                                            $result2 = mysqli_query($conn, $query2);

                                            if (!$result2)
                                            {
                                                $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                                                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                                                echo $post_data;
                                            } else
                                            {
                                                $sessions = array();
                                                if (mysqli_num_rows($result2) > 0)
                                                {
                                                    while ($row2 = mysqli_fetch_array($result2))
                                                    {

                                                        $sessions[] = array('id' => $row2['id'], 'session_date' => $row2['sessdate'], 'duration' => $row2['duration'], 'lasttaken' => $row2['lasttaken'], 'lasttakenby' => $row2['lasttakenby'], 'first_name' => $row2['firstname'], 'last_name' => $row2['lastname'], 'timemodified' => $row2['timemodified'], 'description' => strip_tags($row2['description']), 'statusid' => $row2['statusid'], 'acronym' => $row2['acronym'], 'desc' => $row2['sdesc'], 'remarks' => $row2['remarks'], 'timetaken' => $row2['timetaken']);
                                                    }
                                                }

                                            }

                                            $query3 = "SELECT * FROM " . $CFG->prefix . "attendance_statuses WHERE attendanceid LIKE $attendanceId";

                                            $result3 = mysqli_query($conn, $query3);

                                            if (!$result3)
                                            {
                                                $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                                                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                                                echo $post_data;
                                            } else
                                            {
                                                if (mysqli_num_rows($result3) > 0)
                                                {
                                                    while ($row3 = mysqli_fetch_array($result3))
                                                    {
                                                        $status[] = array('id' => $row3['id'], 'acronym' => $row3['acronym'], 'description' => $row3['description'], 'grade' => $row3['grade'], 'visible' => $row3['visible'], 'deleted' => $row3['deleted']);
                                                    }

                                                }

                                            }

                                            $attendance[] = array('id' => $row['id'], 'name' => $row['name'], 'grade' => $row['grade'], 'sessions' => $sessions, 'statuses' => $status);
                                        }


                                        $courses[] = array('id' => $courseId, 'full_name' => $courseFullName, 'short_name' => $courseShortName, 'attendance' => $attendance);
                                    } else
                                    {
                                        $courses[] = array('id' => $courseId, 'full_name' => $courseFullName, 'short_name' => $courseShortName, 'attendance' => array());
                                    }

                                }


                            }


                        }
                    }
                }
            }
        }
    }

    //echo $userId;
    //echo $userName;
    //echo $firstName;
    //echo $lastName;
    //echo $fullName;


    $post_data = array('id' => $userId, 'token' => $token, 'user_name' => $userName, 'first_name' => $firstName, 'last_name' => $lastName, 'full_name' => $fullName, 'profile_pic_url' => $profilePic, 'role_id' => $roleId, 'role_short_name' => $roleShortName, 'course' => $courses);
    $post_data = json_encode(array('user' => $post_data));
    echo $post_data;


}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function getAttendance($conn, $token, $sessionId)
{
    global $CFG;

    $student = array();

    $userId = "";

    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_webservice_get_siteinfo";
    $data = file_get_contents($url);
    //$result = json_decode($data, true);
    $response = new SimpleXMLElement($data);
    //var_dump($response);

    $response->getName();

    foreach ($response->children() as $child)
    {
        //echo $child->getName()."\n";

        foreach ($child->children() as $child2)
        {
            //echo $child2->getName()."\n";
            //echo $child2['name'];
            if ($child2['name'] == "userid")
            {
                foreach ($child2->children() as $child3)
                {
                    $userId = (string)$child3;
                    //print_r($userId);
                }
            }
        }
    }


    $query = "SELECT * FROM " . $CFG->prefix . "attendance_log WHERE sessionid LIKE $sessionId";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                $studentId = $row['studentid'];

                $query2 = "SELECT * FROM " . $CFG->prefix . "user WHERE id LIKE $studentId";

                $result2 = mysqli_query($conn, $query2);

                if (!$result2)
                {
                    $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                    $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                    echo $post_data;
                } else
                {
                    if (mysqli_num_rows($result2) > 0)
                    {
                        while ($row2 = mysqli_fetch_array($result2))
                        {
                            $statusId = $row['statusid'];

                            $query3 = "SELECT * FROM " . $CFG->prefix . "attendance_statuses WHERE id LIKE $statusId";

                            $result3 = mysqli_query($conn, $query3);

                            if (!$result3)
                            {
                                $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                                echo $post_data;
                            } else
                            {
                                if (mysqli_num_rows($result3) > 0)
                                {
                                    $row3 = mysqli_fetch_array($result3);

                                    $attendanceid = $row3['id'];
                                    $acronym = $row3['acronym'];
                                    $description = $row3['description'];
                                    $grade = $row3['grade'];
                                    $visible = $row3['visible'];
                                    $deleted = $row3['deleted'];

                                }
                            }

                            $takenById = $row['takenby'];

                            $query3 = "SELECT * FROM " . $CFG->prefix . "user WHERE id LIKE $takenById";

                            $result3 = mysqli_query($conn, $query3);

                            if (!$result3)
                            {
                                $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                                echo $post_data;
                            } else
                            {
                                if (mysqli_num_rows($result3) > 0)
                                {
                                    $row4 = mysqli_fetch_array($result3);
                                    $takenBy = array('id' => $row4['id'], 'first_name' => $row4['firstname'], 'last_name' => $row4['lastname'], 'user_name' => $row4['username']);
                                }

                            }

                            if ($row2['id'] != $userId)
                            {
                                $student[] = array('id' => $row2['id'], 'first_name' => $row2['firstname'], 'last_name' => $row2['lastname'], 'user_name' => $row2['username'], 'remarks' => $row['remarks'], 'status_set' => $row['statusset'], 'time_taken' => $row['timetaken'], 'status_id' => $attendanceid, 'acronym' => $acronym, 'description' => $description, 'taken_by' => $takenBy);
                            }
                        }
                    }
                }

            }

        }

        //$post_data=array('student'=>$student);
        $post_data = json_encode(array('attendance_data' => $student));
        echo $post_data;

    }


}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function test($conn, $userName, $password)
{

    global $CFG;

    $userId = "";
    $firstName = "";
    $lastName = "";
    $fullName = "";
    $profilePic = "";
    $courses;

    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/login/token.php?username=$userName&password=$password&service=moodle_mobile_app";
    $data = file_get_contents($url);
    $result = json_decode($data, TRUE);
    //var_dump($result);
    $token = $result['token'];


    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_webservice_get_siteinfo";
    $data = file_get_contents($url);
    //$result = json_decode($data, true);
    $response = new SimpleXMLElement($data);
    //var_dump($response);

    $response->getName();

    foreach ($response->children() as $child)
    {
        //echo $child->getName()."\n";

        foreach ($child->children() as $child2)
        {
            //echo $child2->getName()."\n";
            //echo $child2['name'];
            if ($child2['name'] == "userid")
            {
                foreach ($child2->children() as $child3)
                {
                    $userId = (string)$child3;
                    //print_r($userId);
                }
            }
            if ($child2['name'] == "firstname")
            {
                foreach ($child2->children() as $child3)
                {
                    $firstName = (string)$child3;
                }
            }
            if ($child2['name'] == "lastname")
            {
                foreach ($child2->children() as $child3)
                {
                    $lastName = (string)$child3;
                }
            }
            if ($child2['name'] == "fullname")
            {
                foreach ($child2->children() as $child3)
                {
                    $fullName = (string)$child3;
                }
            }
            if ($child2['name'] == "userpictureurl")
            {
                foreach ($child2->children() as $child3)
                {
                    $profilePic = (string)$child3;
                }
            }
            /*foreach($child2->children() as $child3)
            {
                echo $child3->getName();
                echo $child3;
            }*/
        }
    }


    $query = "SELECT roleid FROM " . $CFG->prefix . "role_assignments WHERE userid LIKE $userId LIMIT 1";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                $roleId = $row['roleid'];
            }
            $query2 = "SELECT shortname FROM " . $CFG->prefix . "role WHERE id LIKE $roleId LIMIT 1";

            $result2 = mysqli_query($conn, $query2);

            if (!$result2)
            {
                $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                echo $post_data;
            } else
            {
                if (mysqli_num_rows($result2) > 0)
                {
                    while ($row2 = mysqli_fetch_array($result2))
                    {
                        $roleShortName = $row2['shortname'];
                    }
                }
            }
        }

    }


    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/webservice/rest/server.php?wstoken=56840502d9debd802e2dc6213f10b8c2&wsfunction=moodle_user_get_users_by_id&userids[0]=$userId";

    $data = file_get_contents($url);
    //$result = json_decode($data, true);
    $response = new SimpleXMLElement($data);
    //var_dump($response);

    $response->getName();


    foreach ($response->children() as $child)
    {
        //echo $child->getName()."\n";
        foreach ($child->children() as $child2)
        {
            //echo $child2->getName()."\n";
            foreach ($child2->children() as $child3)
            {
                //echo $child3->getName()."\n";
                if ($child3['name'] == "enrolledcourses")
                {
                    foreach ($child3->children() as $child4)
                    {
                        //echo $child4->getName()."\n";
                        foreach ($child4->children() as $child5)
                        {
                            //echo $child5->getName()."\n";
                            foreach ($child5->children() as $child6)
                            {
                                $courseId;
                                $courseFullName;
                                $courseShortName;
                                //echo $child6->getName()."\n";
                                if ($child6['name'] == "id")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseId = (string)$child7;
                                    }
                                }
                                if ($child6['name'] == "fullname")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseFullName = (string)$child7;
                                    }
                                }
                                if ($child6['name'] == "shortname")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseShortName = (string)$child7;
                                    }
                                }
                            }

                            $courses[] = array('id' => $courseId, 'full_name' => $courseFullName, 'short_name' => $courseShortName);

                        }
                    }
                }
            }
        }
    }


    $post_data = array('id' => $userId, 'user_name' => $userName, 'first_name' => $firstName, 'last_name' => $lastName, 'full_name' => $fullName, 'profile_pic_url' => $profilePic, 'role_id' => $roleId, 'role_short_name' => $roleShortName, 'course' => $courses);
    $post_data = json_encode(array('user' => $post_data));
    echo $post_data;


}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function addSession($conn, $attendanceId, $sessionDate, $duration, $timeModified, $description)
{
    global $CFG;

    $query = "INSERT INTO " . $CFG->prefix . "attendance_sessions (id,attendanceid,groupid,sessdate,duration,lasttaken,lasttakenby,timemodified,description,descriptionformat,studentscanmark) VALUES(NULL,'$attendanceId',0,'$sessionDate','$duration',NULL,0,'$timeModified','$description',1,0)";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing INSERT query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        $post_data = array('message' => 'success', 'comment' => 'session added successfully');
        $post_data = json_encode(array('response' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    }


}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function addAttendance($conn, $sessionId, $statusSet, $takenBy, $time, $data)
{
    $values = '';

    $jsonData = $data;

    $jsonPure = html_entity_decode($jsonData);

    $attendanceArr = json_decode($jsonPure, TRUE);

    foreach ($attendanceArr as $single)
    {
        foreach ($single as $s)
        {
            $studentId = $s["i"];
            $statusId = $s["s"];
            $timeTaken = $time;
            $remarks = $s["r"];

            $values .= "(NULL,'$sessionId','$studentId','$statusId','$statusSet','$timeTaken','$takenBy','$remarks'),";
        }
    }

    $values = rtrim($values, ',');

    $query = "INSERT INTO " . $CFG->prefix . "attendance_log (id,sessionid,studentid,statusid,statusset,timetaken,takenby,remarks) VALUES $values";

    //echo $query;

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing INSERT query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {

        $query = "UPDATE " . $CFG->prefix . "attendance_sessions SET `lasttaken`=$timeTaken,`lasttakenby`=$takenBy,`timemodified`=$timeTaken WHERE `id` LIKE $sessionId";

        $result = mysqli_query($conn, $query);

        if (!$result)
        {
            $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing INSERT query');
            $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
            echo $post_data;
        } else
        {
            $post_data = array('message' => 'success', 'comment' => 'attendance added successfully');
            $post_data = json_encode(array('response' => $post_data), JSON_FORCE_OBJECT);
            echo $post_data;
        }


    }

}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function updateAttendance($conn, $sessionId, $takenBy, $time, $data)
{
    global $CFG;

    $jsonData = $data;

    $jsonPure = html_entity_decode($jsonData);

    $attendanceArr = json_decode($jsonPure, TRUE);

    foreach ($attendanceArr as $single)
    {
        foreach ($single as $s)
        {
            $studentId[] = $s["i"];
            $statusId[] = $s["s"];
            $timeTaken[] = $time;
            $remarks[] = $s["r"];
        }
    }

    for ($i = 0; $i < count($studentId); $i++)
    {
        $query = "UPDATE " . $CFG->prefix . "attendance_log SET statusid='$statusId[$i]', timetaken='$timeTaken[$i]', remarks='$remarks[$i]', takenby='$takenBy' WHERE studentid LIKE '$studentId[$i]' AND sessionid LIKE '$sessionId'";

        $result = mysqli_query($conn, $query);

        if (!$result)
        {
            $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing UPDATE query');
            $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
            die($post_data);
        } else
        {
            if ($i == count($studentId) - 1)
            {
                $query = "UPDATE " . $CFG->prefix . "attendance_sessions SET `lasttaken`=$time,`lasttakenby`=$takenBy,`timemodified`=$time WHERE `id` LIKE $sessionId";

                $result = mysqli_query($conn, $query);

                if (!$result)
                {
                    $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing INSERT query');
                    $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                    echo $post_data;
                } else
                {
                    $post_data = array('message' => 'success', 'comment' => 'attendance updated successfully');
                    $post_data = json_encode(array('response' => $post_data), JSON_FORCE_OBJECT);
                    echo $post_data;
                }

            }
        }

    }

}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getCourses($conn, $token, $userId)
{
    global $CFG;

    $courses = array();

    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_user_get_users_by_id&userids[0]=$userId";

    $data = file_get_contents($url);
    //$result = json_decode($data, true);
    $response = new SimpleXMLElement($data);
    //var_dump($response);

    $response->getName();

    foreach ($response->children() as $child)
    {
        //echo $child->getName()."\n";
        foreach ($child->children() as $child2)
        {
            //echo $child2->getName()."\n";
            foreach ($child2->children() as $child3)
            {
                //echo $child3->getName()."\n";
                if ($child3['name'] == "enrolledcourses")
                {
                    foreach ($child3->children() as $child4)
                    {
                        //echo $child4->getName()."\n";
                        foreach ($child4->children() as $child5)
                        {
                            //echo $child5->getName()."\n";
                            foreach ($child5->children() as $child6)
                            {
                                $courseId;
                                $courseFullName;
                                $courseShortName;
                                //echo $child6->getName()."\n";
                                if ($child6['name'] == "id")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseId = (string)$child7;
                                    }
                                }
                                if ($child6['name'] == "fullname")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseFullName = (string)$child7;
                                    }
                                }
                                if ($child6['name'] == "shortname")
                                {
                                    foreach ($child6->children() as $child7)
                                    {
                                        $courseShortName = (string)$child7;
                                    }
                                }
                            }

                            //$courses[]=array('id'=>$courseId,'full_name'=>$courseFullName,'short_name'=>$courseShortName);
                        }
                    }
                }
            }
        }
    }

    $post_data = json_encode(array('course' => $courses));
    echo $post_data;

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function getSessions($conn, $courseId, $attendanceTypeId, $flag = FALSE)
{
    global $CFG;

    $sessions = array();

    $query = "SELECT * FROM " . $CFG->prefix . "attendance WHERE course LIKE $courseId AND id LIKE $attendanceTypeId";


    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                //echo 'inside it!!';

                $val = $row['id'];

                $query2 = "SELECT * FROM " . $CFG->prefix . "attendance_sessions WHERE attendanceid LIKE $val";

//echo $query2."<br/><br/><br/>";

                $result2 = mysqli_query($conn, $query2);

                if (!$result2)
                {
                    $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
                    $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
                    echo $post_data;
                } else
                {
                    if (mysqli_num_rows($result2) > 0)
                    {
//echo "data<br/>";
                        while ($row2 = mysqli_fetch_array($result2))
                        {
                            //echo $row2['id']."<br/><br/>";
                            $desc = strip_tags($row2['description']);
                            $sessions[] = array('id' => $row2['id'], 'groupid' => $row2['groupid'], 'sessdate' => $row2['sessdate'], 'duration' => $row2['duration'], 'lasttaken' => $row2['lasttaken'], 'lasttakenby' => $row2['lasttakenby'], 'timemodified' => $row2['timemodified'], 'description' => $desc, 'descriptionformat' => $row2['descriptionformat'], 'studentscanmark' => $row2['studentscanmark']);
                        }

                    }
                }
            }
        }


        if ($flag)
        {
            //var_dump($sessions);
            return $sessions;
        } else
        {
            $post_data = json_encode(array('sessions' => $sessions));
            echo $post_data;
        }

    }

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getEnrolledStudents($conn, $token, $courseId)
{
    global $CFG;

    $students = array();

    $url2 = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_enrol_get_enrolled_users&courseid=$courseId";

    $data2 = file_get_contents($url2);
    //$result = json_decode($data, true);
    $response2 = new SimpleXMLElement($data2);
    //var_dump($response);

    $response2->getName();

    foreach ($response2->children() as $c)
    {
        //echo $c->getName()."\n";
        foreach ($c->children() as $c2)
        {
            //echo $c2->getName()."\n";
            foreach ($c2->children() as $c3)
            {
                $studentUserId;
                $studentFirstName;
                $studentLastName;
                $studentFullName;
                $studentUserName;
                $studentProfilePic;

                //echo $c3->getName()."\n";
                if ($c3['name'] == "userid")
                {
                    foreach ($c3->children() as $c4)
                    {
                        //echo $c4."\n";
                        $studentUserId = (string)$c4;
                    }
                }
                if ($c3['name'] == "firstname")
                {
                    foreach ($c3->children() as $c4)
                    {
                        //echo $c4."\n";
                        $studentFirstName = (string)$c4;
                    }
                }
                if ($c3['name'] == "lastname")
                {
                    foreach ($c3->children() as $c4)
                    {
                        //echo $c4."\n";
                        $studentLastName = (string)$c4;
                    }
                }
                if ($c3['name'] == "fullname")
                {
                    foreach ($c3->children() as $c4)
                    {
                        //echo $c4."\n";
                        $studentFullName = (string)$c4;
                    }
                }
                if ($c3['name'] == "username")
                {
                    foreach ($c3->children() as $c4)
                    {
                        //echo $c4."\n";
                        $studentUserName = (string)$c4;
                    }
                }
                if ($c3['name'] == "profileimgurl")
                {
                    foreach ($c3->children() as $c4)
                    {
                        //echo $c4."\n";
                        $studentProfilePic = (string)$c4;
                    }
                }
            }
            $students[] = array('user_id' => $studentUserId, 'first_name' => $studentFirstName, 'last_name' => $studentLastName, 'full_name' => $studentFullName, 'user_name' => $studentUserName, 'profile_pic_url' => $studentProfilePic);
        }
    }

    $post_data = json_encode(array('students' => $students));
    echo $post_data;

}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function deleteSession($conn, $userName, $password, $sessionId)
{
    global $CFG;

    $url = "http://" . $CFG->dbhost . "" . MOODLE_DIR . "/login/token.php?username=$userName&password=$password&service=moodle_mobile_app";
    $data = file_get_contents($url);
    $result = json_decode($data, TRUE);
    //var_dump($result);
    if (isset($result['token']))
    {
        $token = $result['token'];
    } else
    {
        $post_data = array('message' => 'login error', 'comment' => 'invalid user name or password');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        die($post_data);
    }


    $query = "SELECT * FROM " . $CFG->prefix . "attendance_log WHERE sessionid LIKE $sessionId";

    $result = mysqli_query($conn, $query);

    if (!mysqli_num_rows($result) > 0)
    {
        $query = "DELETE FROM " . $CFG->prefix . "attendance_sessions WHERE id LIKE $sessionId";
    } else
    {
        $query = "DELETE " . $CFG->prefix . "attendance_sessions, " . $CFG->prefix . "attendance_log FROM " . $CFG->prefix . "attendance_sessions INNER JOIN " . $CFG->prefix . "attendance_log ON " . $CFG->prefix . "attendance_sessions.id = " . $CFG->prefix . "attendance_log.sessionid WHERE " . $CFG->prefix . "attendance_sessions.id LIKE '$sessionId'";

    }

    //echo $query;

    $result = mysqli_query($conn, $query);

    //var_dump($result);

    if (!mysqli_affected_rows($conn) > 0)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing DELETE query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        $post_data = array('message' => 'success', 'comment' => 'session deleted successfully');
        $post_data = json_encode(array('response' => $post_data), JSON_FORCE_OBJECT);
        die($post_data);
    }


}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getAttendanceType($conn, $courseId)
{
    global $CFG;

    $attendance = array();

    $sessions = array();

    $query = "SELECT * FROM " . $CFG->prefix . "attendance WHERE course LIKE '$courseId'";

    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing DELETE query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_assoc($result))
            {
                //print_r(getSessions($conn,$courseId,$row['id'],true));
                $attendance[] = array('id' => $row['id'], 'course' => $row['course'], 'name' => $row['name'], 'grade' => $row['grade'], 'sessions' => getSessions($conn, $courseId, $row['id'], TRUE), 'statuses' => getAttendanceStatus($conn, $row['id']));

            }

            $post_data = json_encode(array('attendance' => $attendance));
            echo $post_data;

        }

    }

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getAttendanceStatus($conn, $attendanceId)
{
    global $CFG;

    $query = "SELECT * FROM " . $CFG->prefix . "attendance_statuses WHERE attendanceid LIKE $attendanceId";


    $result = mysqli_query($conn, $query);

    if (!$result)
    {
        $post_data = array('message' => mysqli_errno($conn) . ": " . mysqli_error($conn), 'comment' => 'while performing SELECT query');
        $post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
        echo $post_data;
    } else
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row3 = mysqli_fetch_array($result))
            {
                $status[] = array('id' => $row3['id'], 'acronym' => $row3['acronym'], 'description' => $row3['description'], 'grade' => $row3['grade'], 'visible' => $row3['visible'], 'deleted' => $row3['deleted']);
            }

        }
        return $status;
    }
}
