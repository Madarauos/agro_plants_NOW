<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agro Plants NOW</title>
    <link rel="stylesheet" href="../PUBLIC/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

<body>
    <main class="jp_main-content">

        <section class="ym_section">
            <div class="ym_area-table">

                <table class="ym_tabela">

                    <thead class="ym_thead">
                        <tr class="ym_tr">
                            <th class="ym_th" style="color:white;">Código</th>
                            <th class="ym_th" style="color:white;">Porcentagem</th>
                            <th class="ym_th" style="color:white;">Data</th>
                            
                            <th class="ym_th" style="color:white;">Estado</th>
                            <th class="ym_th" id="ym_th-icon"></th>
                            
                            
                    </thead>

                    <tbody class="ym_tbody">

                        <?php
                            echo'
                            <tr class="ym_tr">
                                <td class="ym_td">#paulorojas100</td>
                                <td class="ym_td sb_td">100%</td>
                                <td class="ym_td">16/08</td>
                                <td class="ym_td">Ativo</td>
                                <td class="ym_td" id="ym_td-icon"><a href=""><i class="fa-solid fa-circle-info"></i></a></td>
                            </tr>
                            ';
                        ?>

                    </tbody>
                </table>

                <a class="ym_mobile-td">
                    <i class="fa-solid fa-circle-info"></i>
                </a>
        
            </div>
        </section> 
    
    

    </main>
</body>
</html>