<?php

namespace WHMCS\Module\Addon\OpenFactura\Admin;

use WHMCS\Database\Capsule;

/**
 *  Admin Area Controller
 */
class Controller
{
    /**
     * Index action.
     *
     * @param array $vars Module configuration parameters
     * @param object $smarty Object smarty template
     *
     * @return string
     */
    public function index($vars, $params)
    {
        global $CONFIG;
        // Get common module parameters
        $smarty = $params[0];
        $urlParams = $params[1];
        $modulelink = $vars['modulelink']; 
        $apikey = $vars['apikey'];
        $demo = $vars['demo'];
        $update = !empty($urlParams['update']) && boolval($urlParams['update']);
        // Check Apikey
        if ($demo) {
            try {
                $openfactura_config = Capsule::table('mod_openfactura_config')->where('apikey', '=', '928e15a2d14d4a6292345f04960f4bd3')->get();
                if (!isset($openfactura_config[0])) {
                    // Delete Apikey 
                    Capsule::table('mod_openfactura_config')->delete();
                    // Insert demo data
                    $insertConfig = Capsule::table('mod_openfactura_config')->insert(
                        [
                            'apikey' => '928e15a2d14d4a6292345f04960f4bd3',
                            'generate_boleta' => true,
                            'is_demo' => true,
                            'allow_factura' => false,
                            'link_logo' => 'https://pbs.twimg.com/profile_images/1052252093761933312/Np8k7ijc_400x400.jpg',
                            'show_logo' => true,
                            'rut' => '76795561-8',
                            'razon_social' => 'HAULMER SPA',
                            'glosa_descriptiva' => 'VENTA AL POR MENOR EN EMPRESAS DE VENTA A DISTANCIA VÍA INTERNET; COMERCIO ELEC',
                            'sucursales' => 'W10=',
                            'sucursal_active' => 'ARTURO PRAT 527   CURICO',
                            'actividad_economica_active' => 'VENTA AL POR MENOR POR CORREO, POR INTERNET Y VIA TELEFONICA',
                            'actividades_economicas' => 'ewogICAgICAgICAgICAiZ2lybyI6ICJWRU5UQSBBTCBQT1IgTUVOT1IgUE9SIENPUlJFTywgUE9SIElOVEVSTkVUIFkgVklBIFRFTEVGT05JQ0EiLAogICAgICAgICAgICAiYWN0aXZpZGFkRWNvbm9taWNhIjogIlZFTlRBIEFMIFBPUiBNRU5PUiBQT1IgQ09SUkVPLCBQT1IgSU5URVJORVQgWSBWSUEgVEVMRUZPTklDQSIsCiAgICAgICAgICAgICJjb2RpZ29BY3RpdmlkYWRFY29ub21pY2EiOiA0NzkxMDAsCiAgICAgICAgICAgICJhY3RpdmlkYWRQcmluY2lwYWwiOiB0cnVlCiAgICAgICAgfSwKICAgICAgICB7CiAgICAgICAgICAgICJnaXJvIjogIlZFTlRBIEFMIFBPUiBNRU5PUiBQT1IgQ09SUkVPLCBQT1IgSU5URVJORVQgWSBWSUEgVEVMRUZPTklDQSIsCiAgICAgICAgICAgICJhY3RpdmlkYWRFY29ub21pY2EiOiAiT1RST1MgU0VSVklDSU9TIERFIFRFTEVDT01VTklDQUNJT05FUyBBTEFNQlJJQ0FTIE4uQy5QLiIsCiAgICAgICAgICAgICJjb2RpZ29BY3RpdmlkYWRFY29ub21pY2EiOiA2MTEwOTAsCiAgICAgICAgICAgICJhY3RpdmlkYWRQcmluY2lwYWwiOiBmYWxzZQogICAgICAgIH0sCiAgICAgICAgewogICAgICAgICAgICAiZ2lybyI6ICJWRU5UQSBBTCBQT1IgTUVOT1IgUE9SIENPUlJFTywgUE9SIElOVEVSTkVUIFkgVklBIFRFTEVGT05JQ0EiLAogICAgICAgICAgICAiYWN0aXZpZGFkRWNvbm9taWNhIjogIkFDVElWSURBREVTIERFIENPTlNVTFRPUklBIERFIElORk9STUFUSUNBIFkgREUgR0VTVElPTiBERSBJTlNUQUxBQ0lPTkUiLAogICAgICAgICAgICAiY29kaWdvQWN0aXZpZGFkRWNvbm9taWNhIjogNjIwMjAwLAogICAgICAgICAgICAiYWN0aXZpZGFkUHJpbmNpcGFsIjogZmFsc2UKICAgICAgICB9LAogICAgICAgIHsKICAgICAgICAgICAgImdpcm8iOiAiVkVOVEEgQUwgUE9SIE1FTk9SIFBPUiBDT1JSRU8sIFBPUiBJTlRFUk5FVCBZIFZJQSBURUxFRk9OSUNBIiwKICAgICAgICAgICAgImFjdGl2aWRhZEVjb25vbWljYSI6ICJQUk9DRVNBTUlFTlRPIERFIERBVE9TLCBIT1NQRURBSkUgWSBBQ1RJVklEQURFUyBDT05FWEFTIiwKICAgICAgICAgICAgImNvZGlnb0FjdGl2aWRhZEVjb25vbWljYSI6IDYzMTEwMCwKICAgICAgICAgICAgImFjdGl2aWRhZFByaW5jaXBhbCI6IGZhbHNlCiAgICAgICAgfSwKICAgICAgICB7CiAgICAgICAgICAgICJnaXJvIjogIlZFTlRBIEFMIFBPUiBNRU5PUiBQT1IgQ09SUkVPLCBQT1IgSU5URVJORVQgWSBWSUEgVEVMRUZPTklDQSIsCiAgICAgICAgICAgICJhY3RpdmlkYWRFY29ub21pY2EiOiAiQURNSU5JU1RSQUNJT04gREUgVEFSSkVUQVMgREUgQ1JFRElUTyIsCiAgICAgICAgICAgICJjb2RpZ29BY3RpdmlkYWRFY29ub21pY2EiOiA2NjE5MDIsCiAgICAgICAgICAgICJhY3RpdmlkYWRQcmluY2lwYWwiOiBmYWxzZQogICAgICAgIH0sCiAgICAgICAgewogICAgICAgICAgICAiZ2lybyI6ICJWRU5UQSBBTCBQT1IgTUVOT1IgUE9SIENPUlJFTywgUE9SIElOVEVSTkVUIFkgVklBIFRFTEVGT05JQ0EiLAogICAgICAgICAgICAiYWN0aXZpZGFkRWNvbm9taWNhIjogIkVNUFJFU0FTIERFIEFTRVNPUklBIFkgQ09OU1VMVE9SSUEgRU4gSU5WRVJTSU9OIEZJTkFOQ0lFUkE7IFNPQ0lFREFERVMiLAogICAgICAgICAgICAiY29kaWdvQWN0aXZpZGFkRWNvbm9taWNhIjogNjYxOTAzLAogICAgICAgICAgICAiYWN0aXZpZGFkUHJpbmNpcGFsIjogZmFsc2UKICAgICAgICB9LAogICAgICAgIHsKICAgICAgICAgICAgImdpcm8iOiAiVkVOVEEgQUwgUE9SIE1FTk9SIFBPUiBDT1JSRU8sIFBPUiBJTlRFUk5FVCBZIFZJQSBURUxFRk9OSUNBIiwKICAgICAgICAgICAgImFjdGl2aWRhZEVjb25vbWljYSI6ICJBQ1RJVklEQURFUyBERSBDQUxMLUNFTlRFUiIsCiAgICAgICAgICAgICJjb2RpZ29BY3RpdmlkYWRFY29ub21pY2EiOiA4MjIwMDAsCiAgICAgICAgICAgICJhY3RpdmlkYWRQcmluY2lwYWwiOiBmYWxzZQogICAgICAgIH0=',
                            'direccion_origen' => 'ARTURO PRAT 527',
                            'comuna_origen' => 'Curicó',
                            'url_doc_base' => $CONFIG['SystemURL'],
                            'name_doc_base' => 'ORDEN DE COMPRA',
                            'json_info_contribuyente' => 'ewogICAgInJ1dCI6ICI3Njc5NTU2MS04IiwKICAgICJyYXpvblNvY2lhbCI6ICJIQVVMTUVSIFNQQSIsCiAgICAiZW1haWwiOiBudWxsLAogICAgInRlbGVmb25vIjogIjAgMCIsCiAgICAiZGlyZWNjaW9uIjogIkFSVFVSTyBQUkFUIDUyNyAgIENVUklDTyIsCiAgICAiY2RnU0lJU3VjdXIiOiAiODEzMDMzNDciLAogICAgImdsb3NhRGVzY3JpcHRpdmEiOiAiUFJPRFVDVE9TIFkgU0VSVklDSU9TIFJFTEFDSU9OQURPUyBDT04gSU5URVJORVQsIFNPRlRXQVJFLCBESVNQT1NJVElWTyIsCiAgICAiZGlyZWNjaW9uUmVnaW9uYWwiOiAiQ1VSSUPDkyIsCiAgICAiY29tdW5hIjogIkN1cmljw7MiLAogICAgInJlc29sdWNpb24iOiB7CiAgICAgICAgImZlY2hhIjogIjIwMTgtMDMtMjYiLAogICAgICAgICJudW1lcm8iOiAiMCIKICAgIH0sCiAgICAibm9tYnJlRmFudGFzaWEiOiAiSGF1bG1lciIsCiAgICAid2ViIjogIiIsCiAgICAic3VjdXJzYWxlcyI6IFtdLAogICAgImFjdGl2aWRhZGVzIjogWwogICAgICAgIHsKICAgICAgICAgICAgImdpcm8iOiAiVkVOVEEgQUwgUE9SIE1FTk9SIFBPUiBDT1JSRU8sIFBPUiBJTlRFUk5FVCBZIFZJQSBURUxFRk9OSUNBIiwKICAgICAgICAgICAgImFjdGl2aWRhZEVjb25vbWljYSI6ICJWRU5UQSBBTCBQT1IgTUVOT1IgUE9SIENPUlJFTywgUE9SIElOVEVSTkVUIFkgVklBIFRFTEVGT05JQ0EiLAogICAgICAgICAgICAiY29kaWdvQWN0aXZpZGFkRWNvbm9taWNhIjogNDc5MTAwLAogICAgICAgICAgICAiYWN0aXZpZGFkUHJpbmNpcGFsIjogdHJ1ZQogICAgICAgIH0sCiAgICAgICAgewogICAgICAgICAgICAiZ2lybyI6ICJWRU5UQSBBTCBQT1IgTUVOT1IgUE9SIENPUlJFTywgUE9SIElOVEVSTkVUIFkgVklBIFRFTEVGT05JQ0EiLAogICAgICAgICAgICAiYWN0aXZpZGFkRWNvbm9taWNhIjogIk9UUk9TIFNFUlZJQ0lPUyBERSBURUxFQ09NVU5JQ0FDSU9ORVMgQUxBTUJSSUNBUyBOLkMuUC4iLAogICAgICAgICAgICAiY29kaWdvQWN0aXZpZGFkRWNvbm9taWNhIjogNjExMDkwLAogICAgICAgICAgICAiYWN0aXZpZGFkUHJpbmNpcGFsIjogZmFsc2UKICAgICAgICB9LAogICAgICAgIHsKICAgICAgICAgICAgImdpcm8iOiAiVkVOVEEgQUwgUE9SIE1FTk9SIFBPUiBDT1JSRU8sIFBPUiBJTlRFUk5FVCBZIFZJQSBURUxFRk9OSUNBIiwKICAgICAgICAgICAgImFjdGl2aWRhZEVjb25vbWljYSI6ICJBQ1RJVklEQURFUyBERSBDT05TVUxUT1JJQSBERSBJTkZPUk1BVElDQSBZIERFIEdFU1RJT04gREUgSU5TVEFMQUNJT05FIiwKICAgICAgICAgICAgImNvZGlnb0FjdGl2aWRhZEVjb25vbWljYSI6IDYyMDIwMCwKICAgICAgICAgICAgImFjdGl2aWRhZFByaW5jaXBhbCI6IGZhbHNlCiAgICAgICAgfSwKICAgICAgICB7CiAgICAgICAgICAgICJnaXJvIjogIlZFTlRBIEFMIFBPUiBNRU5PUiBQT1IgQ09SUkVPLCBQT1IgSU5URVJORVQgWSBWSUEgVEVMRUZPTklDQSIsCiAgICAgICAgICAgICJhY3RpdmlkYWRFY29ub21pY2EiOiAiUFJPQ0VTQU1JRU5UTyBERSBEQVRPUywgSE9TUEVEQUpFIFkgQUNUSVZJREFERVMgQ09ORVhBUyIsCiAgICAgICAgICAgICJjb2RpZ29BY3RpdmlkYWRFY29ub21pY2EiOiA2MzExMDAsCiAgICAgICAgICAgICJhY3RpdmlkYWRQcmluY2lwYWwiOiBmYWxzZQogICAgICAgIH0sCiAgICAgICAgewogICAgICAgICAgICAiZ2lybyI6ICJWRU5UQSBBTCBQT1IgTUVOT1IgUE9SIENPUlJFTywgUE9SIElOVEVSTkVUIFkgVklBIFRFTEVGT05JQ0EiLAogICAgICAgICAgICAiYWN0aXZpZGFkRWNvbm9taWNhIjogIkFETUlOSVNUUkFDSU9OIERFIFRBUkpFVEFTIERFIENSRURJVE8iLAogICAgICAgICAgICAiY29kaWdvQWN0aXZpZGFkRWNvbm9taWNhIjogNjYxOTAyLAogICAgICAgICAgICAiYWN0aXZpZGFkUHJpbmNpcGFsIjogZmFsc2UKICAgICAgICB9LAogICAgICAgIHsKICAgICAgICAgICAgImdpcm8iOiAiVkVOVEEgQUwgUE9SIE1FTk9SIFBPUiBDT1JSRU8sIFBPUiBJTlRFUk5FVCBZIFZJQSBURUxFRk9OSUNBIiwKICAgICAgICAgICAgImFjdGl2aWRhZEVjb25vbWljYSI6ICJFTVBSRVNBUyBERSBBU0VTT1JJQSBZIENPTlNVTFRPUklBIEVOIElOVkVSU0lPTiBGSU5BTkNJRVJBOyBTT0NJRURBREVTIiwKICAgICAgICAgICAgImNvZGlnb0FjdGl2aWRhZEVjb25vbWljYSI6IDY2MTkwMywKICAgICAgICAgICAgImFjdGl2aWRhZFByaW5jaXBhbCI6IGZhbHNlCiAgICAgICAgfSwKICAgICAgICB7CiAgICAgICAgICAgICJnaXJvIjogIlZFTlRBIEFMIFBPUiBNRU5PUiBQT1IgQ09SUkVPLCBQT1IgSU5URVJORVQgWSBWSUEgVEVMRUZPTklDQSIsCiAgICAgICAgICAgICJhY3RpdmlkYWRFY29ub21pY2EiOiAiQUNUSVZJREFERVMgREUgQ0FMTC1DRU5URVIiLAogICAgICAgICAgICAiY29kaWdvQWN0aXZpZGFkRWNvbm9taWNhIjogODIyMDAwLAogICAgICAgICAgICAiYWN0aXZpZGFkUHJpbmNpcGFsIjogZmFsc2UKICAgICAgICB9CiAgICBdCn0=',
                            'url_send' => 'https://dev-api.haulmer.com/',
                            'cdgSIISucur' => '81303347'
                        ]
                    );
                }
            } catch (\Exception $e) {
                logModuleCall('OpenFactura', 'Error in save apikey', $e->getMessage(), '', '', '');
            }
        } else {
            // Verify the API key entered
            try {
                $openfactura_config = Capsule::table('mod_openfactura_config')->where('apikey', '=', $apikey)->get();
                if (!isset($openfactura_config[0])) {
                    // Delete Apikey 
                    Capsule::table('mod_openfactura_config')->delete();
                    // Insert data of apikey
                    $updatedConfig = Capsule::table('mod_openfactura_config')->insert(
                        [
                            'apikey' => $vars['apikey'],
                            'generate_boleta' => false,
                            'is_demo' => false,
                            'allow_factura' => false,
                            'link_logo' => 'URL_LOGO',
                            'show_logo' => false,
                            'url_send' => 'https://api.haulmer.com/',
                            'name_doc_base' => 'ORDEN DE COMPRA',
                            'url_doc_base' => $CONFIG['SystemURL']
                        ]
                    );
                }
            } catch (\Exception $e) {
                logModuleCall('OpenFactura', 'Error in save apikey', $e->getMessage(), '', '', '');
            }
        }

        if ($update) {
            // search data in Openfactura
            $header = [
                'apikey: ' . $openfactura_config[0]->apikey,
                'Content-Type: application/json',
            ];

            $url = $openfactura_config[0]->url_send . "v2/dte/organization";
            
            for ($i = 0; $i < 5; $i++) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                curl_setopt($curl, CURLOPT_URL, $url);

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
                $response = curl_exec($curl);
                $info = curl_getinfo($curl);
                if (in_array($info['http_code'], [200, 201])) {
                    $i = 10;
                    try {
                        // save data
                        if (isset($openfactura_config[0])) {
                            $response_aux = base64_encode($response);
                            $response_object = json_decode($response);
                            $response = json_decode($response, true);

                            
                            $current_info_contribuyente = json_decode(base64_decode(Capsule::table('mod_openfactura_config')
                                ->where('apikey', $openfactura_config[0]->apikey)->get()[0]
                                ->json_info_contribuyente, true));
                            $updated_info_contribuyente = $response_object;

                            $diff_branch = sizeof($updated_info_contribuyente->sucursales) !==
                                sizeof($current_info_contribuyente->sucursales);
                           
                            $diff_acteco = $current_info_contribuyente->actividades[0]->actividadEconomica !==
                                $updated_info_contribuyente->actividades[0]->actividadEconomica;

                            $diff_glosa = $current_info_contribuyente->glosaDescriptiva !==
                                $updated_info_contribuyente->glosaDescriptiva;

                            $diff_info = $diff_branch || $diff_acteco || $diff_glosa;

                            if (!empty($current_info_contribuyente) && !$diff_info) {
                                logModuleCall('OpenFactura', 'No changes after update', 'apikey: ' . $openfactura_config[0]->apikey . ' URL: ' . $url, $response, '');
                                return '<div id="response-api">201</div>';
                            } else {0
                                $active_branche = $response['direccion'];
                                if (!empty($urlParams['active_branch'])) {
                                    $active_branche = $urlParams['active_branch'];
                                }

                                $updatedConfig = Capsule::table('mod_openfactura_config')
                                    ->where('apikey', $openfactura_config[0]->apikey)
                                    ->update(
                                        [
                                            'rut' => $response['rut'],
                                            'razon_social' => $response['razonSocial'],
                                            'glosa_descriptiva' => $response['glosaDescriptiva'],
                                            'sucursales' => base64_encode(json_encode($response['sucursales'])),
                                            'actividades_economicas' => base64_encode(json_encode($response['actividades'])),
                                            'actividad_economica_active' => isset($response['actividades'][0]['actividadEconomica']) ? $response['actividades'][0]['actividadEconomica'] : null,
                                            'sucursal_active' => $active_branche,
                                            'direccion_origen' => $response['direccion'],
                                            'comuna_origen' => $response['comuna'],
                                            'json_info_contribuyente' => $response_aux,
                                            'cdgSIISucur' => $response['cdgSIISucur']
                                        ]
                                    );
                                logModuleCall('OpenFactura', 'Update info ', 'apikey: ' . $openfactura_config[0]->apikey . ' URL: ' . $url, $response, '');
                                return '<div id="response-api">200</div>';
                            }
                        } else {
                            logModuleCall('OpenFactura', 'Error in Update info ', 'apikey: ' . $openfactura_config[0]->apikey . ' URL: ' . $url, $response, '');
                            return '<div id="response-api">400</div>';
                        }
                    } catch (\Exception $e) {
                        logModuleCall('OpenFactura', 'Error in update info', 'apikey: ' . $openfactura_config[0]->apikey . ' URL: ' . $url, $response, '');
                        return '<div id="response-api">400</div>';
                    }
                } else {
                    usleep(50000);
                    logModuleCall('OpenFactura', 'Error in update info', 'apikey: ' . $openfactura_config[0]->apikey . ' URL: ' . $url, $response, '');
                    return '<div id="response-api">400</div>';
                }
                curl_close($curl);
            }
        }

        $openfactura_config = Capsule::table('mod_openfactura_config')->get()[0];
        if (!empty($urlParams['data'])) {
            try {
                $dataPost = json_decode(base64_decode($urlParams['data']), true);
                Capsule::table('mod_openfactura_config')->where('apikey', '=', $openfactura_config->apikey)
                    ->update([
                        'generate_boleta' => $dataPost['automatic39'],
                        'allow_factura' => $dataPost['allow33'],
                        'link_logo' => $dataPost['urlLogo'],
                        'show_logo' => $dataPost['enableLogo'],
                        'sucursal_active' => $dataPost['branch'],
                    ]);
                logModuleCall('OpenFactura', 'Save new data successfully', 'apikey: ' . $openfactura_config->apikey, $dataPost, '');
                return '<div id="response-api">200</div>';
            } catch (\Exception $e) {
                logModuleCall('OpenFactura', 'Error in save new data', $e->getMessage(), '', '', '');
                return '<div id="response-api">400</div>';
            }
        } else {
            if (isset($openfactura_config->generate_boleta)) {
                $smarty->assign("valueAutomatic39", boolval($openfactura_config->generate_boleta));
            }
            if (isset($openfactura_config->allow_factura)) {
                $smarty->assign("valueAllow33", boolval($openfactura_config->allow_factura));
            }
            if (isset($openfactura_config->show_logo)) {
                $smarty->assign("valueEnableLogo", boolval($openfactura_config->show_logo));
            }
            if (isset($openfactura_config->link_logo)) {
                $smarty->assign("urlLogo", $openfactura_config->link_logo);
            }
            // Variable assign rut
            if (isset($openfactura_config->rut)) {
                $rutEmisor = explode('-', $openfactura_config->rut)[0];
                $dvEmisor = explode('-', $openfactura_config->rut)[1];
                $rutEmisorFull = number_format($rutEmisor, 0, "", ".") . '-' . $dvEmisor;
                $smarty->assign('rutEmisor', $rutEmisorFull);
            }
            // Variable assign rznSoc
            if (isset($openfactura_config->razon_social)) {
                $rznSocEmisor = $openfactura_config->razon_social;
                $smarty->assign('rznSoc', $rznSocEmisor);
            }
            // Variable assign glosa
            if (isset($openfactura_config->glosa_descriptiva)) {
                $glosaDesEmisor = $openfactura_config->glosa_descriptiva;
                $smarty->assign('glosa', $glosaDesEmisor);
            }
            // Variable assign sucursales
            if (isset($openfactura_config->json_info_contribuyente)) {
                $infoContribuyente = json_decode(base64_decode($openfactura_config->json_info_contribuyente));
                $sucursalArray = [(object) array(
                    'comuna' => '',
                    'comuna_nombre' => $infoContribuyente->comuna,
                    'cdgSIISucur' => $infoContribuyente->cdgSIISucur,
                    'sucursal' => 'Domicilio',
                    'direccion' => $infoContribuyente->direccion,
                    'ciudad' => $infoContribuyente->direccionRegional,
                    'actividad_nombre' => $infoContribuyente->actividadEconomica,
                    'actividad_economica' => $infoContribuyente->codigoActividadEconomica
                )];
                // Check if branches array have data
                if (isset($infoContribuyente->sucursales) and sizeof($infoContribuyente->sucursales) > 0) {
                    $sucursalesArray = $infoContribuyente->sucursales;
                    foreach ($sucursalesArray as $key => $sucursal) {
                        array_push($sucursalArray, $sucursal);
                    }
                }
            }
            $sucursales_name = array();
            $sucursales_id = array();
            foreach ($sucursalArray as $key => $sucursal) {
                array_push($sucursales_name, $sucursal->direccion . ', ' . $sucursal->comuna_nombre);
                array_push($sucursales_id, $sucursal->cdgSIISucur);
            }
            $smarty->assign('sucursales_value', $sucursales_name);
            $smarty->assign('sucursales_key', $sucursales_id);
            $smarty->assign('sucursales_active', $openfactura_config->sucursal_active);

            // Variable assign acteco
            if (isset($openfactura_config->actividad_economica_active)) {
                $actecto_name = $openfactura_config->actividad_economica_active;
                $smarty->assign('acteco', $actecto_name);
            }

            $smarty->assign('customVariable', $apikey);
            $smarty->assign('configTextField', $configPasswordField);
            $smarty->assign('modulelink', $modulelink);
            $smarty->assign('RootDirectory', $ROOTDIR);

            $smarty->caching = false;
            $smarty->compile_dir = $GLOBALS['templates_compiledir'];
            $smarty->display(ROOTDIR . '/modules/addons/OpenFactura/templates/index.tpl');
            return;
        }
    }

    /**
     * Registration action.
     *
     * @param array $vars Module configuration parameters
     * @param object $smarty Object smarty template
     *
     * @return string
     */
    public function registration($vars, $params)
    {
        require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'function_openfactura.php';
        $adminUsername = openfactura_getAdmin()->username;
        //Get url params
        $smarty = $params[0];
        $urlParams = $params[1];
        // Get common module parameters
        $modulelink = $vars['modulelink'];
        $apikey = $vars['apikey'];
        $demo = $vars['demo'];

        if ($demo) {
            $apikey = '928e15a2d14d4a6292345f04960f4bd3';
        }
        // Parse limit and offset from queryParams
        $offset = NULL;
        $limit = NULL;

        if (!empty($urlParams['page'])) {
            $pageParam = explode(",",$urlParams['page']);
            $offset = explode("=", $pageParam[1])[1];
            $limit = explode("=", $pageParam[2])[1];
        }
        // Prevent to pass NULL parameter to SQL
        if (empty($limit)) {
                $limitSQL = 0;
                $offsetSQL = 10;
        } else {
                $limitSQL = $limit;
                $offsetSQL = $offset;
        }
        if (!empty($urlParams['orderID'])) {
            $openfactura_registry = Capsule::table('mod_openfactura_registry')->where('apikey', $apikey)->where('invoice_id','LIKE','%'.$urlParams['orderID'].'%')->offset($offsetSQL)->limit($limitSQL)->orderBy('date', 'desc')->get();
        }else{
            $openfactura_registry = Capsule::table('mod_openfactura_registry')->where('apikey', $apikey)->offset($offsetSQL)->limit($limitSQL)->orderBy('date', 'desc')->get();
        }
        $registry = [];
        foreach ($openfactura_registry as $key => $value) {
            array_push($registry, [
                "number" => $value->invoice_id,
                "name" => $value->name,
                "email" => $value->email,
                "date" => date('d-m-Y', strtotime($value->date)),
                "amount" => "$ " . number_format(intval($value->amount), 2),
                "status" => $value->url,
                "user_id" => $value->user_id,
                "user_name" => [$value->user_id, $value->name],
                "status_invoice"=>$value->status_invoice
            ]);
        }

        $openfactura_config = Capsule::table('mod_openfactura_config')->get();

        if (!empty($urlParams['page'])) {

            return '<div id="response-api">' . json_encode($registry) . '</div>';

        } elseif (!empty($urlParams['resendURL']) && boolval($urlParams['resendURL'])) {

            $registry = Capsule::table('mod_openfactura_registry')->where('apikey', $apikey)
                ->where('invoice_id', $urlParams['resendURL'])
                ->get();
            $openfactura_config = Capsule::table('mod_openfactura_config')->get();
            if (isset($registry[0]) and isset($openfactura_config[0]->rut)) {
                $url = $openfactura_config[0]->url_send . 'v2/dte/document';
                $header = [
                    'apikey: ' . $apikey,
                    'Content-Type: application/json',
                    'Idempotency-Key: ' . $registry[0]->invoice_id
                ];
                // NOTE: Behavior to implement re-send URL Api Call
                $json = json_decode(base64_decode($registry[0]->json_send), true);

                //acteco
                $acteco_json = json_decode(base64_decode($openfactura_config[0]->actividades_economicas, true), true);
                foreach ($acteco_json as $key => $value) {
                    if ($value['actividadEconomica'] == $openfactura_config[0]->actividad_economica_active) {
                        $acteco = $value['codigoActividadEconomica'];
                        break;
                    }
                }
                //sucursal
                $sucursal_json = json_decode(base64_decode($openfactura_config[0]->sucursales, true), true);
                if (!empty($sucursal_json)) {
                    foreach ($sucursal_json as $key => $value) {
                        if ($value['direccion'] == $openfactura_config[0]->sucursal_active) {
                            $sucursal = $value['cdgSIISucur'];
                            break;
                        }
                    }
                } else {
                    $sucursal = $openfactura_config[0]->cdgSIISucur;
                }
                $json['dte']['Encabezado']["Emisor"] = [
                    "RUTEmisor" => $openfactura_config[0]->rut,
                    "RznSocEmisor" => substr($openfactura_config[0]->razon_social, 0, 100),
                    "GiroEmisor" => substr($openfactura_config[0]->glosa_descriptiva, 0, 80),
                    "CdgSIISucur" => $sucursal,
                    "DirOrigen" => substr($openfactura_config[0]->direccion_origen, 0, 60),
                    "CmnaOrigen" => substr($openfactura_config[0]->comuna_origen, 0, 20),
                    "Acteco" => $acteco
                ];
                $invoiceid = $urlParams['resendURL'];

                 $json["customizePage"] = [ 
                    "externalReference" => [ 
                        "hyperlinkText" => $openfactura_config[0]->name_doc_base.' Nº'.$invoiceid ,
                        "hyperlinkURL" => $openfactura_config[0]->url_doc_base.'/viewinvoice.php?id='.$invoiceid
                    ]
                ];

                if($openfactura_config[0]->link_logo != null){
                    $json['customizePage']['urlLogo'] = $openfactura_config[0]->link_logo;
                }
                $json["selfService"]["issueBoleta" ] = (boolean)$openfactura_config[0]->generate_boleta;
                $json["selfService"]["allowFactura" ] = (boolean)$openfactura_config[0]->allow_factura;
                
                logModuleCall('OpenFactura', 'json', 'apikey: ' . $apikey . ' URL: ' . $url, json_encode($json), '');

                for ($i = 0; $i < 5; $i++) {
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($json));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
                    $response = curl_exec($curl);
                    $info = curl_getinfo($curl);
                    if (in_array($info['http_code'], [200, 201])) {
                        $i = 10;
                        $response2 = json_decode($response, true);
                        try {
                            // Re-send url api implementation
                            $updatedRegistry = Capsule::table('mod_openfactura_registry')
                                ->where('apikey', $apikey)
                                ->where('invoice_id', $invoiceid)
                                ->update(
                                    [
                                        'url' => $response2['SELF_SERVICE']['url'],
                                        'status' => 'done'
                                    ]
                                );
                            $command = 'UpdateInvoice';
                            $postData = array(
                                'invoiceid' => $invoiceid,
                                'notes' => 'Obtén tu documento tributario en ' . $response2['SELF_SERVICE']['url']
                            );
                            $results = localAPI($command, $postData, $adminUsername);
                            logModuleCall('openfactura', 'Re-Sent to OF invoice: ' . $invoiceid, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), $response, $response, $response);
                            return '<div id="response-api">200</div>';
                        } catch (\Exception $e) {

                            logModuleCall('OpenFactura', 'Error in re-send URL SS2', 'apikey: ' . $apikey . ' URL: ' . $url, $response, '');

                            return '<div id="response-api">400</div>';
                        }
                    } else {
                        usleep(50000);
                        logModuleCall('OpenFactura', 'Error in re-send URL SS', 'apikey: ' . $apikey . ' URL: ' . $url, $response, '');
                        return '<div id="response-api">400</div>';
                    }
                    curl_close($curl);
                }
            } else {
                logModuleCall('OpenFactura', 'Error in re-send URL SS', 'apikey: ' . $apikey, '', '');
                return '<div id="response-api">400</div>';
            }
        } elseif (!empty($urlParams['resendEmail']) && boolval($urlParams['resendEmail'])) {

            $registry = Capsule::table('mod_openfactura_registry')->where('apikey', $apikey)
                ->where('invoice_id', $urlParams['resendEmail'])
                ->get();
            if (isset($registry[0])) {
                $header = [
                    'apikey: ' . $apikey
                ];
                $token = explode("/", $registry[0]->url);

                $url = $openfactura_config[0]->url_send . 'v2/dte/selfservice/email/' . end($token);
                
                for ($i = 0; $i < 5; $i++) {
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
                    $response = curl_exec($curl);
                    $info = curl_getinfo($curl);
                    if (in_array($info['http_code'], [200, 201])) {
                        $i = 10;
                        logModuleCall('OpenFactura', 're-send URL mail', 'apikey: ' . $apikey . ' URL: ' . $url . ' token:' . end($token), $response, '');
                        return '<div id="response-api">200</div>';
                    } else {
                        usleep(50000);
                        logModuleCall('OpenFactura', 'Error in re-send URL mail', 'apikey: ' . $apikey . ' URL: ' . $url . ' token:' . end($token), $response, '');
                        return '<div id="response-api">400</div>';
                    }
                    curl_close($curl);
                }
            }
        } elseif (!empty($urlParams['updateState']) && boolval($urlParams['updateState'])) {
            $registry = Capsule::table('mod_openfactura_registry')->where('apikey', $apikey)
                ->where('invoice_id', $urlParams['updateState'])
                ->get();
            if (isset($registry[0])) {
                $token = explode("/", $registry[0]->url);
                
                $url = $openfactura_config[0]->url_send . 'v2/dte/selfservice/stateinvoice/' . end($token);
                $header = [
                    'apikey: ' . $apikey
                ];
                for ($i = 0; $i < 5; $i++) {
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
                    $response = curl_exec($curl);
                    $info = curl_getinfo($curl);
                    if (in_array($info['http_code'], [200, 201])) {
                        $i = 10;
                        // Update the mod_openfactura_registry table with the new invoice status
                        $status=json_decode($response,true);
                        logModuleCall('openfactura', 'Status pago: '.$status, json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), $response , $response, $response);
                        Capsule::table('mod_openfactura_registry')->where('invoice_id', $urlParams['updateState'])->update(['status_invoice' => $status['response']]);
                        return '<div id="response-api">200</div>';
                    } else {
                        usleep(50000);
                        return '<div id="response-api">400</div>';
                    }
                    curl_close($curl);
                }
            }
        } else {
            $datas = array();
            foreach ($registry as $key => $value) {
                $data = json_decode($value, true);
                array_push($datas, $data);
            }
            $smarty->assign('registerDatas', $datas);
            $smarty->assign('customVariable', $configTextField);
            $smarty->assign('configTextField', $configPasswordField);
            $smarty->assign('modulelink', $modulelink);
            $smarty->assign('RootDirectory', $ROOTDIR);
            $smarty->assign('enableEmision', false);


            $smarty->caching = false;
            $smarty->compile_dir = $GLOBALS['templates_compiledir'];
            $smarty->display(ROOTDIR . '/modules/addons/OpenFactura/templates/registration.tpl');
            return;
        }
    }

    /**
     * Registration action.
     *
     * @param array $vars Module configuration parameters
     * @param object $smarty Object smarty template
     *
     * @return string
     */
    public function search($vars, $smarty)
    {
        // Get module configuration parameters
        $modulelink = $vars['modulelink']; 
        $configTextField = $vars['apikey'];
        $configPasswordField = $vars['demo'];

        $smarty->assign('customVariable', $configTextField);
        $smarty->assign('configTextField', $configPasswordField);
        $smarty->assign('modulelink', $modulelink);
        $smarty->assign('RootDirectory', $ROOTDIR);

        $smarty->caching = false;
        $smarty->compile_dir = $GLOBALS['templates_compiledir'];
        $smarty->display(ROOTDIR . '/modules/addons/OpenFactura/templates/registration.tpl');
        return;
    }

    
    /**
     * Function that allows you to create an invoice manually.
     *
     * @param array $vars Module configuration parameters
     * @param object $smarty Object smarty template
     *
     * @return string
     */
    public function createManualInvoice($vars, $params)
    {
        $invoiceid = $params[1]['invoiceid'];

        if (!empty($invoiceid)) {

            // Query for the existence of the invoice in the whmcs table
            $invoicewhmcs = Capsule::table('tblinvoices')->where('id', '=', $invoiceid)->get();
            if (isset($invoicewhmcs[0])) {
                // Query for the existence of the invoice in Openfactura table
                $invoiceof = Capsule::table('mod_openfactura_registry')->where('invoice_id', '=', $invoiceid)->get();
                if (isset($invoiceof[0])) {
                    // Invoice exists on both systems
                    return '<div id="response-api">4000</div>';
                    logModuleCall('openfactura', 'El invoice que desea agregar ya existe: ' . $invoiceid);
                } else {
                    $client = localAPI('GetClientsDetails', array('clientid' =>  $invoicewhmcs[0]->userid), 'apiuser');
                    // The invoice is added to the Openfactura addon system
                    include '/usr/share/nginx/html/configuration.php';
                    require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'function_openfactura.php';
                    $adminUsername = openfactura_getAdmin()->username;
                    $invoiceid = $invoicewhmcs[0]->id;
                    // Search the invoice
                    $result_invoice = localAPI('getinvoice', array('invoiceid' => $invoiceid), $adminUsername);
                    $client = localAPI('GetClientsDetails', array('clientid' => $result_invoice['userid']), $adminUsername);

                    if($result_invoice['total'] > 10 and $client['currency_code'] == 'CLP' and $client['countrycode'] == 'CL'  and $result_invoice['tax'] > 0 and $result_invoice['status']=='Paid'){
                        $openfactura_config = Capsule::table('mod_openfactura_config')->get();
                        // If Apikey configuration exists
                        if(isset($openfactura_config[0])){

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
                            //Json to Openfactura
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
                                    array_push($detalle ,[
                                        "NroLinDet" => $cont,
                                        "NmbItem" => substr(preg_replace("/\([^)]+\)/","",$value['description']) , 0,80),
                                        "DscItem" => substr($value['description'], 0,1000),
                                        "QtyItem" => 1,
                                        "PrcItem" => 1,
                                        "MontoItem" => 0
                                    ]);
                                }
                
                                else{
                                    array_push($detalle ,[
                                        "NroLinDet" => $cont,
                                        "NmbItem" => substr(preg_replace("/\([^)]+\)/","",$value['description']), 0,80),
                                        "DscItem" => substr($value['description'], 0,1000),
                                        "QtyItem" => 1,
                                        "PrcItem" => $value['amount'],
                                        "MontoItem" => round($value['amount'])   
                                    ]);
                                    // Calculate net amount
                                    $MntNeto = $value['amount'] + $MntNeto;
                                }
                            }
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
                
                            //json to send
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
                                "service" => "SS" 
                            ];

                            if($openfactura_config[0]->link_logo != null){
                                $json['customizePage']['urlLogo'] = $openfactura_config[0]->link_logo;
                            }
                            if(!empty($descuento)){
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
                                // sleep 500ms 
                                usleep(500000);
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
                    else {
                        if($result_invoice['status']!='Paid'){
                            return '<div id="response-api">4006</div>';
                            logModuleCall('openfactura', 'El invoice que desea agregar no está pagada: ' . $invoiceid, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        }
                        if($result_invoice['total'] <= 10){
                            return '<div id="response-api">4001</div>';
                            logModuleCall('openfactura', 'El invoice que desea agregar ya existe: ' . $invoiceid, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        } 
                        if($client['currency_code'] != 'CLP'){
                            return '<div id="response-api">4002</div>';
                            logModuleCall('openfactura', 'El invoice que desea agregar no está en pesos chilenos: ' . $invoiceid, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        }
                        if($client['countrycode'] != 'CL'){
                            return '<div id="response-api">4003</div>';                            
                            logModuleCall('openfactura', 'El invoice que desea comprar debe pertenecer a Chile: ' . $invoiceid, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        } 
                        if($result_invoice['tax'] <= 0){
                            return '<div id="response-api">4004</div>';
                            logModuleCall('openfactura', 'El invoice que desea agregar debe tener un monto mayor a 0: ' . $invoiceid, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        }
                    }
                }
            } else {
                return '<div id="response-api">4005</div>';
                logModuleCall('openfactura', 'El invoice que desea agregar no se encuentra en WHMCS: ' . $invoiceid, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        }
        return '<div id="response-api">200</div>';
        logModuleCall('openfactura', 'Invoice agregada correctamente:' . $invoiceid, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }


    /**
     * Function that allows creating a csv file
     *
     * @param array $vars,$params 
     * @return file csv
     */
    public function exportCSV($vars, $params)
    {
        $start = $params[1]['start'];
        $end = $params[1]['end'];

        // Get module configuration parameters
        $apikey = $vars['apikey'];
        $demo = $vars['demo'];

        if ($demo) {
            $apikey = '928e15a2d14d4a6292345f04960f4bd3';
        }
        $openfactura_registry = Capsule::table('mod_openfactura_registry')
            ->where('apikey', $apikey)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();
        $registry = [];
        array_push($registry, [
            "number" => "ID",
            "name" => "Nombre",
            "email" => "Email",
            "date" => "Fecha",
            "amount" => "Monto",
            "stade" => "Estado",
            "status" => "Link de enlace",
        ]);
        foreach ($openfactura_registry as $key => $value) {
            array_push($registry, [
                "number" => $value->invoice_id,
                "name" => $value->name,
                "email" => $value->email,
                "date" => date('d-m-Y', strtotime($value->date)),
                "amount" => "$ " . number_format(intval($value->amount), 2),
                "stade" => $value->status_invoice,
                "status" => $value->url,
            ]);
        }

        $filename = "data_invoice_" . date("Y-m-d") . ".csv";
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");

        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($registry as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        die();
    }
}
