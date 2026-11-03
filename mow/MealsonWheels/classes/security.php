<?php
class security
{
    private $db;

    public function __construct($db)
    {
        $this->setup($db);
    }

    private function setup($db)
    {
        $this->db = $db;
    }

    public function pageAccess($user, $page)
    {
        if ($page->isAdmin()) {
            if (!$user) {
                header("location: /login");
                exit();
            }
            if ($user->isAdmin()) {
                if ($user->getPermissions() >= $page->permissionLevel()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function sanitizeInput($string){
        $value = htmlspecialchars(trim($string));

        return $value;
    }

    public function cleanMoney($string){
        return str_replace(['$',','],'',htmlspecialchars(trim($string)));
    }
}
