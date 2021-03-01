<?php
    
    // Criando uma categorai multi nivel
    /*
    |   Não é interesante fazer multiplas consultas para montar, até porque poderá dar problema no sistema
    |   
    |   ESTRUTURA BANCO
    |   id (INT) sub (INT) name (VACHAR)
    */

    try {
        // Definindo conexão ao banco de dados MySQL
        $db_host = 'localhost';
        $db_name = 'sistema';
        $db_user = 'root';
        $db_pass = '';

        // Criação da classe do PDO para consultas
        $pdo = new PDO("mysql:dbname=".$db_name.";host=".$db_host, $db_user, $db_pass);
    } catch ( PDOException $e ) {
        // Exibir mensagem relacionada a erro ao conectar ao banco
        die($e->getMessage());
    }

    $sql = $pdo->query("SELECT * FROM categorias ORDER BY sub DESC");

    // VERIFICAR SE NO ARRAY TEM ALGUMA COISA NA SUB
    function aindaPrecisa ( array $array) {

        foreach($array as $item) {
            // Se no array tiver sub e não for vazio
            if(!empty($item['sub'])){
                return true;
            }
        }
        // Se acabar o loop se não tiver mais o que organizar
        return false;
    }

    // Observe que estamos passando o $array para dentro da função
    function organizarCategoria(array &$array) {

        foreach( $array as $id => $item ) {
            
            // Verifica se no array principal tem a chave do item 'sub'
            
            if( isset($array[$item['sub']]) ) {
                
                // Se tiver quer dizer que ele tem um pai e irei jogar ele dentro dele
                $array[$item['sub']]['subs'][$item['id']] = $item;

                // Após isso deletamos do array o item que foi relocado
                unset($array[$id]);

                // Paramos o loop do foreach porque agora iremos entrar novamente no loop se ainda precisa
                break;
            }
        }
    }

    if($sql->rowCount() > 0) {
        $data = $sql->fetchAll(PDO::FETCH_ASSOC);
        
        // Esse loop só roda uma vez para organizar o array e colocar o campo ['subs']
        foreach( $data as $item ) {
            // Criação de array vazios para preenchimento
            $item['subs'] = array();

            // Organizar o array com chave 'ID'
            $array[$item['id']] = $item;
        }

        // Loop organizar se ainda precisar
        while( aindaPrecisa($array) ){
            // Então se no array o achar sub manda para organizar
            organizarCategoria($array);
        }
    }

    echo '<pre>';
    print_r($array);

    /* ANTES

        Array
            (
                [0] => Array
                    (
                        [id] => 3
                        [sub] => 2
                        [nome] => Descktop
                    )

                [1] => Array
                    (
                        [id] => 4
                        [sub] => 2
                        [nome] => Notebook
                    )

                [2] => Array
                    (
                        [id] => 2
                        [sub] => 1
                        [nome] => Computador
                    )

                [3] => Array
                    (
                        [id] => 1
                        [sub] => 
                        [nome] => Informatica
                    )

            )

    
        DEPOIS

        Array
        (
            [1] => Array
                (
                    [id] => 1
                    [sub] => 
                    [nome] => Informatica
                    [subs] => Array
                        (
                            [2] => Array
                                (
                                    [id] => 2
                                    [sub] => 1
                                    [nome] => Computador
                                    [subs] => Array
                                        (
                                            [3] => Array
                                                (
                                                    [id] => 3
                                                    [sub] => 2
                                                    [nome] => Descktop
                                                    [subs] => Array
                                                        (
                                                        )

                                                )

                                            [4] => Array
                                                (
                                                    [id] => 4
                                                    [sub] => 2
                                                    [nome] => Notebook
                                                    [subs] => Array
                                                        (
                                                        )

                                                )

                                        )

                                )

                        )

                )

        )
    */