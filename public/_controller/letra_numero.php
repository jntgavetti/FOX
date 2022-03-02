<?php 
function troca_letra_numero($numero){
    switch($numero){
        case 0:
            $numero = 'A';
        break;

        case 1:
            $numero = 'B';
        break;

        case 2:
            $numero = 'C';
        break;

        case 3:
            $numero = 'D';
        break;

        case 4:
            $numero = 'E';
        break;
        
        case 5:
            $numero = 'F';
        break;
        
        case 6:
            $numero = 'G';
        break;

        case 7:
            $numero = 'H';
        break;

        case 8:
            $numero = 'I';
        break;

        case 9:
            $numero = 'J';
        break;

        case 10:
            $numero = 'K';
        break;

        case 11:
            $numero = 'L';
        break;

        case 12:
            $numero = 'M';
        break;

        case 13:
            $numero = 'N';
        break;

        case 14:
            $numero = 'O';
        break;

        case 15:
            $numero = 'P';
        break;

        case 16:
            $numero = 'a';
        break;

        case 17:
            $numero = 'n';
        break;

        case 18:
            $numero = 'e';
        break;

        case 19:
            $numero = 'p';
        break;
        
        case 20:
            $numero = 't';
        break;

        case 21:
            $numero = 'f';
        break;

        case 22:
            $numero = 'm';
        break;

        case 23:
            $numero = 's';
        break;

        default:
            $numero = '-';
    }
    return $numero;
}
if(isset($_POST['numero'])){
    $numero = $_POST['numero'];
    echo troca_letra_numero($numero);
}
?>