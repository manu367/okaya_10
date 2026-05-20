<?php
class BatterySerialUploader{
    private $conn;
    private $response=[];
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }
    public function checkModel():bool
    {
        return true;
    }
    public function insertData():bool
    {
        return false;
    }
    private function setresponse($data,$isAction)
    {
        array_push($this->response,$data,$isAction);
    }
    public function getResponse():json{
        return json_encode($this->response);
    }
}


class XMLMenuParser
{
    private $role;
    private SimpleXMLElement $xml;
    public function __construct(string $role, string $xmlFile)
    {
        $this->role = $role;
        $this->xml  = simplexml_load_file($xmlFile);

        if (!$this->xml) {
            throw new Exception("XML file load nahi hui");
        }
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function setRole(string $role): void
    {
        $this->role = $role;
    }
    private function getMenuByRole(): array
    {
        if (!isset($this->xml->{$this->role})) {
            return [];
        }

        $menuList = $this->xml->{$this->role}->{'menu-list'};
        $result   = [];

        foreach ($menuList->{'main-menu'} as $mainMenu) {

            $mainName = (string)$mainMenu['name'];
            $result[$mainName] = [];

            foreach ($mainMenu->{'sub-menu'} as $subMenu) {
                $result[$mainName][] = [
                    'title' => trim((string)$subMenu),
                    'href'  => (string)$subMenu['href'],
                    'icon'  => (string)$subMenu['data-icon']
                ];
            }
        }

        return $result;
    }
    public function getAllAdminMenu(): array
    {
        $this->setRole('admin');
        return $this->getMenuByRole();
    }
    public function getAllASPMenu(): array
    {
        $this->setRole('asp');
        return $this->getMenuByRole();
    }
    public function getAllWarehouseMenu(): array
    {
        $this->setRole('warehouse');
        return $this->getMenuByRole();
    }
    public function getAllCallCenterMenu(): array
    {
        $this->setRole('call-center');
        return $this->getMenuByRole();
    }
}
?>