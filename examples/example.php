<?php

require_once(__DIR__.'/../src/Kogatana.php');

try {
    $db = new PDO("sqlite::memory:");
    if (!$db)  throw new \Exception('db connection failed');

    // create users
    $sql = "CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        sex TEXT,
        age INTEGER
    )";
    $db->query($sql);

    // create songs
    $sql = "CREATE TABLE songs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        title TEXT,
        category TEXT
    )";
    $db->query($sql);

    // insert users
    $db->query("INSERT INTO users (name, sex, age) VALUES ('hoge', 'man', 20)");
    $db->query("INSERT INTO users (name, sex, age) VALUES ('fuge', 'woman', 25)");
    $db->query("INSERT INTO users (name, sex, age) VALUES ('piyo', 'woman', 30)");

    // insert songs
    $db->query("INSERT INTO songs (user_id, title, category) VALUES (1, 'song1', 'rock')");
    $db->query("INSERT INTO songs (user_id, title, category) VALUES (1, 'song2', 'pop')");
    $db->query("INSERT INTO songs (user_id, title, category) VALUES (1, 'song3', 'punk')");
        

    $query = \Kogatana\Query::table('users')->eq('name', 'hoge')->join('songs', array('users.id', '=', 'songs.user_id'));
    list($sql, $binds) = $query->to_sql();

    $statement = $db->prepare($sql);
    $statement->execute($binds);
    $res = $statement->fetchAll(PDO::FETCH_ASSOC);
    var_dump($res);
    
} catch (\PDOException $e) {
    var_dump($e->getMessage());
    exit(1);
} catch (\Exception $e) {
    var_dump($e->getMessage());
    exit(1);
}
