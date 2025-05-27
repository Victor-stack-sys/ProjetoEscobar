<?php

function getDb() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
         
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    return $pdo;
}


function query($sql, $params = []) {
    try {
        $pdo = getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
       
        die('Query error: ' . $e->getMessage());
    }
}


function queryOne($sql, $params = []) {
    try {
        $pdo = getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        
        die('Query error: ' . $e->getMessage());
    }
}


function execute($sql, $params = []) {
    try {
        $pdo = getDb();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        
        die('Execute error: ' . $e->getMessage());
    }
}


function createSchema() {
    try {
        $pdo = getDb();
        
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categorias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS produtos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(200) NOT NULL,
                preco DECIMAL(10,2) NOT NULL,
                descricao TEXT,
                imagem VARCHAR(255),
                categoria_id INT NOT NULL,
                FOREIGN KEY (categoria_id) REFERENCES categorias(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS vendas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                data_venda DATETIME NOT NULL,
                total DECIMAL(10,2) NOT NULL,
                cliente_nome VARCHAR(200),
                cliente_email VARCHAR(200),
                cliente_telefone VARCHAR(50),
                cliente_endereco TEXT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
       
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS vendas_itens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                venda_id INT NOT NULL,
                produto_id INT NOT NULL,
                quantidade INT NOT NULL,
                preco DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (venda_id) REFERENCES vendas(id),
                FOREIGN KEY (produto_id) REFERENCES produtos(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        
        $categoriesCount = queryOne("SELECT COUNT(*) as count FROM categorias")['count'];
        if ($categoriesCount == 0) {
            
            execute("INSERT INTO categorias (nome) VALUES (?)", ['Trufas']);
           
            
            
            $catTrufas = queryOne("SELECT id FROM categorias WHERE nome = 'Trufas'")['id'];
            $catTabletes = queryOne("SELECT id FROM categorias WHERE nome = 'Tabletes'")['id'];
            $catKits = queryOne("SELECT id FROM categorias WHERE nome = 'Kit e Presentes'")['id'];
            
            
            execute("INSERT INTO produtos (nome, preco, descricao, categoria_id) VALUES (?, ?, ?, ?)", 
                    ['Trufa de morango', 8.00, 'Trufa de morango', $catTrufas]);
            
       
        }
    } catch (PDOException $e) {
        die('Schema creation error: ' . $e->getMessage());
    }
}


createSchema();