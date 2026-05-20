<?php
require_once "../common/core.php";

header('Content-Type: application/json');

$category = new CategoryControllers();

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        if (isset($_GET['id'])) {
            echo $category->getCategoryByID((int)$_GET['id']);
        }
        else if (isset($_GET['insert'])) {
            echo $category->insertCategory();
        }
        else {
            echo json_encode([
                "status" => "success",
                "data" => $category->getAllCategory()
            ]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "msg" => "Method Not Allowed"
        ]);
}

/* ================= CONTROLLER ================= */

class CategoryControllers
{
    private $con;

    public function __construct()
    {
        $this->con = DBConnection::getConnection();
    }
    public function getAllCategory(): array
    {
        $data = [];

        $sql = GET_ALL_CATEGORY . " ORDER BY category.sort_order ASC";
        $result = mysqli_query($this->con, $sql);

        if (!$result) {
            return [];
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
    public function getCategoryByID(int $id): string
    {
        if ($id <= 0) {
            return json_encode([
                "status" => "error",
                "msg" => "Invalid ID"
            ]);
        }

        $sql = GET_ALL_CATEGORY . " WHERE cat_id = ?";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id,$id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            return json_encode([
                "status" => "success",
                "data" => $row
            ]);
        }

        return json_encode([
            "status" => "error",
            "msg" => "No record found"
        ]);
    }

    public function insertCategory() {
        $stmt=mysqli_prepare($this->con, INSERT_CATEGORY);
        var_dump(INSERT_CATEGORY); exit();
    }
    public function updateCategory() {}
    public function deleteCategory() {}
    public function updateCatOrder($arr){
//        [[data="[{"cat_id":"4","cat_name":"IBPS SO IT","sort_order":"0"}]
    }
}
