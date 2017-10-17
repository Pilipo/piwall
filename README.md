# piwall

Install, setup, and control a piwall implementation. One installation package for both server and client. Instructions will be forthcoming, but for now this document is being used for tracking of base features.

## Requirements:

* PHP
* mySQL
* avahi
* 2 or more Raspberry Pi systems with at least one display

## Installation:

## Use:

### **Server**

### **Client**

## **TODO:**

### SERVER:
* Installer
    * Check for: 
        * mySQL
            * set db user
            * set config
        * PHP v??
        * Avahi-daemon
        * jq
    * Init Keys *(should this happen during client check-in?)*
* Listen for clients
* Log client serial and access keys
* Test SSH controls via keys
* List clients and allow "identify" tiles

### CLIENT: 
* Installer
* Post-boot check-in
* Add authorized key for SSH during check-in
