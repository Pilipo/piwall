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

    if (empty($clients)) {
        $sql = "INSERT INTO clients (wall_id, serial_number, confirmed) VALUES (1, :serial, FALSE)";
        $qi = $this->db->prepare($sql);
        $publickey = file_get_contents(__DIR__ . "/../private/piwall_rsa.pub");

        $qi->bindParam(":serial", $data['serial']);
        $qi->execute();

        $data['key'] = $publickey;
        $data['id'] = $this->db->lastInsertId();
        return $this->response->withJson($data);
    } else {
        $publickey = file_get_contents(__DIR__ . "/../private/piwall_rsa.pub");
        $data['key'] = $publickey;
        return $this->response->withJson($data);
    }
});

$app->get('claim_key', function(Request $req, Response $res, array $args){
    $this->logger->info("Client is claiming a key.");
    $data = $req->getParsedBody();
    $serial = $data['serial'];
    $key = $data['key'];

    $res->getBody()->write("\nHere's your key: \n" . hash("sha256", $key_file));    
});

$app->get('/command/reboot/{serial}', function (Request $req, Response $res, array $args) {
    $serial = $args['serial'];
    $this->logger->info("Issuing a reboot command on " . $args["serial"]);
    $qs = $this->db->prepare("SELECT * FROM clients WHERE serial_number='$serial'");
    $qs->bindParam("serial", $args['serial']);
    $qs->execute();
    $client = $qs->fetchObject();
    if (empty($client)) {
        $this->logger->info("No serials found matching " . $args["serial"]);
        return;
    } else {
        // SSH send reboot command.
        //$data["reboot_success"] = ssh_command($client->hostname, "reboot");

        $ssh = new phpseclib\Net\SSH2("192.168.1.104");
        $key = new phpseclib\Crypt\RSA();
        $key->loadKey(__DIR__ . "/../private/piwall_rsa");
        $data["reboot_success"] = false;
        if ($ssh->login('pi', "raspberry")) {
            $this->logger->info("Logged in to " . $hostname);
            $ssh->write("sudo reboot\n");
            $output = $ssh->read('#[pP]assword[^:]*:|pi@panel-1.local:~\$#', NET_SSH2_READ_REGEX);
            if (preg_match('#[pP]assword[^:]*:#', $output)) {
                $ssh->write("raspberry\n");
                $data["reboot_success"] = $ssh->read('pi@panel-1.local:~$');
            }
        } else {
            $data["reboot_success"] = "SSH failed";
            $this->logger->info("SSH failed...");
        }
        return $this->response->withJson($data);
    }
});

function ssh_command($hostname, $command) {
    $ssh = new phpseclib\Net\SSH2($hostname);
    $key = new phpseclib\Crypt\RSA();
    $key->loadKey(__DIR__ . "/../private/piwall_rsa");
    if ($ssh->login('pi', $key)) {
        $this->logger->info("Logged in to " . $hostname);
        return $ssh->exec('whoami');
    } else {
        // $this->logger->info("Failed to connect to " . $hostname);
    }
}