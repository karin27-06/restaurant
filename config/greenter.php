<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

$see = new See();

$see->setCertificate(file_get_contents(storage_path('app/certificados/certificate.pem')));
$see->setClaveSOL(env('GREENTER_RUC'), env('GREENTER_USUARIO'), env('GREENTER_PASSWORD'));
$see->setService(SunatEndpoints::FE_BETA);  // Para modo prueba

return $see;
