<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// New Filecabinet //
$app->post('/newfilecabinet', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);
    $filecabinetid = $bodyArry['Userid']."_".$bodyArry['Name'];
    $userstatus = "ผู้รับผิดชอบ";

    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("insert into filecabinet ".
    " (Filecabinetid,Name,Userid,Userstatus)".
    " values (?,?,?,?)");
    $stmt->bind_param("ssss",
    $filecabinetid, $bodyArry['Name'], $bodyArry['Userid'],$userstatus);
  
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

function CheckFilecabinet($conn,$filecabinetid){
    $stmt = $conn->prepare("select Documentid from document where Filecabinetid=?");
    $stmt->bind_param("s",$filecabinetid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        return true;
    }
    else{
        return false;
    }
}
// Delete Filecabinet //
$app->get('/deletefilecabinet/{filecabinetid}', function (Request $request, Response $response, array $args) {
    $filecabinetid = $args['filecabinetid'];
    $conn = $GLOBALS['dbconn'];
    $candelete = CheckFilecabinet($conn,$filecabinetid);
    
    if ($candelete == true) {
        $stmt = $conn->prepare('delete from filecabinet where Filecabinetid = ?');
        $stmt->bind_param("s",$filecabinetid);
        $stmt->execute();

        $stmt = $conn->prepare('delete from document where Filecabinetid = ?');
        $stmt->bind_param("s",$filecabinetid);
        $stmt->execute();

        $result = $stmt->affected_rows;
        $response->getBody()->write($result."");
    }
    else{
        $response->getBody()->write("-1");
    }
    
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// Change Name Filecabinet //
$app->post('/changenamefilecabinet', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("update filecabinet set Name = ? where Filecabinetid = ?");
    $stmt->bind_param("ss",$bodyArry['Name'],$bodyArry['Filecabinetid']);
  
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

function CheckUserstatus($conn,$userid,$filecabinetid){
    $stmt = $conn->prepare("select Userstatus from filecabinet where Filecabinetid=? and Userid=?");
    $stmt->bind_param("ss",$filecabinetid,$userid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        return true;
    }
    else{
        return false;
    }
    //select Userstatus from filecabinet where Filecabinetid='future1507_test' and Userid='por031'
}
// Add ผู้รับผิดชอบ //
$app->post('/addresponsible', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);
    $userstatus = "ผู้รับผิดชอบ";

    $conn = $GLOBALS['dbconn'];
    $have = CheckUserstatus($conn,$bodyArry['Userid'],$bodyArry['Filecabinetid']);
    //echo $have." ";
    if ($have == true) {
        $stmt = $conn->prepare("update filecabinet set Userstatus = ? 
        where Filecabinetid = ? and Userid = ?");
        $stmt->bind_param("sss",$userstatus,$bodyArry['Filecabinetid'],$bodyArry['Userid']);
    
        $stmt->execute();
        $result = $stmt->affected_rows;

        $response->getBody()->write($result."");
    }
    else {
        $stmt = $conn->prepare("insert into filecabinet ".
        " (Filecabinetid,Name,Userid,Userstatus)".
        " values (?,?,?,?)");
        $stmt->bind_param("ssss",
        $bodyArry['Filecabinetid'], $bodyArry['Name'], $bodyArry['Userid'],$userstatus);
    
        $stmt->execute();
        $result = $stmt->affected_rows;

        $response->getBody()->write($result."");
    }
    
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// Delete ผู้รับผิดชอบ หรือ ผู้ใช้งานตู้เอกสาร//
$app->post('/deleteRorU', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("delete from filecabinet 
    where Filecabinetid = ? and
    Userid = ?");
    $stmt->bind_param("ss",
    $bodyArry['Filecabinetid'], $bodyArry['Userid']);
  
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//


// Add ผู้ใช้งานตู้เอกสาร //
$app->post('/adduser', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);
    $userstatus = "ผู้ใช้งานตู้เอกสาร";

    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("insert into filecabinet ".
    " (Filecabinetid,Name,Userid,Userstatus)".
    " values (?,?,?,?)");
    $stmt->bind_param("ssss",
    $bodyArry['Filecabinetid'], $bodyArry['Name'], $bodyArry['Userid'],$userstatus);
  
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// Show Filecabinet//
$app->get('/showfilecabinet/{userid}', function (Request $request, Response $response, array $args) {
    $userid = $args['userid'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from filecabinet where Userid = '$userid'";
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

// Show Eligibility //
$app->get('/showeligibility/{filecabinetid}', function (Request $request, Response $response, array $args) {
    $filecabinetid = $args['filecabinetid'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select filecabinet.Userid,user.firstname,user.lastname,filecabinet.Userstatus
    from user,filecabinet
    where user.Userid = filecabinet.Userid
    and Filecabinetid = '$filecabinetid'";
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

// My Status//
$app->post('/mystatus', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);
    $filecabinetid = $bodyArry['Filecabinetid'];
    $userid = $bodyArry['Userid'];

    $conn = $GLOBALS['dbconn'];
    $sql = "select * from filecabinet where Userid = '$userid' and Filecabinetid = '$filecabinetid'";
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


// Get FilecabinetName //
$app->get('/getfilecabinetname/{filecabinetid}', function (Request $request, Response $response, array $args) {
    $filecabinetid = $args['filecabinetid'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select Name from filecabinet where Filecabinetid = '$filecabinetid'";
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