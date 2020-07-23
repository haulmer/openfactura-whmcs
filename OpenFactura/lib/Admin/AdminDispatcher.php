<?php

namespace WHMCS\Module\Addon\OpenFactura\Admin;

/**
 * Admin Area Dispatch Handler
 */
class AdminDispatcher {

    /**
     * Dispatch request.
     *
     * @param string $action
     * @param array $parameters
     *
     * @return string
     */
    public function dispatch($action, $parameters, $smarty)
    {
        if (!$action) {
            // Default to index if no action specified
            $action = 'index';
        }

        $controller = new Controller();

        // Verify requested action is valid and callable
        if (is_callable(array($controller, $action))) {
            return $controller->$action($parameters, $smarty);
        }

        return '<p>Invalid action requested. Please go back and try again.</p>';
    }
}
