# AOSTW

Acest proiect are scop didactic, fiind un exemplu de implementare si utilizare a 
serviciilor de tip SOAP si REST.

## SOAP
* `server-soap.php` expune serviciul aflat in `src/Services/CrudService.php`
* `client-soap.php` se conecteaza serviciul SOAP si face gestioneaza apelurile de functii catre serviciu printr-un UI minimal
* `server-soap.php?wsdl` expune schema WSDL

## REST
* `server-rest.php` expune serviciul aflat in `src/Services/CrudService.php`
* `client-rest.php`gestioneaza apelurile catre serviciu folosind REST, printr-un UI minimal

## Observatii
* Partea de UI (`ui/index.php`), Serviciul (`src/Services/CrudService.php`) raman neschimbate indiferent daca se foloseste SOAP sau REST.
* `client-soap.php`, `client-rest.php` sunt extrem de similari, functionalitatea comuna poate fi extrasa, diferenta constand doar in folosirea de clienti diferiti.


## Instalare
```shell script
composer install
cp config.json.example config.json 
```