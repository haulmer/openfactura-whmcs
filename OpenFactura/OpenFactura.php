<?php

/**
 * Module Openfactura.
 */

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\OpenFactura\Admin\AdminDispatcher;
use WHMCS\Module\Addon\OpenFactura\Client\ClientDispatcher;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Config Module Openfactura.
 * 
 * @return array
 */
function OpenFactura_config()
{
    return [
        // Display name for your module
        'name' => 'OpenFactura',
        // Description displayed within the admin interface
        'description' => 'Emita y envíe boletas y facturas electrónicas de forma automatizada a sus clientes, sin intervenir el carro de compras. Disponible en Chile.',
        // Module author name
        'author' => 'Haulmer, Inc.',
        // Default language
        'language' => 'english',
        // Version number
        'version' => '1.1',
        'fields' => [
            // a text field type allows for single line text input
            'apikey' => [
                'FriendlyName' => 'Apikey',
                'Type' => 'text',
                'Size' => '40',
                'Default' => 'Apikey',
                'Description' => '<a href="https://openfactura.cl" target="_blank">¿Donde obtengo mi Api key?</a>',
            ],
            // the yes no field type displays a single checkbox option demo
            'demo' => [
                'FriendlyName' => 'Ambiente de demostración',
                'Type' => 'yesno',
                'Description' => 'Integrar con empresa de demostración en ambiente de pruebas',
            ],

        ]
    ];
}

/**
 * Activate.
 *
 * Called upon activation of the module for the first time.
 *
 * @return array  success/failure message
 */
function OpenFactura_activate()
{
     // Create custom tables and schema required 
    try {
        if (!Capsule::schema()->hasTable('mod_openfactura_config')) {
            Capsule::schema()
                ->create(
                'mod_openfactura_config',
                function ($table) {
                    $table->increments('id');
                    $table->string('apikey')->unique();
                    $table->boolean('is_demo')->nullable();
                    $table->boolean('generate_boleta')->nullable();
                    $table->boolean('allow_factura')->nullable();
                    $table->string('link_logo')->nullable();
                    $table->boolean('show_logo')->nullable();
                    $table->string('rut')->nullable();
                    $table->string('razon_social')->nullable();
                    $table->string('glosa_descriptiva')->nullable();
                    $table->text('sucursales')->nullable();
                    $table->string('sucursal_active')->nullable();
                    $table->string('actividad_economica_active')->nullable();
                    $table->text('actividades_economicas')->nullable();
                    $table->string('direccion_origen')->nullable();
                    $table->string('comuna_origen')->nullable();
                    $table->text('json_info_contribuyente')->nullable();
                    $table->string('url_doc_base')->nullable();
                    $table->string('name_doc_base')->nullable();
                    $table->string('url_send')->nullable();
                    $table->string('cdgSIISucur')->nullable();
                }
            ); 
        }
        if (!Capsule::schema()->hasTable('mod_openfactura_registry')) {
            Capsule::schema()
                ->create(
                'mod_openfactura_registry',
                function ($table) {
                    $table->increments('id');
                    $table->string('apikey');
                    $table->integer('invoice_id')->unique();
                    $table->integer('amount');
                    $table->string('url')->unique()->nullable();
                    $table->string('status');
                    $table->string('name');
                    $table->string('email');
                    $table->date('date');
                    $table->string('user_id');
                    $table->text('json_send'); 
                }
            ); 
        }  
        return [
            // Supported values here include: success, error or info
            'status' => 'success',
            'description' => 'Activación Exitosa',
        ];
    } catch (\Exception $e) {
        return [
            // Supported values here include: success, error or info
            'status' => "error",
            'description' => 'Error en Activación: ' . $e->getMessage(),
        ];
    }
}

/**
 * Deactivate.
 *
 * Called upon deactivation of the module.
 *
 * @return array success/failure message
 */
function OpenFactura_deactivate()
{
      try {
        Capsule::schema()
            ->dropIfExists('mod_openfactura_config');

        return [
            // Supported values here include: success, error or info
            'status' => 'success',
            'description' => 'Desactivación Exitosa',
        ];
    } catch (\Exception $e) {
        return [
            // Supported values here include: success, error or info
            "status" => "error",
            "description" => "Error en Desactivación: {$e->getMessage()}",
        ];
    }
}

/**
 * Upgrade.
 *
 * Called the first time the module is accessed following an update.
 *
 * @return void
 */
function OpenFactura_upgrade($vars)
{
    $currentlyInstalledVersion = $vars['version'];

    /// Perform SQL schema changes required by the upgrade to version 1.1 of your module
    if ($currentlyInstalledVersion < 1.1) {
        $schema = Capsule::schema();
        // Check if the column exists
        if(!$schema->hasColumn('mod_openfactura_registry', 'status_invoice'));
        {
            $schema->table('mod_openfactura_registry', function($table) {
                $table->text('status_invoice');
             });
        }
    }
}

/**
 * Admin Area Output.
 *
 * @return string
 */
function OpenFactura_output($vars)
{
    // Get common module parameters
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $smarty = new Smarty();
    // Create dispatcher
    $dispatcher = new AdminDispatcher();
    $response = $dispatcher->dispatch($action, $vars, [$smarty, $_REQUEST]);
    echo $response;
}

/**
 * Admin Area Sidebar Output.
 *
 * Render output in the admin area sidebar.
 *
 * @param array $vars
 *
 * @return string
 */
function OpenFactura_sidebar($vars)
{

    $sidebar = '<p>Openfactura</p>';
    return $sidebar;
}

/**
 * Client Area Output.
 *
 * Called when the addon module is accessed via the client area.
 *
 * @return array
 */
function OpenFactura_clientarea($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    $dispatcher = new ClientDispatcher();
    return $dispatcher->dispatch($action, $vars);
}
