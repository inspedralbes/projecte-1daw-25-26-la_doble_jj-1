<style>
    body {
        background-color: #fcf8f8;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    h1 { 
         color: #06bbf1; 
         margin-bottom: 2rem; 
         font-size: 1.8rem; 
         letter-spacing: 1px;     
        }

    .grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; gap: 1.2rem; width: 100%; max-width: 600px; padding: 0 1rem; 
    }

    .bloc {
        background-color: #bebaba; 
        border: 2px solid #494c4d; 
        border-radius: 8px;
        padding: 2rem 1rem; 
        text-align: center; 
        text-decoration: none;
        color: #141311; 
        font-weight: bold; 
        font-size: 1rem;
        transition: background 0.2s, color 0.2s, border-color 0.2s;
        display: flex; flex-direction: column; align-items: center; gap: 0.8rem;
    }

    .bloc i { 
        font-size: 2rem; 
        color: #1a1414; 
    }

    .bloc:hover { 
        background-color: #04eff7; 
        border-color: #080808; 
        color: #1a1414; 
    }

    .bloc:hover i { 
        color: #1a1414; 
        }
</style>

<?php include 'header.php'; ?>

<h1>Gestió d'Incidències</h1>

<div class="grid">
    <a href="incidencias.php" class="bloc">
        <i class="bi bi-plus-circle"></i>Nova Incidència
    </a>
    <a href="consultar.php" class="bloc">
        <i class="bi bi-search"></i>Consultar Estat
    </a>
    <a href="administrador.php" class="bloc">
        <i class="bi bi-gear"></i>Administrador
    </a>
    <a href="tecnic.php" class="bloc">
        <i class="bi bi-tools"></i>Tècnic
    </a>
</div>

<?php include 'footer.php'; ?>