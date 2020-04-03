<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Add //
$app->post('/add', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("insert into covid 
    (Name,Gender,Career,Province,Date,Time,Status) values (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssss",$bodyArry['Name'], $bodyArry['Gender'], 
    $bodyArry['Career'],$bodyArry['Province'],$bodyArry['Date'],$bodyArry['Time'],$bodyArry['Status']);

    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// Update //
$app->post('/update', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("update covid ".
    "set Status = ? where Id = ?");
    $stmt->bind_param("si",
    $bodyArry['Status'],$bodyArry['Id']);
  
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// Delete Filecabinet //
$app->get('/delete/{Id}', function (Request $request, Response $response, array $args) {
    $Id = $args['Id'];
    $conn = $GLOBALS['dbconn'];
   
        $stmt = $conn->prepare('delete from covid where Id = ?');
        $stmt->bind_param("i",$Id);
        $stmt->execute();

        $result = $stmt->affected_rows;
        $response->getBody()->write($result."");
    
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

