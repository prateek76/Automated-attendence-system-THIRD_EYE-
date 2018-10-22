<?php

require_once 'HTTP/Request2.php';

if(isset($_POST['submit'])) {
    $Batchname = $_POST['Batchname'];
    $request = new Http_Request2('https://westcentralus.api.cognitive.microsoft.com/face/v1.0/largepersongroups/'.$Batchname);
    $url = $request->getUrl();

    $headers = array(
        // Request headers
        'Content-Type' => 'application/json',
        'Ocp-Apim-Subscription-Key' => 'a6f0b0ef9dda422e9703659b8b230726',
    );

    $request->setHeader($headers);

    $parameters = array(
        // Request parameters
    );

    $url->setQueryVariables($parameters);

    $request->setMethod(HTTP_Request2::METHOD_PUT);

    // Request body
    $request->setBody(json_encode(array("name"=>"test")));//test data or using empty array just chill

    try
    {
        $response = $request->send();
        echo $response->getBody();
        header('Location: index.html');
    }
    catch (HttpException $ex)
    {
        echo $ex;
    }
}

?>


<!DOCTYPE html>
<html>
<head>
<style>
body{background: #2C3E50;
    background: -webkit-linear-gradient(to left, #4CA1AF, #2C3E50); 
    background: linear-gradient(to left, #4CA1AF, #2C3E50);
}
.form
     {
    width:340px;
	height:270px;
	background:#e6e6e6; 
	border-radius:8px;
	box-shadow:0 0 40px -10px #000;
	margin:calc(50vh - 220px) auto;
	padding:20px 30px;
	max-width:calc(100vw - 40px);
	box-sizing:border-box;
	font-family:'Montserrat',sans-serif;
	position:relative
	}
h2
{
  margin:10px 0;
  padding-bottom:10px;
  width:180px;
  color:#78788c;
  border-bottom:3px solid #78788c
  }
input
{
 width:100%;
 padding:10px;
 box-sizing:border-box;
 background:none;
 outline:none;
 resize:none;
 border:0;
 font-family:'Montserrat',sans-serif;transition:all .3s;
 border-bottom:2px solid #bebed2
 }
input:focus{border-bottom:2px solid #78788c}
p:before{content:attr(type);
 display:block;margin:28px 0 0;
 font-size:14px;color:#5a5a5a}
 button{float:right;padding:8px 12px;margin:8px 0 0;
 font-family:'Montserrat',sans-serif;
 border:2px solid #78788c;
 background:0;
 color:#5a5a6e;
 cursor:pointer;
 transition:all .3s
 }
button:hover{background:#78788c;color:#fff}
div{content:'Hi';
 position:absolute;
 bottom:-15px;right:-20px;background:#50505a;
 color:#fff;
 width:320px;
 padding:16px 4px 16px 0;
 border-radius:6px;
 font-size:13px;
 box-shadow:10px 10px 40px -14px #000
 }
span{margin:0 5px 0 15px}
</style>
</head>
<body>
<form autocomplete="off" class="form" action="creategroupLarge.php" method="POST">
  <h2>ADD NEW BATCH</h2>
  <p type="Batch Name:"><input placeholder="Write your batchname here.." name="Batchname"/></p>
  <button type="submit" name="submit">Submit</button>
</form>
</body>
</html> 