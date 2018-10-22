<?php

error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once "vendor/autoload.php";
$mail = new PHPMailer;

if(isset($_POST['submit'])) {

    $urli = $_POST['url'];
    $batch = $_POST['batch'];
    $face_id = array();
    $ans_id = array();
    //give batch selection option (:
    include 'init/init.php';

    $sql = "SELECT username, enroll, persistedId, batch FROM students";
    $result = $conn->query($sql);
    $data_from_base = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row['batch'] == $batch) $data_from_base[$row['persistedId']] = $row['enroll'];
            //echo $data_from_base[$row['persistedId']];
        }
    } else {
        echo "0 results";//alert instead
    }

    require_once 'HTTP/Request2.php';
    /**detect list */

    $request = new Http_Request2('https://westcentralus.api.cognitive.microsoft.com/face/v1.0/detect');
    $url = $request->getUrl();

    $headers = array(
        // Request headers
        'Content-Type' => 'application/json',
        'Ocp-Apim-Subscription-Key' => 'a6f0b0ef9dda422e9703659b8b230726',
    );

    $request->setHeader($headers);

    $parameters = array(
        // Request parameters
        'returnFaceId' => 'true',
    );

    $url->setQueryVariables($parameters);

    $request->setMethod(HTTP_Request2::METHOD_POST);

    // Request body
    $flag1=0;
    $flagarr1 = array();
    $flag2=0;
    $flagarr2 = array();
    $flag3=0;
    $flagarr3 = array();
    $flag4=0;
    $flagarr4 = array();
    $flag5=0;
    $flagarr5 = array();
    $flag6=0;
    $flagarr6 = array();
    $flag7=0;
    $flagarr7 = array();
    $request->setBody(json_encode(array("url"=>$urli)));
    $cnt = 0;
    try
    {
        $response = $request->send();
        $face = $response->getBody();
        $faces = (json_decode($face,true));
        for($i=0;$i<sizeof($faces);$i++) {
            if($cnt < 9) array_push($face_id, $faces[$i]['faceId'] );
            $cnt++;
            //echo $face_id;
            if($cnt >= 9 && $cnt < 19) {
                //echo $cnt;
                $flag1 = 1;
                array_push($flagarr1, $faces[$i]['faceId']);
            } else if($cnt >= 19 && $cnt < 29) {
                $flag2 = 1;
                array_push($flagarr2, $faces[$i]['faceId']);
            }
        }
        //print_r($face_id);
    }
    catch (HttpException $ex)
    {
        echo $ex;
    }

    /*** */
    /** identifying azure */
    $request = new Http_Request2('https://westcentralus.api.cognitive.microsoft.com/face/v1.0/identify');
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

    $request->setMethod(HTTP_Request2::METHOD_POST);



    // Request body
    $request->setBody(json_encode(array("largePersonGroupId"=>$batch,
                                        "faceIds" => $face_id,
                                        "maxNumOfCandidatesReturned" => 1,
                                        "confidenceThreshold" => 0.4)));

    try
    {
        $response = $request->send();
        $varx = $response->getBody();
        //echo $varx;
        
        $findFace = (json_decode($varx,true));
        for($i=0;$i<sizeof($findFace);$i++) {
            if(!empty($findFace[$i]['candidates'])) {
                array_push($ans_id, $data_from_base[$findFace[$i]['candidates'][0]['personId']] );
                $cnt++;
                //echo $data_from_base[$findFace[$i]['candidates'][0]['personId']];
                //echo $findFace[$i]['candidates'][0]['personId'];
                //echo "\t";
            }
        }
    }
    catch (HttpException $ex)
    {
        echo $ex;
    }

    if($flag1 == 1) {
        $request->setBody(json_encode(array("largePersonGroupId"=>$batch,
        "faceIds" => $flagarr1,
        "maxNumOfCandidatesReturned" => 1,
        "confidenceThreshold" => 0.4)));

        try
        {
            $response = $request->send();
            $varx = $response->getBody();
            //echo $varx;
            $findFace = (json_decode($varx,true));
            for($i=0;$i<sizeof($findFace);$i++) {
                if(!empty($findFace[$i]['candidates'])) {
                    array_push($ans_id, $data_from_base[$findFace[$i]['candidates'][0]['personId']] );
                    $cnt++;
                    echo $data_from_base[$findFace[$i]['candidates'][0]['personId']];
                    //echo $findFace[$i]['candidates'][0]['personId'];
                    //echo "\t";
                }
            }
            }
            catch (HttpException $ex)
            {
            echo $ex;
            }
    }

    if($flag2 == 1) {
        $request->setBody(json_encode(array("largePersonGroupId"=>$batch,
        "faceIds" => $flagarr2,
        "maxNumOfCandidatesReturned" => 1,
        "confidenceThreshold" => 0.4)));

        try
        {
            $response = $request->send();
            $varx = $response->getBody();
            //echo $varx;
            $findFace = (json_decode($varx,true));
            for($i=0;$i<sizeof($findFace);$i++) {
                if(!empty($findFace[$i]['candidates'])) {
                    array_push($ans_id, $data_from_base[$findFace[$i]['candidates'][0]['personId']] );
                    $cnt++;
                    //echo $data_from_base[$findFace[$i]['candidates'][0]['personId']];
                    //echo $findFace[$i]['candidates'][0]['personId'];
                    //echo "\t";
                }
            }
            }
            catch (HttpException $ex)
            {
            echo $ex;
            }
    }

    $result = array_unique($ans_id);
    //print_r($result);

    echo "<table>";
    echo "<tr>";
    echo "<th>ENROLLMENT NO.</th>";
    echo "<th>Status</th>";
    echo "</tr>";

    $sendHit = "";
    $sendHit .= "<head><style>
    table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
    }

    td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
    }

    tr:nth-child(even) {
    background-color: #dddddd;
    }
    </style>
    </head>
    <table><tr><th>ENROLLMENT NO.</th><th>Status</th></tr>";

    for($i = 0;$i < sizeof($result); $i++) {
        if(trim($result[$i]) != "") {
            echo '<tr>';
            $sendHit .= '<tr>';
            echo '<td>'.$result[$i].'</td>';
            $sendHit .= '<td>'.$result[$i].'</td>';
            echo '<td>Present</td>';
            $sendHit .= '<td style="color: green">Present</td>';
            echo '</tr>';
            $sendHit .= '</tr>';
        }
    }
    echo "</table>";
    $sendHit .= "</table>";
        error_reporting(0);

    $mail->isSMTP();          
    $mail->SMTPOptions = array(
        'ssl' => array(
                'verify_peer'         => false,
                'verify_peer_name'    => false,
                'allow_self_signed'   => true
            )
        );
    $mail->SMTPDebug    = 0;  
    $mail->Host         = "smtp.gmail.com";
    $mail->SMTPAuth     = true;                          
    $mail->Username     = 'iec2017029@iiita.ac.in';                 
    $mail->Password     = '';                           
    $mail->SMTPSecure   = "tls";                           
    $mail->Port         = 587;                                   

    $mail->From         = 'iec2017029@iiita.ac.in';
    $mail->FromName     = 'Third-eye';
    $mail->Subject      = 'attendence from Third-eye';
    $mail->Body         = $sendHit;

    $mail->addAddress('iec2017029@iiita.ac.in');
    //$mail->addAttachment("uploads/".$fileNewName);
    $mail->isHTML(true);

    if(!$mail->send()) 
    {
        //echo '<script language="javascript"> alert("not sent!") </script>';
    } 
    else 
    {
        //echo '<script language="javascript"> alert("Message has been sent successfully") </script>';
    }
    //echo file_get_contents('send.html');
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
        width: 340px;
        height: 580px;
        background: #e6e6e6;
        border-radius: 8px;
        box-shadow: 0 0 40px -10px #000;
        margin: auto;
        margin-top: 10%;
        padding: 20px 30px;
        max-width: calc(100vw - 40px);
        box-sizing: border-box;
        font-family: 'Montserrat',sans-serif;
        position: relative;
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

table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}

tr:nth-child(odd) {
    background-color: aliceblue;
}

</style>
</head>
<body>
<form class="form" action="attend.php" method="POST">
  <h2>MAKE ATTENDENCE</h2>
  <p type="Batch Name:"><input placeholder="Write your name here.." name="batch"/></p>
  <p type="Url:"><input placeholder="Write your url here.." name="url"/></p>
  <p style="color: red">UPLOAD your image and get URL from this <a target="_blank" href="https://iec2017029.000webhostapp.com/Upload.php">link</a></p>
  <button type="submit" name="submit">Submit</button>
</form>
</body>
</html>