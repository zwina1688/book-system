<?php

function Error($msg, $sql) {
    return json_encode(array("code" => 1, "msg" => $msg, "sql" => $sql, "error_info" => error_get_last()));
}


if (!session_id()) {
    session_start();
}error_reporting(E_ALL & ~E_NOTICE);function ConnDB() {
    try {
        $db = new PDO("mysql:host=localhost;dbname=api_supersite_to", "api_supersite_to", "swMfwRZHbDkFNe4W");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->query("SET NAMES utf8");
        return $db;
    } catch (PDOException $e) {
        echo "数据库连接失败：" . $e->getMessage();
        exit;
    }
}function Success($sql, $data, $count) {
    return json_encode(array("data" => $data, "code" => 0, "msg" => "Success", "count" => $count, "sql" => $sql));
}function Error($msg, $sql) {
    return json_encode(array("code" => 1, "msg" => $msg, "sql" => $sql));
}function GetAll($sql, $page, $limit) {
    $DbContext = ConnDB();
    $temp = ($page - 1) * $limit;
    $tempsql = "$sql LIMIT :temp, :limit";    $stmt = $DbContext->prepare($sql);
    $stmt->execute();
    $count = $stmt->rowCount();    $stmt = $DbContext->prepare($tempsql);
    $stmt->bindValue(':temp', $temp, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);    $arr = $DbContext->errorInfo();
    return Base($arr, $tempsql, $data, $count);
}function GetFull($sql) {
    $DbContext = ConnDB();
    $stmt = $DbContext->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);    $arr = $DbContext->errorInfo();
    return Base($arr, $sql, $data, 0);
}function Get($sql) {
    $DbContext = ConnDB();
    $stmt = $DbContext->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();    $arr = $DbContext->errorInfo();
    return Base($arr, $sql, $data, $count);
}function Update($sql) {
    $DbContext = ConnDB();
    $result = $DbContext->exec($sql); // exec() 返回受影响的行数
    $arr = $DbContext->errorInfo();
    return Base($arr, $sql, $result, null);
}function Create($sql) {
    $DbContext = ConnDB();
    $result = $DbContext->exec($sql); // exec() 返回受影响的行数
    $arr = $DbContext->errorInfo();
    return Base($arr, $sql, $result, null);
}function Base($arr, $sql, $data, $count) {
    if ($arr[0] == "00000") {
        return Success($sql, $data, $count);
    }
    return Error($arr[2], $sql);
}function LikeScreen(&$sql, $label, $value) {
    if ($value != null) {
        $sql .= " AND $label LIKE :like_value";
    }
}function StringEqualScreen(&$sql, $label, $value) {
    $temp = $value != null ? "'$value'" : 'NULL';
    EqualScreen($sql, $label, $temp);
}function EqualScreen(&$sql, $label, $value) {
    if ($value != null) {
        $sql .= " AND $label = $value";
    }
}function SelectSql($sql) {
    $DbContext = ConnDB();
    $stmt = $DbContext->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}