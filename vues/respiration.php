<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$inspiration = isset($_GET['inspiration']) ? intval($_GET['inspiration']) : 4;
$apnee = isset($_GET['apnee']) ? intval($_GET['apnee']) : 0;
$expiration = isset($_GET['expiration']) ? intval($_GET['expiration']) : 6;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Exercice de Respiration</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f4f8;
      margin: 0;
    }

    main {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 60px 20px 40px;
    }

    h1 {
      margin-bottom: 10px;
      color: #2b2d42;
    }

    #chrono {
      font-size: 1.3em;
      margin-bottom: 20px;
      color: #444;
    }

    #instruction {
      font-size: 2em;
      font-weight: bold;
      margin: 20px 0;
      color: #1d3557;
      height: 40px;
    }

    /* Ajout d'une marge en haut du cercle pour le descendre */
    .circle {
      width: 150px;
      height: 150px;
      background-color: #457b9d;
      border-radius: 50%;
      transition: transform ease-in-out;
      margin-bottom: 30px;
      margin-top: 38px; /* Ajout de 1cm (environ 38px) */
    }

    /* Ajout d'une marge en haut des contrôles pour les descendre */
    .controls {
      margin-top: 38px; /* Ajout de 1cm (environ 38px) */
      display: flex;
      gap: 20px;
    }

    button {
      padding: 10px 25px;
      background-color: #1d3557;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1em;
      cursor: pointer;
    }

    button:hover {
      background-color: #2a9d8f;
    }

    button:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }
  </style>
</head>
<body>

  <?php include("header.php"); ?>

  <main>
    <h1>Exercice de Respiration</h1>
    <div id="chrono">00:00</div>
    <div id="instruction">Appuyez sur démarrer</div>
    <div class="circle" id="circle"></div>

    <div class="controls">
      <button id="start-btn">Démarrer</button>
      <button id="pause-btn" disabled>Pause</button>
      <button id="reset-btn" disabled>Réinitialiser</button>
    </div>
  </main>

  <?php include("composants/footer.php"); ?>

  <script>
    const inspirationTime = <?= $inspiration ?>;
    const apneeTime = <?= $apnee ?>;
    const expirationTime = <?= $expiration ?>;

    const circle = document.getElementById('circle');
    const instruction = document.getElementById('instruction');
    const chrono = document.getElementById('chrono');
    const startBtn = document.getElementById('start-btn');
    const pauseBtn = document.getElementById('pause-btn');
    const resetBtn = document.getElementById('reset-btn');

    let startTime;
    let timerInterval;
    let phaseTimeout;
    let phase = 0;
    let paused = false;
    let elapsedTime = 0;

    function updateChrono() {
      const now = new Date();
      const diff = Math.floor((now - startTime + elapsedTime) / 1000);
      const min = String(Math.floor(diff / 60)).padStart(2, '0');
      const sec = String(diff % 60).padStart(2, '0');
      chrono.textContent = `${min}:${sec}`;
    }

    function clearTimers() {
      clearInterval(timerInterval);
      clearTimeout(phaseTimeout);
    }

    function animateBreathing() {
      if (paused) return;

      const durations = [inspirationTime, apneeTime, expirationTime];
      const instructions = ['Inspirez', 'Apnée', 'Expirez'];
      const transforms = ['scale(1.5)', 'scale(1.5)', 'scale(1)'];

      const currentDuration = durations[phase % 3] * 1000;

      instruction.textContent = instructions[phase % 3];
      circle.style.transition = `transform ${durations[phase % 3]}s ease-in-out`;
      circle.style.transform = transforms[phase % 3];

      phaseTimeout = setTimeout(() => {
        phase++;
        animateBreathing();
      }, currentDuration);
    }

    startBtn.addEventListener('click', () => {
      startBtn.disabled = true;
      pauseBtn.disabled = false;
      resetBtn.disabled = false;
      paused = false;
      startTime = new Date();
      timerInterval = setInterval(updateChrono, 1000);
      animateBreathing();
    });

    pauseBtn.addEventListener('click', () => {
      paused = true;
      clearTimers();
      elapsedTime += new Date() - startTime;
      startBtn.disabled = false;
      pauseBtn.disabled = true;
      instruction.textContent = 'En pause';
    });

    resetBtn.addEventListener('click', () => {
      clearTimers();
      elapsedTime = 0;
      phase = 0;
      paused = false;
      instruction.textContent = 'Appuyez sur démarrer';
      chrono.textContent = '00:00';
      circle.style.transform = 'scale(1)';
      startBtn.disabled = false;
      pauseBtn.disabled = true;
      resetBtn.disabled = true;
    });
  </script>

</body>
</html>