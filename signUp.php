<?php
    //header della pagina
    include_once 'header.php';
?>
            <h1>Benvenuto su YouBlog!</h1>
            <h2></h2>

            <form action="insertData.php" method="post">
                Nome: <input type="text" name="nome"><br>
                E-mail: <input type="text" name="email"><br>
                Password: <input type="text" name="password"><br>
                Telefono <input type="text" name="telefono"><br>
                <input type="submit">
                <p id="message"></p>
            </form>
<?php
    //footer della pagina
    include_once 'footer.php';
?>