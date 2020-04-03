<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

function getPasswordFromDB($conn,$email){
    $stmt = $conn->prepare("select Password from user where Email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row["Password"];
    }
    else{
        return "";
    }
}

// Login //
$app->post('/login', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];

    $pwdInDB = getPasswordFromDB($conn,$bodyArry['Email']);//passhash
    //echo $bodyArry['password']." ";
    //echo  $pwdInDB." ";
    if(password_verify($bodyArry['Password'],$pwdInDB)){
        $stmt = $conn->prepare("select * from user where Email = ? and Password = ?");
        $stmt->bind_param("ss",$bodyArry['Email'], $pwdInDB);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            array_push($data,$row);
        }
        $json = json_encode($data);
        $response->getBody()->write($json);
    }
    else{
        $response->getBody()->write("false");

    }
    /*return $response->withHeader('Content-Type', 'application/json');*/
    return $response
		->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// Change password //
$app->post('/changepassword', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);
    
    $conn = $GLOBALS['dbconn'];

    //$oldpassCorrect = "false";
    //$result = null;
    $pwdInDB = getPasswordFromDB($conn,$bodyArry['Email']);
    if(password_verify($bodyArry['Oldpassword'],$pwdInDB)){
        /*$check = $conn->prepare("select * from employees where email = ? and password = ?");
        $check->bind_param("ss",$bodyArry['email'], $pwdInDB);
        $check-> execute();
        $result = $check->get_result();
        $oldpassCorrect = "true";*/
        $hashed = password_hash($bodyArry['Newpassword'],PASSWORD_DEFAULT);
        $stmt = $conn->prepare("update user set Password = ? where Email = ?");
        $stmt->bind_param("ss",$hashed,$bodyArry['Email']);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $response->getBody()->write($affected.""); 
        
    }
    else{
       // echo  $oldpassCorrect;
        echo "Old password Not correct";
    }
    /*if ($oldpassCorrect == "true") {
        if($result->num_rows > 0 ){
            $row = $result->fetch_assoc(); 
            
        }
    }*/
    
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;   
});
//


// singup //
$app->post('/signup', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];
    $hashed = password_hash($bodyArry['Password'],PASSWORD_DEFAULT);
    $stmt = $conn->prepare("insert into user ".
    " (Userid,Email,Password,firstname,lastname,gender)".
    " values (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss",
    $bodyArry['Userid'], $bodyArry['Email'], $hashed,
    $bodyArry['firstname'], $bodyArry['lastname'], $bodyArry['gender']);
  
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

//  editprofile //
$app->post('/editprofile', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("update user ".
    "set firstname = ?,lastname = ?,gender = ? where Userid = ?");
    $stmt->bind_param("ssss",$bodyArry['firstname'], $bodyArry['lastname'],
    $bodyArry['gender'], $bodyArry['Userid']);
  
    
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// showprofile // 
$app->get('/showprofile/{userid}', function (Request $request, Response $response, array $args) {
    $userid = $args['userid'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from user where Userid = '$userid'";
    $result = $conn->query($sql);
    $data = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data,$row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//