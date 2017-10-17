#!/bin/bash

serial=000000012345678
server_hostname="rpi2b.local"

function jsonValue() {
    KEY=$1
    num=$2
    awk -F"[,:}]" '{for(i=1;i<=NF;i++){if($i~/'$KEY'\042/){print $(i+1)}}}' | tr -d '"' | sed -n ${num}p
}

key=`curl -s -d "serial=$serial" -X POST http://$server_hostname:8023/check | jsonValue pub_key 1`
echo $key >> /home/pi/.ssh/authorized_keys
