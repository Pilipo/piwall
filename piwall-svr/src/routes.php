<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->post('/check', function (Request $req, Response $res, array $args){
    $this->logger->info("Client checking in...");
    $data = $req->getParsedBody();
    $serial = $data['serial'];
    $qs = $this->db->prepare("SELECT * FROM clients WHERE serial_number='$serial'");
    $qs->bindParam("serial", $args['serial']);
    $qs->execute();
    $clients = $qs->fetchObject();

    //TESTING SSH
    
    $conn = ssh2_connect('panel-l.local', 22, array('hostkey'=>'ssh-rsa'));

    if (ssh2_auth_pubkey_file($conn, 'pi', openssl_decrypt("YGgTa+y38yCXIN+QQiQnJnquthsYf1Vr0an4CK55Fv55zmXRtwMlwqNf4HdeS0c4SbCmFUAXagt2MmYQUjmH4nIrgDY3hNVo3bEHAgHvPOjYQxIFGW+Je6JpIvgFDY0F1PofT0L1e1fjuMeOodwJQUeEfiRoAhcIhmaRwDgUlqAMul/Wu1HHFPmtPTHkQfuKvkas2vZIL+1vYsM4eWIPlXVH9nnPigLchPqBS93C6e21k/SHiIxzNgvwKdQv2dQn", 'aes-256-cbc', getenv("salt")), openssl_decrypt("e1eTsqIS/1kYzAFONzePQHIqZ0gU55HREahQfgzJNp47E/YWXpKPyx6cwrnoZyw95NaivwlLS3wKUvpgLd7KCowG+wmG3hlfyY+6eMCBKOCjIUv5fbxtV4V/WW3YHrEQmrH0npEg+N0mmRCbAH8VZJOh2Vov8Y+4KPCyMZUJhez3joHfo0BWndG/cKbZHD4KG1Lt9PJNToFVIvO5EdUoX+HD+q5pI+zKudHg67g+lwNfCyjhoFKvtAvuWRiygQv", 'aes-256-cbc', getenv("salt")))) {
        $data["test"] = "SUCCESS!!";
    } else {
        $data["test"] = "FAILURE!!";
    }
    

    if (empty($clients)) {
        $sql = "INSERT INTO clients (wall_id, serial_number, RSA_key, RSA_pub, confirmed) VALUES (1, :serial, :priv_key, :pub_key, FALSE)";
        $qi = $this->db->prepare($sql);

        $key_res = openssl_pkey_new(array( 
              'private_key_bits' => 1024, 
              'private_key_type' => OPENSSL_KEYTYPE_RSA));

        openssl_pkey_export($key_res, $priv_key);
        $pub_key = sshEncodePublicKey($key_res);
        $data["pub_key"] = $pub_key;

        $qi->bindParam(":serial", $data['serial']);
        $qi->bindParam(":priv_key", openssl_encrypt($priv_key, 'aes-256-cbc', getenv("salt")));
        $qi->bindParam(":pub_key", openssl_encrypt($pub_key, 'aes-256-cbc', getenv("salt")));
        $qi->execute();
        $data['id'] = $this->db->lastInsertId();
        return $this->response->withJson($data);
    } else {
        $res->getBody()->write("nice to see you again, " . $serial);
        $key_file = file_get_contents(__DIR__ . "/../private/piwall_rsa.pub");
        $res->getBody()->write("\nHere's your key: \n" . hash("sha256", $key_file));
    }

    // if doesn't exist, store RSA key and serial in db (mark entry unconfirmed, untested)
    //  then reply with RSA key for addition to authorized keys and wait for response.
});

$app->get('claim_key', function(Request $req, Response $res, array $args){
    $this->logger->info("Client is claiming a key.");
    $data = $req->getParsedBody();
    $serial = $data['serial'];
    $key = $data['key'];

    $res->getBody()->write("\nHere's your key: \n" . hash("sha256", $key_file));    
});



$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

function sshEncodePublicKey($privKey) {
    $keyInfo = openssl_pkey_get_details($privKey);
    $buffer  = pack("N", 7) . "ssh-rsa" .
    sshEncodeBuffer($keyInfo['rsa']['e']) . 
    sshEncodeBuffer($keyInfo['rsa']['n']);
    return "ssh-rsa " . base64_encode($buffer);
};

function sshEncodeBuffer($buffer) {
    $len = strlen($buffer);
    if (ord($buffer[0]) & 0x80) {
        $len++;
        $buffer = "\x00" . $buffer;
    }
    return pack("Na*", $len, $buffer);
};