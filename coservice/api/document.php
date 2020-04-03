<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Add Document //
$app->post('/adddocument', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);
    $date = $bodyArry['Date'];
    

    
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("insert into document 
    (Documentid,Filecabinetid,Name,Source,Sendto,Referto,Date) values (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssss",$bodyArry['Documentid'], $bodyArry['Filecabinetid'], 
    $bodyArry['Name'],$bodyArry['Source'],$bodyArry['Sendto'],$bodyArry['Referto'],$date);

    $stmt->execute();
    /*echo $bodyArry['Documentid'].' ';
    echo $bodyArry['Filecabinetid'].' ';
    echo $bodyArry['Name'].' ';
    echo $bodyArry['Source'].' ';
    echo $bodyArry['Sendto'].' ';
    echo $bodyArry['Referto'].' ';*/
    //echo $date.' ';
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// Edit Document //
$app->post('/editdocument', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("update document ".
    "set Documentid = ?,Name = ?,Source = ?,Date = ?,Sendto = ?,Referto = ? where id = ?");
    $stmt->bind_param("ssssssi",
    $bodyArry['Documentid'], $bodyArry['Name'],$bodyArry['Source'],
    $bodyArry['Date'],$bodyArry['Sendto'],$bodyArry['Referto'],$bodyArry['id']);
  
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

//Search Documentid//
$app->get('/searchdocumentid/{documentid}', function (Request $request, Response $response, array $args) {
    $documentid = $args['documentid'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from document where Documentid like '%$documentid%'";
    $result = $conn->query($sql);
    $data = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data,$row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});
//

//Search Name//
$app->get('/searchname/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from document where Name like '%$name%'";
    $result = $conn->query($sql);
    $data = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data,$row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});
//

//Search Source//
$app->get('/searchsource/{source}', function (Request $request, Response $response, array $args) {
    $source = $args['source'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from document where Source like '%$source%'";
    $result = $conn->query($sql);
    $data = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data,$row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});
//

//Search Date//
$app->get('/searchdate/{date}', function (Request $request, Response $response, array $args) {
    $date = $args['date'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from document where Date like '%$date%'";
    $result = $conn->query($sql);
    $data = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data,$row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});
//

//Search Sendto//
$app->get('/searchsendto/{sendto}', function (Request $request, Response $response, array $args) {
    $sendto = $args['sendto'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from document where Sendto like '%$sendto%'";
    $result = $conn->query($sql);
    $data = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data,$row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});
//

//Search Referto//
$app->get('/searchreferto/{referto}', function (Request $request, Response $response, array $args) {
    $referto = $args['referto'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from document where Referto like '%$referto%'";
    $result = $conn->query($sql);
    $data = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data,$row);
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
});
//

// Show Document//
$app->get('/showdocument/{filecabinetid}', function (Request $request, Response $response, array $args) {
    $filecabinetid = $args['filecabinetid'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select id,Documentid,Filecabinetid,Name,Source,Sendto,Referto,DATE_FORMAT(Date, '%d/%m/%y') AS Date from document where Filecabinetid = '$filecabinetid'";
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

// Get Document//
$app->get('/getdocument/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select id,Documentid,Filecabinetid,Name,Source,Sendto,Referto,DAY(Date) as Day,MONTH(Date) as Month,YEAR(Date) as Year from document where id = '$id'";
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

//Max id//
$app->get('/maxdocid', function (Request $request, Response $response, array $args) {
    $conn = $GLOBALS['dbconn'];
    $sql = "select MAX(id) as maxdocid from document";
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