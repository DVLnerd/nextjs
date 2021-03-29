<?php

$refer_percent = (float)$settings['ReferrsPercent'];
global $refer_percent;

/** Detection does it user someone referal. If it's true - return refer-chain ID.
 * @param $user_id
 * @return bool | integer
 */
function thisUserReferral($user_id){
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM referrs WHERE ReferrReferralUserID = :ReferrReferralUserID');
    $stmt->bindParam(':ReferrReferralUserID', $user_id);
    $stmt->execute();
    $row = $stmt->fetch();

    #return (isset($row['ReferrID'])) ? $row['ReferrID'] : false;
    return $row;
}

/** Add deduction info in DB.
 * @param $refer
 * @param $sum
 */
function newDeduction($refer, $sum){
    global $pdo;

    $stmt = $pdo->prepare('INSERT INTO `refer_deduction` (`refer`, `sum`, `date`) VALUES (:REFER, :PAYSUM, :PAYDATE)');
    $stmt->execute(array(':REFER' => $refer, ':PAYSUM' => $sum, ':PAYDATE' => time()));
}

/**
 * Detect user referal and pay if it's true.
 * @param $user_id
 * @param $pay_sum
 */
function deductionPercent($user_id, $pay_sum){
    $percent = countPercent($pay_sum);
    $refer = thisUserReferral($user_id);

    if(isset($refer['ReferrID']) && isset($refer['ReferrUserID'])){
        updateUserBalance($refer['ReferrUserID'], $percent);
        newDeduction($refer['ReferrID'], $percent);
    }
}

/**
 * Count percent of sum.
 * @param $pay_sum
 * @return float|int
 */
function countPercent($pay_sum){
    global $refer_percent;

    return (float)$pay_sum * $refer_percent / 100;
}