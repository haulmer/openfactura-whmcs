<?php

/**
 * Register InvoicePaid hook.
 *
 * add_hook(string $hookPointName, int $priority, string|array|Closure $function)
 */

use WHMCS\Database\Capsule;
 
add_hook('InvoicePaid', 2, function($vars) {
    require_once dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . 'OpenFactura' . DIRECTORY_SEPARATOR . 'lib'. DIRECTORY_SEPARATOR . 'function_openfactura.php';
    $adminUsername = openfactura_getAdmin()->username;
    $invoiceid = $vars['invoiceid'];
    // Search the invoice
    $result_invoice = localAPI('getinvoice', array('invoiceid' => $invoiceid), $adminUsername);
    $client = localAPI('GetClientsDetails', array('clientid' => $result_invoice['userid']), $adminUsername);
    // Validate total and currency
    if($result_invoice['total'] > 10 and $client['currency_code'] == 'CLP' and $client['countrycode'] == 'CL'  and $result_invoice['tax'] > 0 ){
        $openfactura_config = Capsule::table('mod_openfactura_config')->get();

        if(isset($openfactura_config[0])){
            // Build json to send to openfactura
            $url = $openfactura_config[0]->url_send . 'v2/dte/document';
            $header = [
                'apikey: ' . $openfactura_config[0]->apikey,
                'Content-Type: application/json',
                'Idempotency-Key: '. $invoiceid
            ];
            $MntNeto = 0;
            $detalle = [];
            $descuento = [];
            $descuentoTotal = 0;
            $cont = 0; 
            foreach ($result_invoice['items']['item'] as $key => $value) {
                $cont++; 
                if($value['amount'] < 0){
                    // Create discount item with price 0
                    array_push($detalle ,[
                        "NroLinDet" => $cont,
                        "NmbItem" => 'Descuento',
                        "DscItem" => substr($value['description'], 0,1000),
                        "QtyItem" => 1,
                        "PrcItem" => 1,
                        "MontoItem" => 0
                    ]);
                    // Add discount
                    $MntNeto = $MntNeto + $value['amount'];
                    $descuentoTotal = $descuentoTotal + $value['amount']*-1 ;
                }
                else if($value['amount'] == 0){
                    // Create item of value 0
                    $nmb = substr(preg_replace("/\([^)]+\)/","",$value['description']) , 0,80);
                    if (empty($nmb)) {
                        $nmb = "Item " . $cont;
                    }
                    array_push($detalle ,[
                        "NroLinDet" => $cont,
                        "NmbItem" => $nmb,
                        "DscItem" => substr($value['description'], 0,1000),
                        "QtyItem" => 1,
                        "PrcItem" => 1,
                        "MontoItem" => 0
                    ]);
                }

                else{
                    $nmb = substr(preg_replace("/\([^)]+\)/","",$value['description']) , 0,80);
                    if (empty($nmb)) {
                        $nmb = "Item " . $cont;
                    }
                    array_push($detalle ,[
                        "NroLinDet" => $cont,
                        "NmbItem" => $nmb,
                        "DscItem" => substr($value['description'], 0,1000),
                        "QtyItem" => 1,
                        "PrcItem" => $value['amount'],
                        "MontoItem" => round($value['amount'])   
                    ]);
                    // Calculate net amount
                    $MntNeto = $value['amount'] + $MntNeto;
                }
            }
            // Openfactura performs the calculations that come from the variables tax, total, Net Mnt based on the net amount
            // To apply a credit, the value of the net amount must be changed to match the total amount to be paid
            $result_invoice['tax'] =  round( $MntNeto * ($result_invoice['taxrate']/100) );
            $result_invoice['total'] = ($result_invoice['tax'] + $MntNeto)-$result_invoice['credit'];

            // if credit is applied it must be added to discounts and create item
            if($result_invoice['credit'] > 0){
                $result_invoice['credit']=round($MntNeto-($result_invoice['total']/(($result_invoice['taxrate']/100)+1)));
                $descuentoTotal += $result_invoice['credit'];
                $MntNeto=round($result_invoice['total']/(($result_invoice['taxrate']/100)+1));
                $result_invoice['tax'] =  round( $MntNeto * ($result_invoice['taxrate']/100) );
                array_push($detalle ,[
                        "NroLinDet" => $cont+1,
                        "NmbItem" => 'Crédito',
                        "DscItem" => 'Se aplica crédito neto de: $'.$result_invoice['credit'],
                        "QtyItem" => 1,
                        "PrcItem" => 1,
                        "MontoItem" => 0
                    ]);
            }
                
            if($descuentoTotal != 0){
            // Create global discount
                array_push($descuento,[ 
                    "NroLinDR" => 1,
                    "TpoMov" => "D",
                    "TpoValor" => "$",
                    "ValorDR" => $descuentoTotal
                ]); 
            }

            $user = localAPI('GetClientsDetails', array('clientid' => $result_invoice['userid']), $adminUsername);            
            //acteco
            $acteco_json = json_decode(base64_decode($openfactura_config[0]->actividades_economicas,true),true);
            foreach ($acteco_json as $key => $value) {
                if($value['actividadEconomica'] == $openfactura_config[0]->actividad_economica_active ){
                    $acteco = $value['codigoActividadEconomica'];
                    break;
                }
            }
            //sucursal
            $sucursal_json = json_decode(base64_decode($openfactura_config[0]->sucursales,true),true);
            if(!empty($sucursal_json)){
                foreach ($sucursal_json as $key => $value) {
                    if($value['direccion'] == $openfactura_config[0]->sucursal_active ){
                        $sucursal = $value['cdgSIISucur'];
                        break;
                    }
                }
            }
            else{
                $sucursal = $openfactura_config[0]->cdgSIISucur;
            }

            // Final json
            $json = [
                "response" => ["SELF_SERVICE","PDF"],
                "dte" => [
                    "Encabezado" => [
                        "IdDoc" => [
                            "FchEmis" => date( 'Y-m-d' ),
                            "IndMntNeto" => 2
                        ],
                        "Emisor" => [
                            "RUTEmisor" => $openfactura_config[0]->rut,
                            "RznSocEmisor" => substr($openfactura_config[0]->razon_social,0,100),
                            "GiroEmisor" => substr($openfactura_config[0]->glosa_descriptiva,0,80),
                            "CdgSIISucur" => $sucursal,
                            "DirOrigen" => substr($openfactura_config[0]->direccion_origen,0,60),
                            "CmnaOrigen" => substr($openfactura_config[0]->comuna_origen,0,20),
                            "Acteco" => $acteco
                        ],
                        "Totales" => [ 
                            "MntNeto" => round($MntNeto),
                            "TasaIVA" => $result_invoice['taxrate'],
                            "IVA" => round($result_invoice['tax']),
                            "MntTotal" => round($result_invoice['total'])
                        ]
                    ],
                    "Detalle" =>  $detalle
                ],
               "customer" => [ 
                    "fullName" => $user['fullname'],
                    "email" => $user['email']
                ],
                "customizePage" => [ 
                    "externalReference" => [ 
                        "hyperlinkText" => $openfactura_config[0]->name_doc_base.' Nº'.$invoiceid ,
                        "hyperlinkURL" => $openfactura_config[0]->url_doc_base.'/viewinvoice.php?id='.$invoiceid
                    ]
                ],
                "selfService" => [
                    "issueBoleta" => (boolean)$openfactura_config[0]->generate_boleta,
                    "allowFactura" => (boolean)$openfactura_config[0]->allow_factura,
                    "documentReference" => [ 
                        [
                            "type" => 801,
                            "ID" => $result_invoice['invoiceid'],
                            "date" => $result_invoice['date']
                        ]
                    ]
                ],
                "service" => "SS",
                "custom" => [
                    "origin" => "WHMCS"
                ] 
            ];

            if($openfactura_config[0]->link_logo != null){
               $json['customizePage']['urlLogo'] = $openfactura_config[0]->link_logo;
            }
            if(!empty($descuento)){
                // Add global discount 
                $json['dte']['DscRcgGlobal'] = $descuento; 
            }

            for ($i=0; $i < 5; $i++) { 
                $curl = curl_init();
                curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
                curl_setopt( $curl, CURLOPT_URL, $url );
                curl_setopt( $curl, CURLOPT_POST, true );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode ($json));
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $curl, CURLOPT_FORBID_REUSE, true );
                $response = curl_exec( $curl );
                $info = curl_getinfo( $curl );
                if ( in_array( $info[ 'http_code' ], [ 200, 201 ] ) ){
                    $i = 10;
                    try{
                        $response2 = json_decode($response,true);
                        if( isset($response2['SELF_SERVICE']['url']) ){
                            // Generate URL    
                            $insertRegistry = Capsule::table('mod_openfactura_registry')->insert(
                                        [
                                            'apikey' => $openfactura_config[0]->apikey,
                                            'invoice_id' => $invoiceid ,
                                            'url' => $response2['SELF_SERVICE']['url'],
                                            'status' => 'done',
                                            'amount' => $result_invoice['total'],
                                            'name' => $user['fullname'],
                                            'email' => $user['email'],
                                            'date' => date( 'Y-m-d' ),
                                            'user_id' => $user['id'],
                                            'json_send' => base64_encode(json_encode($json))
                                        ]
                            );
                            $command = 'UpdateInvoice';
                            $postData = array(
                                'invoiceid' => $invoiceid,
                                'notes' => 'Obtén tu documento tributario en '.$response2['SELF_SERVICE']['url']                          
                            );
                            $adminUsername = $adminUsername;
                            $results = localAPI($command, $postData, $adminUsername);

                            logModuleCall('openfactura', 'Hook Sent to OF invoice: '.$invoiceid, json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), $response , $response, $response);
                        }
                        else{
                            $insertRegistry = Capsule::table('mod_openfactura_registry')->insert(
                                [
                                    'apikey' => $openfactura_config[0]->apikey,
                                    'invoice_id' => $invoiceid ,
                                    'url' => '',
                                    'status' => 'error',
                                    'amount' => $result_invoice['total'],
                                    'name' => $user['fullname'],
                                    'email' => $user['email'],
                                    'date' => date( 'Y-m-d' ),
                                    'user_id' => $user['id'],
                                    'json_send' => base64_encode(json_encode($json))
                                ]
                            );                  
                            logModuleCall('openfactura', 'Error in Hook Sent to OF invoice: '.$invoiceid, json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), $response , $response, $response);
                        }            
                    } catch (\Exception $e) {
                        logModuleCall('openfactura', 'Error in Hook Sent to OF invoice: '.$invoiceid, json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), $response , $response, $response);
                    }
                }
                usleep(2000000);
            }
            if( $i < 9 ){
                try{
                    $insertRegistry = Capsule::table('mod_openfactura_registry')->insert(
                        [
                            'apikey' => $openfactura_config[0]->apikey,
                            'invoice_id' => $invoiceid ,
                            'status' => 'error',
                            'amount' => $result_invoice['total'],
                            'name' => $user['fullname'],
                            'email' => $user['email'],
                            'date' => date( 'Y-m-d' ),
                            'user_id' => $user['id'],
                            'json_send' => base64_encode(json_encode($json))
                        ]
                    );
                } catch (\Exception $e) {
                    logModuleCall('openfactura', 'Error in save registry: '.$invoiceid, json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), $response , $e, $response);
                }
                logModuleCall('openfactura', 'Hook Error in Sent to OF', json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), $response , $response, $response);
            } 
            curl_close( $curl );
        }   
    }
    else{
        logModuleCall('openfactura', ' ERROR  invoice (total < 0 or currency != CLP or country != CL or IVA < 0)', 'invoice ID: '.$invoiceid, '' , '', '');
    }
});
