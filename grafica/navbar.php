<?php
	//estraggo le categorie principali da mostrare nella navbar
	$categorie = estraiCategorie($link);
?>

<nav class="navbar navbar-expand-lg bg-white shadow p-3 bg-body-tertiary rounded">
	<div class="container-fluid">
		<a class="navbar-brand">Post Scriptum</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
			aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item">
					<a class="nav-link active" aria-current="page" href="index.php?p=1">Homepage</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
						data-bs-toggle="dropdown" aria-expanded="false">
						Categorie
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
						<?php foreach ($categorie as $i => $c) : ?>
						<li><a class="dropdown-item"
								href="categoria.php?nome_categoria=<?php echo $c['nome_categoria'] ?>"><?php echo $c['nome_categoria'] ?></a>
						</li>
						<?php endforeach?>
					</ul>
				</li>
				<!--Se l'utente ha effettuato l'accesso vede varie funzioni-->
				<?php if (isset($_SESSION['user'])) { ?>
				<li class="nav-item">
					<a class="nav-link" href="profilo.php">Profilo</a>
				</li>
				<?php if (isset($_SESSION['verificato'])) { ?>
					<?php if ($_SESSION['verificato']) { ?>
						<li class="nav-item">
							<a class="nav-link" href="listaBlog.php">I Tuoi Blog</a>
						</li>
					<?php } ?>
				<?php } ?>

				<li class="nav-item">
					<a class="nav-link" href="logout.php">Esci</a>
				</li>
				<!--altrimenti vede quello di login/registrazione-->
				<?php } else { ?>
				<li class="nav-item">
					<a class="nav-link" href="login.php">Accedi</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="registrazione.php">Registrati</a>
				</li>
				<?php } ?>
			</ul>

			<!--controlliamo che la variabile $_SESSION contenga la chiave "verificato"-->
			<?php if (isset($_SESSION['verificato'])) { ?>
				<!--se l'utente è verificato allora proseguo con i controlli-->
				<?php if ($_SESSION['verificato']) { ?>
					<!--se l'utente ha meno di 6 blog allora mostriamo il pulsante per creare un blog-->
					<?php if ($_SESSION['numero_blog'] < 6) { ?>
						<a href="creazioneBlog.php" type="button" class="btn btn-primary me-3">Crea Blog</a>
					<?php } ?>
				<?php } else { ?>
					<a href="profilo.php#verifica" type="button" class="btn btn-warning me-3">Verifica account</a>
				<?php } ?>
			<?php } ?>

			<form class="d-flex" action="cerca.php" method="get" role="search">
				<input class="form-control me-2" type="search" placeholder="Search" name="q" aria-label="Search">
				<button class="btn btn-outline-success" type="submit">Search</button>
			</form>
		</div>
	</div>
</nav>