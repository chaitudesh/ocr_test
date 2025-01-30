<style>
    .black {
        background-color: black;
        top: 2px;
        bottom: 2px;
        border-radius: 1px;
        position: absolute;
        ;
    }
</style>

<body>

    <table border="1">
        <tbody>
            <?php


            for ($i = 1; $i <= 8; $i++) {
                echo '<tr>';
                for ($j = 1; $j <= 8; $j++) {

                    if ($i % 2 != 0) {
                        if ($j % 2 == 0) {
                            echo "<td style='background-color:black !important'></td>";

                        } else {
                            echo "<td style='background-color:white !important'></td>";
                        }


                    } else {
                        if ($j % 2 != 0) {
                            echo "<td style='background-color:black !important'></td>";

                        } else {
                            echo "<td style='background-color:white !important'></td>";
                        }
                    }


                }
                echo '</tr>';
                // echo '<br>';
            }

            ?>
        </tbody>
    </table>
</body>