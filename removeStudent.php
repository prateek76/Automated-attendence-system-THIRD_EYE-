<?php

require_once 'HTTP/Request2.php';

include 'init/init.php';

$roll = $_POST['roll'];
$batch = $_POST['batch'];
$p_id = -1;
$sql = "SELECT username, enroll, persistedId, batch FROM students";
$result = $conn->query($sql);
$data_from_base = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if($roll == $row['enroll'] && $batch == $row['batch']) {
            $p_id = $row['persistedId'];
            echo $p_id;
        }
        //$unique_key = $roll.''.$batch;
        //$data_from_base[$unique_key] = $row['persistedId'];
        //echo $data_from_base[$row['persistedId']];
    }
} else {
    echo "0 results";//alert instead
}



//database se match karao

$unique_key = $roll.''.$batch;

$request = new Http_Request2('https://westcentralus.api.cognitive.microsoft.com/face/v1.0/largepersongroups/'.$batch.'/persons/'.$p_id);
$url = $request->getUrl();

$headers = array(
    // Request headers
    'Ocp-Apim-Subscription-Key' => 'a6f0b0ef9dda422e9703659b8b230726',
);

$request->setHeader($headers);

$parameters = array(
    // Request parameters
);

$url->setQueryVariables($parameters);

$request->setMethod(HTTP_Request2::METHOD_DELETE);

// Request body
$request->setBody("{body}");

try
{
    $response = $request->send();
    echo $response->getBody();
}
catch (HttpException $ex)
{
    echo $ex;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
</head>
<body>
<form action="" method="POST">
    <input type="text" name="roll"/>
    <input type="text" name="batch"/>
    <button type="submit">del</button>
</form>
    
</body>
</html>