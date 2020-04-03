<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

function base64_to_file($base64_string, $output_file){
    $ifp = fopen($output_file, 'wb');
    // header,base64
    $data = explode(',',$base64_string);
    if (count($data) == 2) {
        fwrite($ifp, base64_decode($data[1]));
    }
    else {
        fwrite($ifp, base64_decode($data[0]));
    }
    fclose($ifp);
    return $output_file;
}

// Upload file //
$app->post('/uploadfile', function (Request $request, Response $response, array $args) {
    
    $json =$request->getBody();
    $jsonArr = json_decode($json, true);
    $conn = $GLOBALS['dbconn'];

    if (array_key_exists('Name',$jsonArr)) {
        try {
            //mkdir(__DIR__ . '/../files/'.$jsonArr['Docid'],0777);
            $makefolder = __DIR__ . '/../files/'.$jsonArr['Docid'];
            if(!is_dir($makefolder)){
                //Directory does not exist, so lets create it.
                mkdir($makefolder, 0755, true);
            }
           
            
            base64_to_file($jsonArr['base64'], 
                    __DIR__ . '/../files/'.$jsonArr['Docid']."/" . $jsonArr['Name']);
            
            $path = "http://localhost/webservice/files/".$jsonArr['Docid']."/".$jsonArr['Name'];
            //echo $path;
            $stmt = $conn->prepare("insert into file ".
            "(Docid,Name,Path)  values (?,?,?)");
            $stmt->bind_param("iss",
            $jsonArr['Docid'],$jsonArr['Name'], $path);
            $stmt->execute();

            $result = $stmt->affected_rows;
            $response->getBody()->write($result."");
            return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');
        } catch (Exception $error) {
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus(400);
        }
    }
    else{
        $response->getBody()->write(json_encode('No filename'));
        return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*')
        ->withStatus(400);
    }    
});
//

// Delete File //
$app->post('/deletefile', function (Request $request, Response $response, array $args) {
    $body = $request->getBody();
    $bodyArry = json_decode($body,true);

    $conn = $GLOBALS['dbconn'];
    unlink(__DIR__ . '/../files/'.$bodyArry['Docid']."/".$bodyArry['Name']);

    $stmt = $conn->prepare("Delete from file where Fileid = ?");
    $stmt->bind_param("i",$bodyArry['Fileid']);
    $stmt->execute();
    $result = $stmt->affected_rows;

    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');;
});
//

// Show File//
$app->get('/showfile/{docid}', function (Request $request, Response $response, array $args) {
    $docid = $args['docid'];
    $conn = $GLOBALS['dbconn'];
    $sql = "select * from file where Docid = '$docid'";
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