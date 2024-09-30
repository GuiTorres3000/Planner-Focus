<?php
    trait Model{

        // Metodo chamado quando o objeto JSON é serializado (convertido em JSON)
        public function jsonSerialize(): mixed
        {
            $data =[];
            foreach($this as $prop => $value){
                $data[$prop] = $value;
            }
            return $data;
        }

        public function __get($var){

        // Verifica se já existe um metodo com get, caso não, daí cria um
            // ucfirst converte o primeiro caractere em maiusculo e o resto em minusculo
            $method = 'get' . ucfirst($var);
            if (method_exists($this, $method)){
                return $this->{$method}();
            }else{
                return $this->{$var};
            }
        }

        public function __set($var, $param){

        // Verifica se já existe um metodo com set, caso não, daí cria um
            $method = 'set' . ucfirst($var);
            if (method_exists($this, $method)){
                return $this->{$method}();
            }else{
                $this->{$var} = $param;
            }
        }

        public function __call($method, $args){

            // = - Atribuindo, == - false, === - verdadeiro
            if (strpos($method, 'set')===0){
                // Left Trim remove espaços em branco a direita de uma string, nesse caso remove set
                $method = ltrim($method, 'set');
                $method = lcfirst($method);
                if (property_exists($this, $method)){
                    // Pega somente o primeiro argumento, pois set só precisa de um arg
                    $this->{$method} = $args[0];
                    return ;
                }
            } else if (strpos($method, 'get')===0){
                $method = ltrim($method, 'get');
                $method = lcfirst($method);
                if (property_exists($this, $method)){
                    return $this->{$method};
                
                }
            }
        }


    
    }
?>