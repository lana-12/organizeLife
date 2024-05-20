<?php

namespace App\Service;


class CheckService {
   


    public static function checkAdminAccess($user)
    {
        $role = $user->getRoles();
        if (!in_array("ROLE_ADMIN", $role, true)) {
           
        return false;
    }
    
    return true;
    }

}
