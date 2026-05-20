<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://103.25.174.27:8002/sap/opu/odata/SAP/ZOK_MASTER_DATA_LIST_SRV/DistributorListSet?%24filter=StartDate%20eq%20datetime%272025-01-01T00%3A00%3A00%27%20and%20EndDate%20eq%20datetime%272025-07-17T00%3A00%3A00%27&%24format=json',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Cookie: sap-usercontext=sap-client=400'
  ),
	// 🔹 Ignore SSL certificate verification (for testing)
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
