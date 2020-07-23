<?php

/**
 * openfactura getAdmin
 *
 * Function obtained by an administrator in the system.
 *
 * @return array
 */

use WHMCS\Database\Capsule;
function openfactura_getAdmin() {
  return Capsule::table('tbladmins')
  ->join('tbladminroles', 'tbladminroles.id', '=', 'tbladmins.roleid')
  ->where('tbladminroles.name', 'Full Administrator')->first();
}