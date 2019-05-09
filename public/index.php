<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register routes
require __DIR__ . '/../src/routes.php';

//setup middleware
require __DIR__ . '/../src/middleware.php';

//$app->post('/login','login'); /* User login */
//$app->post('/signup','signup'); /* User Signup  */
//$app->get('/getFeed','getFeed'); /* User Feeds  */
//$app->post('/feed','feed'); /* User Feeds  */
//$app->post('/feedUpdate','feedUpdate'); /* User Feeds  */
//$app->post('/feedDelete','feedDelete'); /* User Feeds  */
//$app->post('/getImages', 'getImages');


/************************* USER LOGIN *************************************/
/* ### User login ### */
function login($request,$response) {
    $data = json_decode($request->getBody());
    try {
        
        $db = getConnection();
        $userData ='';
        $sql = "SELECT user_id, name, email, username FROM users WHERE (username=:username or email=:username) and password=:password ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $data->username, PDO::PARAM_STR);
        $password=hash('sha256',$data->password);
        $stmt->bindParam("password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $mainCount=$stmt->rowCount();
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
        
        if(!empty($userData))
        {
            $user_id=$userData->user_id;
            $userData->token = apiToken($user_id);
        }
        
        $db = null;
         if($userData){
             return $response->withJson('{"userData": ' .$userData . '}',200);
            } else {
              return $response->withJson('{"error":{"text":"Bad request wrong username and password"}}',401);
         }

           
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


/* ### User registration ### */
//function signup() {
//    $request = \Slim\Slim::getInstance()->request();
//    $data = json_decode($request->getBody());
//    $email=$data->email;
//    $name=$data->name;
//    $username=$data->username;
//    $password=$data->password;
//
//    try {
//
//        $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
//        $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
//        $password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);
//
//        echo $email_check.'<br/>'.$email;
//
//        if (strlen(trim($username))>0 && strlen(trim($password))>0 && strlen(trim($email))>0 && $email_check>0 && $username_check>0 && $password_check>0)
//        {
//            echo 'here';
//            $db = getDB();
//            $userData = '';
//            $sql = "SELECT user_id FROM users WHERE username=:username or email=:email";
//            $stmt = $db->prepare($sql);
//            $stmt->bindParam("username", $username,PDO::PARAM_STR);
//            $stmt->bindParam("email", $email,PDO::PARAM_STR);
//            $stmt->execute();
//            $mainCount=$stmt->rowCount();
//            $created=time();
//            if($mainCount==0)
//            {
//
//                /*Inserting user values*/
//                $sql1="INSERT INTO users(username,password,email,name)VALUES(:username,:password,:email,:name)";
//                $stmt1 = $db->prepare($sql1);
//                $stmt1->bindParam("username", $username,PDO::PARAM_STR);
//                $password=hash('sha256',$data->password);
//                $stmt1->bindParam("password", $password,PDO::PARAM_STR);
//                $stmt1->bindParam("email", $email,PDO::PARAM_STR);
//                $stmt1->bindParam("name", $name,PDO::PARAM_STR);
//                $stmt1->execute();
//
//                $userData=internalUserDetails($email);
//
//            }
//
//            $db = null;
//
//
//            if($userData){
//               $userData = json_encode($userData);
//                echo '{"userData": ' .$userData . '}';
//            } else {
//               echo '{"error":{"text":"Enter valid data"}}';
//            }
//
//
//        }
//        else{
//            echo '{"error":{"text":"Enter valid data"}}';
//        }
//    }
//    catch(PDOException $e) {
//        echo '{"error":{"text":'. $e->getMessage() .'}}';
//    }
//}

// Run app
$app->run();
?>
