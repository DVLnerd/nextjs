<?php
include('layer.php');
include('smm-layer.php');
$url = $_SERVER['SERVER_NAME'];

$file_name = $layer->file_name();
$url       = $layer->url();
$ip        = $layer->GetIP();
if (isset($_SESSION['auth'])) {
    $UserID = $_SESSION['auth'];
    $stmt   = $pdo->prepare('SELECT * FROM users WHERE UserID = :UserID');
    $stmt->execute(array(
        ':UserID' => $UserID
    ));
    if ($stmt->rowCount() == 1) {
        $UserName        = $layer->GetData('users', 'UserName', 'UserID', $UserID);
        $UserEmail       = $layer->GetData('users', 'UserEmail', 'UserID', $UserID);
        $UserGroup       = $layer->GetData('users', 'UserGroup', 'UserID', $UserID);
        $UserBalance     = $layer->GetData('users', 'UserBalance', 'UserID', $UserID);
        $UserBalance     = round($UserBalance, 2);
        $UserDate        = $layer->GetData('users', 'UserDate', 'UserID', $UserID);
        $UserIPAddress   = $layer->GetData('users', 'UserIPAddress', 'UserID', $UserID);
        $UserAPI         = $layer->GetData('users', 'UserAPI', 'UserID', $UserID);
        $UserFirstName   = $layer->GetData('users', 'UserFirstName', 'UserID', $UserID);
        $UserLastName    = $layer->GetData('users', 'UserLastName', 'UserID', $UserID);
        $UserSkypeID     = $layer->GetData('users', 'UserSkypeID', 'UserID', $UserID);
        $UserInstagramID = $layer->GetData('users', 'UserInstagramID', 'UserID', $UserID);
    } else {
        $layer->redirect('logout.php');
    }
}
class User
{
    function IsLogged()
    {
        global $layer;
        if (!isset($_SESSION['auth']) && !isset($_SESSION['lock-screen'])) {
            $layer->redirect('index.php');
        } else if (isset($_SESSION['lock-screen'])) {
            $layer->redirect('lock.php');
        } else {
            $this->IsBanned($_SESSION['auth']);
        }
    }
    function IsBanned($UserID)
    {
        global $pdo;
        global $layer;
        global $UserID;
        $stmt = $pdo->prepare('SELECT * FROM users_banned WHERE UserBannedID = :UserBannedID');
        $stmt->execute(array(
            ':UserBannedID' => $UserID
        ));
        if ($stmt->rowCount() == 1) {
            $ban_row = $stmt->fetch();
            if (time() > $ban_row['UserBannedExpireDate'] && $ban_row['UserBannedExpireDate'] != 0) {
                $stmt = $pdo->prepare('DELETE FROM users_banned WHERE UserBannedID = :UserBannedID');
                $stmt->execute(array(
                    ':UserBannedID' => $UserID
                ));
            } else {
                session_destroy();
                $layer->redirect('index.php');
            }
        }
    }
    function IsAdmin()
    {
        global $pdo;
        global $layer;
        global $UserID;
        $stmt = $pdo->prepare('SELECT UserGroup FROM users WHERE UserID = :UserID');
        $stmt->execute(array(
            ':UserID' => $UserID
        ));
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch();
            if ($row['UserGroup'] != 'administrator') {
                $layer->redirect('../index.php');
            }
        } else {
            session_destroy();
            $layer->redirect('../index.php');
        }
    }
}
$user = new User();
class Orders
{
    function CheckOrder($OrderID)
    {
        global $layer;
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE OrderID = :OrderID');
        $stmt->execute(array(
            ':OrderID' => $OrderID
        ));
        if ($stmt->rowCount() == 1) {
            $row               = $stmt->fetch();
            $ServiceOrderCheck = $layer->GetData('services', 'ServiceOrderAPI', 'ServiceID', $row['OrderServiceID']);
            $CompleteURL       = str_replace('[OrderID]', $row['OrderAPIID'], $ServiceOrderCheck);
            $OrderCheck        = $layer->SendCurl($CompleteURL);
            return $OrderCheck;
        } else {
            return false;
        }
    }
    function CheckOrderStatus($OrderID)
    {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE OrderID = :OrderID');
        $stmt->execute(array(
            ':OrderID' => $OrderID
        ));
        if ($stmt->rowCount() == 1) {
            $row    = $stmt->fetch();
            $status = $row['OrderStatus'];
            if (!empty($row['OrderAPIID']) && $row['OrderAPIID'] != 0) {
                $order = $this->CheckOrder($OrderID);
                $resp  = json_decode($order);
                if (isset($resp) && property_exists($resp, 'status')) {
                    $status = $resp->status;
                }
                if ($status == 'Partial' || $status == 'Canceled' || $status == 'Refunded') {
                    if ($status == 'Partial')
                        $status = 'Canceled';
                    $stmt = $pdo->prepare('UPDATE orders SET OrderStatus = "Canceled" WHERE OrderID = :OrderID');
                    $stmt->execute(array(
                        ':OrderID' => $row['OrderID']
                    ));
                    $stmt = $pdo->prepare('UPDATE users SET UserBalance = :UserBalance WHERE UserID = :UserID');
                    $stmt->execute(array(
                        ':UserBalance' => $UserBalance + $row['OrderCharge'],
                        ':UserID' => $UserID
                    ));
                } else {
                    $stmt = $pdo->prepare('UPDATE orders SET OrderStatus = :OrderStatus WHERE OrderID = :OrderID');
                    $stmt->execute(array(
                        ':OrderStatus' => $status,
                        ':OrderID' => $row['OrderID']
                    ));
                }
            }
            return $this->serializeStatus($status);
        } else {
            return 'Отменен';
        }
    }

    function serializeStatus($status)
    {
        switch ($status){
            case "Completed": {
                $label = "Завершен";
                break;
            }
            case "Processing":{
                $label = "В работе";
                break;
            }
            case "Pending":{
                $label = "В ожидании";
                break;
            }
            case "Canceled":{
                $label = "Отменен";
                break;
            }
            case "Refunded":{
                $label = "Возвращен";
                break;
            }
            case "Deleted":{
                $label = "Удален";
                break;
            }
            default: {
                $label = "Неизвестен";
            }
        }

        return $label;
    }


    function CheckOrderRemains($OrderID)
    {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE OrderID = :OrderID');
        $stmt->execute(array(
            ':OrderID' => $OrderID
        ));
        if ($stmt->rowCount() == 1) {
            $row     = $stmt->fetch();
            $remains = 0;
            if (!empty($row['OrderAPIID']) && $row['OrderAPIID'] != 0) {
                $order = $this->CheckOrder($OrderID);
                $resp  = json_decode($order);
                if (isset($resp) && property_exists($resp, 'remains')) {
                    $remains = $resp->remains;
                }
            }
            return $remains;
        } else {
            return 0;
        }
    }
    function CheckOrderStartCount($OrderID)
    {
        global $pdo;
        global $layer;
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE OrderID = :OrderID');
        $stmt->execute(array(
            ':OrderID' => $OrderID
        ));
        if ($stmt->rowCount() == 1) {
            $row             = $stmt->fetch();
            $start_count     = $row['OrderStartCount'];
            $ServiceOrderAPI = $layer->GetData('services', 'ServiceOrderAPI', 'ServiceID', $row['OrderServiceID']);
            if (empty($row['OrderStartCount']) && !empty($ServiceOrderAPI)) {
                $URL    = str_replace('[OrderID]', $row['OrderAPIID'], $ServiceOrderAPI);
                $return = $layer->SendCurl($URL);
                $resp   = json_decode($return);
                if (isset($resp) && property_exists($resp, 'start_count'))
                    $start_count = $resp->start_count;
                $stmt = $pdo->prepare('UPDATE orders SET OrderStartCount = :OrderStartCount WHERE OrderID = :OrderID');
                $stmt->execute(array(
                    ':OrderStartCount' => $start_count,
                    ':OrderID' => $OrderID
                ));
            }
            return $start_count;
        } else {
            return 0;
        }
    }
    public function DeclarePrice($ProductPrice, $ProductDefaultQuantity, $ProductQuantity)
    {
        $ProductValue = $ProductPrice / $ProductDefaultQuantity;
        return $ProductValue * $ProductQuantity;
    }
    function GetPrice($service_id, $quantity)
    {
        global $layer;
        global $pdo;
        global $UserID;
        global $UserGroup;
        $service_id = $layer->safe($service_id, 'none');
        $quantity   = $layer->safe($quantity, 'none');
        if (ctype_digit($service_id) && ctype_digit($quantity)) {
            $stmt = $pdo->prepare('SELECT * FROM services WHERE ServiceID = :ServiceID');
            $stmt->execute(array(
                ':ServiceID' => $service_id
            ));
            if ($stmt->rowCount() == 1) {
                $row   = $stmt->fetch();
                $query = $pdo->prepare('SELECT * FROM individual_prices WHERE IPUserID = :IPUserID AND IPServiceID = :IPServiceID');
                $query->execute(array(
                    ':IPUserID' => $UserID,
                    ':IPServiceID' => $service_id
                ));
                if ($query->rowCount() == 1) {
                    $query_row = $query->fetch();
                    $total     = $this->DeclarePrice($query_row['IPPrice'], $row['ServiceMinQuantity'], $quantity);
                } else if (!empty($row['ServiceResellerPrice']) && $UserGroup == 'reseller') {
                    $total = $this->DeclarePrice($row['ServiceResellerPrice'], $row['ServiceMinQuantity'], $quantity);
                } else {
                    $total = $this->DeclarePrice($row['ServicePrice'], $row['ServiceMinQuantity'], $quantity);
                }
                return $total;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    function GetQuantityPerLink($service_id, $link)
    {
        global $layer;
        global $pdo;
        $service_id   = $layer->safe($service_id, 'none');
        $link         = $layer->safe($link, 'none');
        $max_quantity = $layer->GetData('services', 'ServiceMaxQuantity', 'ServiceID', $service_id);
        $stmt         = $pdo->prepare('SELECT * FROM orders WHERE OrderServiceID = :OrderServiceID AND OrderLink = :OrderLink');
        $stmt->execute(array(
            ':OrderServiceID' => $service_id,
            ':OrderLink' => $link
        ));
        if ($stmt->rowCount() == 0) {
            return $max_quantity;
        } else {
            $total = 0;
            foreach ($stmt->fetchAll() as $order_row) {
                $total += $order_row['OrderQuantity'];
            }
            if ($total != $max_quantity) {
                return $max_quantity - $total;
            } else {
                return 0;
            }
        }
    }
}
$orders = new Orders();