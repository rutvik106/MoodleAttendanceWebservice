<?php	//this is moodle app webservice for attendance module
		//include basic functions for attendance get,set,insert,update,delete,modify....
		//includes DB connection
		
$mysql_host = 'localhost';
$mysql_database = 'moodle';
$mysql_user = 'root';
$mysql_password = 'reset@123';

$conn = mysqli_connect($mysql_host,$mysql_user,$mysql_password,$mysql_database);

if (!$conn)
{
	die('Could not connect error: ' . mysqli_error());
}

if(isset($_GET['method']) && $_GET['method']!="")
{
			


switch($_GET['method'])
{

	case 'get_table_names':
		getTableNames($conn);
	break;
	
	case 'get_table_columns':
		if(isset($_GET['table_name']) && $_GET['table_name']!="")
		{
			getTableColumns($conn,$_GET['table_name']);
		}
		else
		{
			$post_data=array('message'=>'missing parameters','comment'=>'require param [table_name]');
			$post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
			echo $post_data;
		}		
	break;
	
	case 'get_table_data':
		if(isset($_GET['table_name']) && $_GET['table_name']!="")
		{
			getTableData($conn,$_GET['table_name']);
		}
		else
		{
			$post_data=array('message'=>'missing parameters','comment'=>'require param [table_name]');
			$post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
			echo $post_data;
		}		
	break;
	
	case 'login':
		if(isset($_GET['user_name']) && $_GET['user_name']!="" && isset($_GET['password']) && $_GET['password']!="")
		{
			login($conn,$_GET['user_name'],$_GET['password']);
		}
		else
		{
			$post_data=array('message'=>'missing parameters','comment'=>'require param [user_name,password]');
			$post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
			echo $post_data;
		}		
	break;
	
	default:
	
		echo("invalid method name or method name not provided");		
		
	break;

}

}
else
{
	echo "<h3>***Welcome to Moodle custome webservice, Successfully Connected to MOODLE DB available method=[get_table_names,get_table_columns,get_table_data,login]***</h3>"."\n\n eg: <b>[host]</b><i>/webservice.php?method=login&user_name=rutvik&password=Reset@123</i>\n\n <h2>For any query Contact: <font color=#FF0000>+91-9409210488</font> (Rutvik D Mehta)</h2>";
}

	
function getTableNames($conn)
{
	$query="SHOW TABLES FROM moodle";
		
	$result=mysqli_query($conn,$query);
	
	$count=0;
			
	if(!$result)
	{
		$post_data=array('message'=>mysqli_errno($conn) . ": " . mysqli_error($conn),'comment'=>'while performing SHOW TABLES query');
		$post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
		echo $post_data;
	}
	else
	{
		if(mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{			
				$count++;
				$data=array('no'=>$count,'name'=>$row['Tables_in_moodle']);
				$post_data[]= $data;
				
			}
			$post_data = json_encode(array('tables' => $post_data));
			echo $post_data;
			
		}
	}
}

function getTableColumns($conn,$tableName)
{
	$query="SHOW COLUMNS FROM $tableName";
	
	$result=mysqli_query($conn,$query);
	
	$count=0;
			
	if(!$result)
	{
		$post_data=array('message'=>mysqli_errno($conn) . ": " . mysqli_error($conn),'comment'=>'while performing SHOW COLUMNS query');
		$post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
		echo $post_data;
	}
	else
	{
		while($row =mysqli_fetch_array($result))
		{
			$count++;
			$data=array('no'=>$count,'name'=>$row['Field'],'type'=>$row['Type']);
			$post_data[]= $data;
		}
		$post_data = json_encode(array('columns' => $post_data));
		echo $post_data;
	}
}

function getTableData($conn,$tableName)
{
	$query="SELECT * FROM $tableName";
	
	$result=mysqli_query($conn,$query);
			
	if(!$result)
	{
		$post_data=array('message'=>mysqli_errno($conn) . ": " . mysqli_error($conn),'comment'=>'while performing SELECT query');
		$post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
		echo $post_data;
	}
	else
	{
		if(mysqli_num_rows($result)>0)
		{
			while($row = mysqli_fetch_assoc($result))
			{						
				$encode[]=$row;						
			}
			
			$data['rows']=$encode;
					
			echo json_encode($data);
		}
	}
}


function login($conn,$userName,$password)
{
		$userId="";
		$firstName="";
		$lastName="";
		$fullName="";
		$profilePic="";
		$courses;
	
		$url="http://192.168.1.100/login/token.php?username=$userName&password=$password&service=moodle_mobile_app";	
		$data = file_get_contents($url); 		
		$result = json_decode($data, true);
		//var_dump($result);
		$token=$result['token'];
		
		
		
		$url="http://192.168.1.100/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_webservice_get_siteinfo";		
		$data = file_get_contents($url); 
		//$result = json_decode($data, true);
		$response = new SimpleXMLElement($data);
		//var_dump($response);
				
		$response->getName();
		
		foreach($response->children() as $child)
		{
			//echo $child->getName()."\n";
			
			foreach($child->children() as $child2)
			{
				//echo $child2->getName()."\n";
				//echo $child2['name'];
				if($child2['name']=="userid")
				{
					foreach($child2->children() as $child3)
					{
						$userId=(string)$child3;
						//print_r($userId);
					}
				}
				if($child2['name']=="firstname")
				{
					foreach($child2->children() as $child3)
					{
						$firstName=(string)$child3;
					}
				}
				if($child2['name']=="lastname")
				{
					foreach($child2->children() as $child3)
					{
						$lastName=(string)$child3;
					}
				}
				if($child2['name']=="fullname")
				{
					foreach($child2->children() as $child3)
					{
						$fullName=(string)$child3;
					}
				}
				if($child2['name']=="userpictureurl")
				{
					foreach($child2->children() as $child3)
					{
						$profilePic=(string)$child3;
					}
				}
				/*foreach($child2->children() as $child3)
				{
					echo $child3->getName();
					echo $child3;
				}*/
			}
		}
		
		
		
		$url="http://192.168.1.100/webservice/rest/server.php?wstoken=56840502d9debd802e2dc6213f10b8c2&wsfunction=moodle_user_get_users_by_id&userids[0]=$userId";
		
		$data = file_get_contents($url); 
		//$result = json_decode($data, true);
		$response = new SimpleXMLElement($data);
		//var_dump($response);
		
		$response->getName();
		
		foreach($response->children() as $child)
		{
			//echo $child->getName()."\n";
			foreach($child->children() as $child2)
			{
				//echo $child2->getName()."\n";
				foreach($child2->children() as $child3)
				{
					//echo $child3->getName()."\n";
					if($child3['name']=="enrolledcourses")
					{
						foreach($child3->children() as $child4)
						{
							//echo $child4->getName()."\n";
							foreach($child4->children() as $child5)
							{
								//echo $child5->getName()."\n";
								foreach($child5->children() as $child6)
								{
									$courseId;
									$courseFullName;
									$courseShortName;
									//echo $child6->getName()."\n";
									if($child6['name']=="id")
									{
										foreach($child6->children() as $child7)
										{
											$courseId=(string)$child7;	
										}
									}
									if($child6['name']=="fullname")
									{
										foreach($child6->children() as $child7)
										{
											$courseFullName=(string)$child7;	
										}
									}
									if($child6['name']=="shortname")
									{
										foreach($child6->children() as $child7)
										{
											$courseShortName=(string)$child7;	
										}
									}								
								}
								
								$url2="http://192.168.1.100/webservice/rest/server.php?wstoken=$token&wsfunction=moodle_enrol_get_enrolled_users&courseid=$courseId";
								
								$data2 = file_get_contents($url2); 
								//$result = json_decode($data, true);
								$response2 = new SimpleXMLElement($data2);
								//var_dump($response);
								
								$response2->getName();
								
								foreach($response2->children() as $c)
								{
									//echo $c->getName()."\n";
									foreach($c->children() as $c2)
									{
										//echo $c2->getName()."\n";
										foreach($c2->children() as $c3)
										{
											$studentUserId;
											$studentFirstName;
											$studentLastName;
											$studentFullName;
											$studentUserName;
											$studentProfilePic;
											
											//echo $c3->getName()."\n";
											if($c3['name']=="userid")
											{
												foreach($c3->children() as $c4)
												{
													//echo $c4."\n";
													$studentUserId=(string)$c4;	
												}
											}
											if($c3['name']=="firstname")
											{
												foreach($c3->children() as $c4)
												{
													//echo $c4."\n";
													$studentFirstName=(string)$c4;	
												}
											}
											if($c3['name']=="lastname")
											{
												foreach($c3->children() as $c4)
												{
													//echo $c4."\n";	
													$studentLastName=(string)$c4;
												}
											}
											if($c3['name']=="fullname")
											{
												foreach($c3->children() as $c4)
												{
													//echo $c4."\n";	
													$studentFullName=(string)$c4;
												}
											}
											if($c3['name']=="username")
											{
												foreach($c3->children() as $c4)
												{
													//echo $c4."\n";
													$studentUserName=(string)$c4;	
												}
											}
											if($c3['name']=="profileimgurl")
											{
												foreach($c3->children() as $c4)
												{
													//echo $c4."\n";
													$studentProfilePic=(string)$c4;	
												}
											}
										}
										$students[]=array('user_id'=>$studentUserId,'first_name'=>$studentFirstName,'last_name'=>$studentLastName,'full_name'=>$studentFullName,'user_name'=>$studentUserName,'profile_pic_url'=>$studentProfilePic);
									}
								}
								
								$courses[]=array('id'=>$courseId,'full_name'=>$courseFullName,'short_name'=>$courseShortName,'enrolled_students'=>$students);
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
				
		$query="SELECT roleid FROM mdl_role_assignments WHERE userid LIKE $userId LIMIT 1";
		
		$result=mysqli_query($conn,$query);
		
		if(!$result)
		{
			$post_data=array('message'=>mysqli_errno($conn) . ": " . mysqli_error($conn),'comment'=>'while performing SELECT query');
			$post_data = json_encode(array('error' => $post_data), JSON_FORCE_OBJECT);
			echo $post_data;
		}
		else
		{
			if(mysqli_num_rows($result)>0)
			{
				while($row =mysqli_fetch_array($result))
				{
					$roleId=$row['roleid'];
				}
				$query2="SELECT shortname FROM mdl_role WHERE id LIKE $roleId LIMIT 1";
		
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
							$roleShortName=$row2['shortname'];
						}
						
					}
				}
			}
		}
		
		$post_data=array('id'=>$userId,'user_name'=>$userName,'first_name'=>$firstName,'last_name'=>$lastName,'full_name'=>$fullName,'profile_pic_url'=>$profilePic,'role_id'=>$roleId,'role_short_name'=>$roleShortName,'course'=>$courses);
		$post_data = json_encode(array('user' => $post_data));
		echo $post_data;
		
	
}






		



