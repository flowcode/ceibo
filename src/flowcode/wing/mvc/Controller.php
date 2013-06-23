<?php

namespace flowcode\wing\mvc;

/**
 * Description of Action
 *
 * @author juanma
 */
class Controller {

    public $isSecure = true;
    protected $module;
    protected $name;

    /**
     * Permissions that are allowed to access to this controller instance.
     * @var type 
     */
    protected $permissions = array();

    public function setIsSecure($isSecure) {
        $this->isSecure = $isSecure;
    }

    public function redirect($to_url) {
        header("Location: $to_url");
    }

    public function isSecure() {
        return $this->isSecure;
    }

    public function addPermission($permission) {
        $this->permissions[] = $permission;
    }

    public function canAccess($user) {
        $can = false;
        foreach ($user["roles"] as $userRole) {
            foreach ($userRole["permissions"] as $permission) {
                if (in_array($permission, $this->permissions)) {
                    $can = true;
                    break;
                }
            }
        }
        return $can;
    }

    /**
     * Retorna la representacion json del objecto recibido por parametro.
     * @param type $object
     * @return json json. 
     */
    public function toJson($object) {
        $array = $this->toArray($object);
        return str_replace('\\u0000', "", json_encode($array));
    }

    /**
     * Convierte un objeto a notacion json.
     * @param type $obj
     * @return type 
     */
    private function toArray($obj) {
        $arr = array();

        if (is_array($obj)) {
            foreach ($obj as $value) {
                $arr[] = $this->toArray($value);
            }
        }
        if (is_object($obj)) {
            $ardef = array();
            $arObj = (array) $obj;
            foreach ($arObj as $key => $value) {
                $attribute = str_replace(get_class($obj), "", $key);
                if (is_object($value) || is_array($value)) {
                    $value = $this->toArray($value);
                }
                $arr[$attribute] = $value;
            }
        }
        return $arr;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
    }

    public function getModule() {
        return $this->module;
    }

    public function setModule($module) {
        $this->module = $module;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

}

?>
