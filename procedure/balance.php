<?php
/**
 * This function for check exist payment to prevent re-enrollment.
 * @param $operation_id
 * @return bool
 */
function isPaymentExist($operation_id){
    global $pdo;
    $stmt = $pdo->prepare('SELECT DepositVerification FROM deposits WHERE DepositVerification = :DepositVerification');
    $stmt->bindParam(':DepositVerification', $operation_id);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}

/**
 * Check exist user on database
 * @param $user_id
 * @return bool
 */
function isUserExist($user_id){
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM users WHERE UserID = :UserID');
    $stmt->bindParam(':UserID', $user_id);
    $stmt->execute();

    return $stmt->rowCount();
}

/**
 * Function for update user balance
 * @param $user
 * @param $sum
 */
function updateUserBalance($user, $sum){
    global $pdo;
    $stmt = $pdo->prepare('UPDATE users SET UserBalance = UserBalance + :UserBalance WHERE UserID = :UserID');
    $stmt->execute(array(':UserBalance' => $sum, ':UserID' => $user));
}

/**
 * Add new deposit to date base
 * @param $user
 * @param $amount
 * @param $operation_id
 */
function newDeposit($user, $amount, $operation_id){
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO deposits (DepositUserID, DepositDate, DepositAmount, DepositVerification, DepositType) VALUES (:DepositUserID, :DepositDate, :DepositAmount, :DepositVerification, :DepositType)');
    $stmt->execute(array(':DepositUserID' => $user, ':DepositDate' => time(), ':DepositAmount' => $amount, ':DepositVerification' => $operation_id, ':DepositType' => 'FreeKassa'));
}
