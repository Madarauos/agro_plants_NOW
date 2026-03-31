<div class="ym_area-alertas"></div>
<script src="../../PUBLIC/JS/script-alertas.js"></script>
<?php
    if(isset($_SESSION['alerta'])){
        echo($_SESSION['alerta']);
        unset($_SESSION['alerta']);
    }
?>